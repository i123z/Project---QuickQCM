<?php
session_start(); // Démarrer la session

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
            if (isset($_POST['id_qcm'])) {
                $id_qcm = htmlspecialchars($_POST['id_qcm']);
                
                // Récupérer le nom du fichier PDF correspondant à l'ID du QCM
                $recup_Nom_Qcm = $bdd->prepare('SELECT nom_fichier FROM qcm WHERE id_qcm = ? ');
                $recup_Nom_Qcm->execute(array($id_qcm));
                $qcm_Nom = $recup_Nom_Qcm->fetch(PDO::FETCH_ASSOC);

                // Chemin du dossier de téléversement
                $uploadDir = 'latexQCM/';

                // Vérification du téléversement de fichier
                if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                    // Chemin du fichier temporaire téléversé
                    $tmpFile = $_FILES['pdf_file']['tmp_name'];

                    // Nouveaux chemins pour les fichiers PDF et JSON
                    $newFileNamePDF =  $uploadDir.$qcm_Nom['nom_fichier'];
                    $newFileNameJSON =  $qcm_Nom['nom_fichier'];

                    // Renommer et déplacer le fichier PDF téléversé
                    if (move_uploaded_file($tmpFile, $newFileNamePDF.".pdf")) {
                        // Exécuter le script Python avec les chemins des fichiers PDF et JSON
                        shell_exec('py latexQCM\\Correction_QCM.py '.$newFileNameJSON.'.pdf' .' ' .$newFileNameJSON.'.json');
                        
                        echo 
                        '<div id="resultsSection"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                fetchExcelResults("'.$newFileNameJSON.'.xlsx");
                            });

                            function fetchExcelResults(fileName) {
                                fetch("resultats.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({ file: fileName })
                                })
                                .then(response => response.text())
                                .then(data => {
                                    document.getElementById("resultsSection").innerHTML = data;
                                })
                                .catch(error => {
                                    console.error("Erreur:", error);
                                    document.getElementById("resultsSection").innerHTML = "<p>Erreur lors de la récupération des résultats.</p>";
                                });
                            }
                        </script>'
                    ;
                    } else {
                        echo "Erreur lors du téléversement du fichier.";
                    }
                } else {
                    echo "Aucun fichier PDF téléversé ou erreur lors du téléversement.";
                }
            } else {
                echo "Aucun ID de QCM fourni.";
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
