<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['email']) && isset($_SESSION['mdp'])) {
    try {
        // Connectez-vous à la base de données
        $bdd = new PDO("mysql:host=localhost:3308;dbname=projetqcm;charset=utf8mb4", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer l'email de l'utilisateur connecté
        $email = $_SESSION['email'];

        // Récupérer les données du formulaire
        $niveau = isset($_POST['niveau']) ? $_POST['niveau'] : null;
        $matiere = isset($_POST['matiere']) ? $_POST['matiere'] : null;
        $date_qcm = isset($_POST['date_qcm']) ? $_POST['date_qcm'] : null;
        $id_qcm = isset($_POST['id_qcm']) ? $_POST['id_qcm'] : null; // Ajout de la récupération de l'ID du QCM

        // Préparer la requête pour rechercher les QCM correspondants
        $requete = "SELECT qcm.*, matiere.nom_matiere 
                    FROM qcm 
                    INNER JOIN matiere ON qcm.id_matiere = matiere.id_matiere 
                    WHERE matiere.id_prof = ?";
        $params = array($_SESSION['id']); // Ajouter l'ID du professeur connecté

        // Ajouter les critères à la requête en fonction des valeurs fournies dans le formulaire
        if ($niveau) {
            $requete .= " AND qcm.niveau = ?";
            $params[] = $niveau;
        }
        if ($matiere) {
            $requete .= " AND matiere.nom_matiere = ?";
            $params[] = $matiere;
        }
        if ($date_qcm) {
            $requete .= " AND qcm.date = ?";
            $params[] = $date_qcm;
        }
        if ($id_qcm) {
            $requete .= " AND qcm.id_qcm = ?";
            $params[] = $id_qcm;
        }

        // Préparer et exécuter la requête avec les paramètres
        $stmt = $bdd->prepare($requete);
        $stmt->execute($params);

        // Récupérer les résultats
        $qcmCorrespondants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les QCM correspondants
        if (count($qcmCorrespondants) > 0) {
            echo "<h2>Résultats de la recherche :</h2>";
            foreach ($qcmCorrespondants as $qcm) {
                // Afficher les informations du QCM
                echo '<label for="option' . $qcm['id_qcm'] . '">';
                echo '<input type="radio" name="selected_qcm" value="'.$qcm['nom_fichier'].'"> ' . $qcm['niveau'] . "-" . $qcm['nom_matiere'] . "-" . $qcm['date'];
                echo '</label><br>';
            }
        } else {
            echo "Aucun QCM correspondant trouvé.";
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
} else {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header('Location: login.php');
    exit;
}
?>