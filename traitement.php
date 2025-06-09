<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $ecole = htmlspecialchars($_POST['ecole']);
    $prof = htmlspecialchars($_POST['professeur']);
    $niveau = htmlspecialchars($_POST['niveau']);
    $matiere = htmlspecialchars($_POST['matiere']);
    $type_qcm = htmlspecialchars($_POST['type_qcm']);
    $date_qcm = htmlspecialchars($_POST['date_qcm']);
    $duree = htmlspecialchars($_POST['duree_qcm']);
    $calculatrice = htmlspecialchars($_POST['calculatrice_autorisee']);
    $calculatrice = ($calculatrice == 'oui') ? 1 : 0;
    $documents = htmlspecialchars($_POST['documents_autorises']);
    $documents = ($documents == 'oui') ? 1 : 0;
    

    $autresConsignes = htmlspecialchars($_POST['autres_consignes']);
    $notation = array(intval($_POST['points_correcte']), intval($_POST['points_incorrecte']));
    $nomFichier = 'qcm_data' . date('d_m_y_i_s');
    $data['nomFichier'] = $nomFichier;
    $questions = [];
    if (isset($_POST["questions"])) {
        $questionsData = json_decode($_POST["questions"], true);
        foreach ($questionsData as $index => $questionElement) {
            // Options sous forme de tableau
            $options = [
                htmlspecialchars($questionElement['reponse_A']),
                htmlspecialchars($questionElement['reponse_B']),
                htmlspecialchars($questionElement['reponse_C']),
                htmlspecialchars($questionElement['reponse_D'])
            ];

            // Lettre de la réponse correcte
            $reponse_correcte = strtoupper($questionElement['reponse_correcte']);
            
            // Attribuer un indice en fonction de la lettre de la réponse correcte
            $indice_reponse_correcte = 0;
            if ($reponse_correcte === 'A') {
                $indice_reponse_correcte = '1';
            } elseif ($reponse_correcte === 'B') {
                $indice_reponse_correcte = '2';
            } elseif ($reponse_correcte === 'C') {
                $indice_reponse_correcte = '3';
            } elseif ($reponse_correcte === 'D') {
                $indice_reponse_correcte = '4';
            }

            $question = [
                'id' => $index + 1,
                'texte' => htmlspecialchars($questionElement['enonce']),
                'options' => $options,
                'reponse' => $indice_reponse_correcte
            ];

            $questions[] = $question;
        }
    }

    // Créer un tableau associatif pour les données du formulaire
    $data = array(
        
        'ecole' => $ecole,
        'prof' => $prof,
        'nomFichier' => $nomFichier,
        'niveau' => $niveau,
        'matiere' => $matiere,
        'type_qcm' => $type_qcm,
        'date' => $date_qcm,
        'duree' => $duree,
        'calculatrice' => $calculatrice,
        'documents' => $documents,
        'autreConsignes' => $autresConsignes,
        'notation' => $notation,
        'questions' => $questions
    );
    //////////////////////////////////////////
// Définir les variables manquantes
$chemin_json = 'latexQCM\\'; // Vous devez définir le contenu du QCM, mais cela dépend de la structure de votre base de données
$idProf = 1; // Remplacez 1 par l'ID du professeur connecté
$idMatiere = 1; // Remplacez 1 par l'ID de la matière associée au QCM
session_start(); // Démarrer la session

$matieresProfesseur = []; // Initialisation d'un tableau pour stocker les matières du professeur

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['email']) && isset($_SESSION['mdp'])) {
    try {
        // Connectez-vous à la base de données
        $bdd = new PDO("mysql:host=localhost:3308;dbname=projetqcm;charset=utf8mb4", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer l'email de l'utilisateur connecté
        $email = $_SESSION['email'];

        // Préparer la requête pour récupérer les informations de l'utilisateur
        $recupUser = $bdd->prepare('SELECT * FROM professeurs WHERE email = ?');
        $recupUser->execute(array($email));
        $userData = $recupUser->fetch(PDO::FETCH_ASSOC);

        // Vérifier si les données utilisateur ont été récupérées
        if ($userData) {
            $id_prof = $userData['id'];

            // Préparer la requête pour récupérer les matières du professeur
            $recupMatiere = $bdd->prepare('SELECT id_matiere FROM matiere WHERE nom_matiere = ?');
            $recupMatiere->execute(array($matiere));
            $matiereData = $recupMatiere->fetchAll(PDO::FETCH_ASSOC);


            // Stocker les matières dans un tableau
            foreach ($matiereData as $matiere) {
                $matieresProfesseur[] = $matiere['nom_matiere'];
            }
        } else {
            // L'utilisateur n'existe pas dans la base de données, déconnecter l'utilisateur
            session_destroy();
            header('Location: login.php'); // Rediriger vers la page de connexion
            exit;
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} else {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header('Location: login.php');
    exit;
}

    $json_data = json_encode($data, JSON_PRETTY_PRINT);

    // Préparer la requête SQL
    $requete = $bdd->prepare("
        INSERT INTO qcm (
            nom_fichier, id_prof, id_matiere, niveau, date, chemin_json, contenu
        ) VALUES (
            :nom_fichier, :id_prof, :id_matiere, :niveau, :date, :chemin_json, :contenu
        )
    ");

    // Exécuter la requête SQL avec les données du formulaire
    $requete->execute([
        ':nom_fichier' => $nomFichier,
        ':id_prof' => $id_prof,
        ':id_matiere' => $matiere['id_matiere'], // Ensure this matches the database column name
        ':niveau' => $niveau,
        ':date' => $date_qcm,
        ':chemin_json' => $chemin_json,
        ':contenu' => $json_data
    ]);
    
    //////////////////////////////////////////
    // Convertir le tableau associatif en JSON


    // Écrire le JSON dans un fichier
    $file_path = 'latexQCM\\'.$nomFichier.'.json';
    file_put_contents($file_path, $json_data);
    


    $variable_php = $nomFichier;


    file_put_contents("variable.txt", $variable_php);
    // Exécuter votre script Python pour générer le PDF
    shell_exec('py latexQCM\\Json_lecture.py '.$nomFichier.'.json');


    // Spécifier le chemin du fichier PDF généré par le script Python
    $cheminFichierPDF = 'latexQCM\\'.$nomFichier.'.pdf';

    // Envoyer les en-têtes HTTP pour forcer le téléchargement
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"Questionnaire ".$date_qcm."-".date('H-i').".pdf\"");
    readfile($cheminFichierPDF);

    exit; // Terminer le script après avoir renvoyé le fichier PDF
} else {
    echo 'Erreur : Accès invalide.';
}

?>
