
<?php
session_start(); // Démarrer la session

$matieresProfesseur = []; // Initialisation d'un tableau pour stocker les matières du professeur
$qcmProfesseur = []; // Initialisation d'un tableau pour stocker les QCM du professeur

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
            $recupMatiere = $bdd->prepare('SELECT DISTINCT matiere.nom_matiere 
                                FROM professeurs 
                                INNER JOIN matiere ON professeurs.id = matiere.id_prof
                                WHERE professeurs.id = ?');
            $recupMatiere->execute(array($id_prof));
            $matiereData = $recupMatiere->fetchAll(PDO::FETCH_ASSOC);

            // Stocker les matières dans un tableau
            foreach ($matiereData as $matiere) {
                $matieresProfesseur[] = $matiere['nom_matiere'];
            }

            // Préparer la requête pour récupérer les QCM du professeur
            $recupQcm = $bdd->prepare('SELECT qcm.id_qcm, qcm.date, qcm.niveau, matiere.nom_matiere, qcm.nom_fichier, qcm.contenu FROM qcm 
            INNER JOIN matiere ON qcm.id_matiere = matiere.id_matiere 
            WHERE qcm.id_prof = ?');

            $recupQcm->execute(array($id_prof));
            $qcmProfesseur = $recupQcm->fetchAll(PDO::FETCH_ASSOC);

            $recupTYPEQCM = $bdd->prepare('SELECT contenu FROM qcm');
            $recupTYPEQCM->execute();
            $qcmTYPE = $recupTYPEQCM->fetchAll(PDO::FETCH_ASSOC);



            function extractTypeQCM($contenu) {
                // Vous devez définir votre propre logique pour extraire le type de QCM en fonction du contenu
                // Voici un exemple de logique simplifié :
                if (strpos($contenu, "type_qcm") !== false) {
                    $data = json_decode($contenu, true);
                    return $data['type_qcm'];
                } else {
                    return "Type non spécifié";
                }
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Correction QCM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./stylecreation.css">
    <link rel="icon" href="images/logo2.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>


    .selected-row {
        background-color: #cce5ff; /* Changez la couleur de fond selon vos préférences */
    }


        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }



        tr:hover {
            background-color: #dddddd;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="demo-page">
    <div class="demo-page">
    <div class="demo-page-navigation">
      <nav><br><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <img src="images/logo.png" alt="Logo du site" class="logo"><br><br>
        <ul>
          <li><a href="Acc.php"><svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19c0 .6.4 1 1 1h3v-3c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v3h3c.6 0 1-.4 1-1v-8.5"/>
          </svg>Acceuil </a>
            </li>
        <hr>
        <li>
          <a href="#Identification">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-width="2" d="M7 17v1c0 .6.4 1 1 1h8c.6 0 1-.4 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>

            Identification</a>
        </li>
        <li>
          <a href="#Correction">
            <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z"/>
              </svg>
              
            Correction</a>
        </li>
        <li><a href="resultats.php"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9H5a1 1 0 0 0-1 1v4c0 .6.4 1 1 1h6m0-6v6m0-6 5.4-3.9A1 1 0 0 1 18 6v12.2a1 1 0 0 1-1.6.8L11 15m7 0a3 3 0 0 0 0-6M6 15h3v5H6v-5Z"/>
          </svg>
        Résultats</a>
        
        </li>
    </ul>
    </nav>
    </div>
    <main class="demo-page-content">


    <section class="intro1">
      <div class="href-target" id="intro"></div>
      <h1 class="package-name"><strong id="s3">Correction du QCM</strong></h1>
      <h2><strong id="s2">Suivez les étapes suivantes pour procéder à la correction automatique des QCM créés par notre site.</strong> 
      </h2><br>
      <ul>
        <li>Scannez les feuilles de réponse du QCM remplies par les étudiants.</li>
        <li>Remplissez les champs du formulaire d'identification.</li>
        <li>Téléversez les fichiers des pages de réponses scannées dans le champ désigné.</li>
      </ul><p>Notre système automatique de correction utilisera le traitement d'image pour analyser les réponses de chaque étudiant.<br><br>Les résultats seront ensuite affectés à chaque étudiant en fonction de l'identifiant unique du QCM dans l'onglet "Résultats Étudiants". </p>
      <strong id="s1">NB:</strong>
      <p>Vous pouvez revenir à tout moment pour modifier les informations en utilisant les ancres situées à gauche.</p>
    </section>

        <section>
            <h1>Correction</h1>
            <!-- Votre contenu ici -->

            <table>
    <thead>
        <tr>
            <th style = "display: none";>ID du QCM</th>
            <th>Date</th>
            <th>Matière</th>
            <th>Niveau</th>
            <th>Type de QCM</th> <!-- Ajoutez cette colonne -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($qcmProfesseur as $qcm) : ?>
            <tr class="qcm-row">
                <td style = "display: none";><?php echo htmlspecialchars($qcm['id_qcm']); ?></td>
                <td><?php echo htmlspecialchars($qcm['date']); ?></td>
                <td><?php echo htmlspecialchars($qcm['nom_matiere']); ?></td>
                <td><?php echo htmlspecialchars($qcm['niveau']); ?></td>
                <td><?php echo extractTypeQCM($qcm['contenu']); ?></td> <!-- Affichez le type de QCM ici -->
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
            <!-- Ajoutez cet élément pour afficher l'ID du QCM sélectionné -->
            <!--  <div style= "font-size:large;" id="selected_qcm_id"></div>
            <div style= "font-size:large;" id="selected_qcm_FileName"></div>-->

            <!-- Formulaire pour ajouter une question -->
            <form class="nice-form-group question-form" action="traitement_correction2.php" enctype="multipart/form-data"
                  method="post">
                <!-- Champ masqué pour l'ID du QCM -->
                <input type="hidden" id="id_qcm_hidden" name="id_qcm" value="">
                
                <label><br>Téléversez le fichier des pages de réponses scannées ici :</label>
                <input type="file" name="pdf_file" accept=".pdf" required>
                <label><br></label>
                <button type="submit" id="corr">Corriger les QCM</button>
            </form>

        </section>


       
        <footer>© 2024 Quick qcm - Tous droits réservés</footer>
    </main>
</div>

<!-- JavaScript pour rendre les lignes du tableau cliquables -->
<script>
    $(document).ready(function () {
        // Fonction pour gérer les clics sur les lignes du tableau
        $(".qcm-row").click(function () {
            // Récupérer l'ID du QCM de la ligne sur laquelle l'utilisateur a cliqué
            var qcmId = $(this).find("td:first").text(); // Suppose que le premier TD contient l'ID du QCM
            var qcmFileName = $(this).find("td:last").text(); // Suppose que le dernier TD contient le nom du fichier

            // Mettre à jour le contenu de l'élément HTML avec l'ID et le nom du fichier du QCM sélectionné
            $("#selected_qcm_id").text("ID du QCM sélectionné : " + qcmId);
            $("#selected_qcm_FileName").text("Nom du QCM sélectionné : " + qcmFileName);

            // Mettre à jour la valeur du champ masqué
            $("#id_qcm_hidden").val(qcmId);
        });
    });

// Fonction pour ajouter la classe à la ligne sélectionnée
function selectRow(event) {
        // Supprimer la classe de toutes les lignes
        var rows = document.querySelectorAll('.qcm-row');
        rows.forEach(function(row) {
            row.classList.remove('selected-row');
        });

        // Ajouter la classe à la ligne sélectionnée
        event.currentTarget.classList.add('selected-row');
    }

    // Ajouter un écouteur d'événement à toutes les lignes du tableau
    var rows = document.querySelectorAll('.qcm-row');
    rows.forEach(function(row) {
        row.addEventListener('click', selectRow);
    });



</script>




</body>
</html>
