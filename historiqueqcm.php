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
            header('Location: Error404.html'); // Rediriger vers la page de connexion
            exit;
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} else {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header('Location:Error404.html');
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
       
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        tr:nth-child(1):hover th {
            cursor: default;
            color: #000;
        }
        a {
            color: #D1000A;
            cursor:pointer;

        }
        
        a:hover{
            color: black;
        

        }
        #qcmAperçu{
  border: 1px solid #ccc;
  padding: 10px;
  margin-top: 10px;
}
    </style>
</head>
<body>
<div class="demo-page">
    <div class="demo-page-navigation">
        <nav>
            <br>
            <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <img src="images/logo.png" alt="Logo du site" class="logo"><br><br>
            <ul>
                <li><a href="Acc.php"><svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19c0 .6.4 1 1 1h3v-3c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v3h3c.6 0 1-.4 1-1v-8.5"/>
                </svg>Accueil</a></li><hr>
                <li>
                    <a href="#historique">
                        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/>
                        </svg>Historique</a>
                </li>
            </ul>
        </nav>
    </div>
    <main class="demo-page-content">
        <section class="intro1">
            <div class="href-target" id="intro"></div>
            <h1 class="package-name"><strong id="s3">Historique des QCM</strong></h1>
            <h2><strong id="s2">Suivez les étapes suivantes pour récupérer les QCM déjà créés sur notre site pour une autre utilisation :</strong></h2><br>
            <ul>
                <li>Choisissez le QCM que vous voulez récupérer parmi ceux que vous avez créés dans le tableau.</li>
                <li>Modifiez le niveau auquel vous voulez disposer le QCM.</li>
                <li>Téléchargez le QCM pour une nouvelle utilisation.</li><br>
            </ul>
            <p>Notre système d'historique conserve tous les QCM créés, permettant aux enseignants de les réutiliser ultérieurement.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
            <strong id="s1">NB:</strong>
            <p>Vous pouvez revenir à tout moment pour modifier les informations en utilisant les ancres situées à gauche.</p>
        </section>

        <section>
            <h1><svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/>
            </svg> Historique de vos QCM:</h1>
            <table id="qcmTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Matière</th>
                        <th>Niveau</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($qcmProfesseur as $qcm): ?>
                        <tr>
                            <td><?php echo $qcm['date']; ?></td>
                            <td><?php echo $qcm['nom_matiere']; ?></td>
                            <td><?php echo $qcm['niveau']; ?></td>
                            <td><?php echo extractTypeQCM($qcm['contenu']); ?></td>
                            <td>
                                <a class="select-btn" data-qcm='<?php echo json_encode($qcm); ?>'>Sélectionner</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        

        <br><br>
            <h2>Aperçu du QCM sélectionné :</h2>
            <div id="qcmAperçu"><span style="color:gray">Ici s'affichera l'Aperçu du QCM, après la sélection dans la table historique des Qcm :</span></div>
            
            <form id="qcmForm" class="nice-form-group" method="post" action="traitementhistorique.php">
    <input type="hidden" id="qcmData" name="qcmData"> <!-- Champ caché pour stocker les données JSON -->
    <button type="submit"class="validerButton">Télécharger QCM</button> 
</form>
        </section>

        <footer>© 2024 Quick qcm - Tous droits réservés</footer>
    </main>
</div>

<!-- JavaScript pour rendre les lignes du tableau cliquables -->
<script>

    window.addEventListener('unload', function (e) {
    // Envoyer une requête AJAX au serveur pour déconnecter l'utilisateur
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'logout.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send();
});

$(document).ready(function() {
    $('.select-btn').on('click', function() {
        var qcmData = $(this).data('qcm');
        var contenu = JSON.parse(qcmData.contenu);

        var apercuHtml = `<br><u><h1 id="countainertr"><strong id="colortr">Type du QCM:</strong> ${contenu.type_qcm}</h1></u><br><br>
            <table>
                <tr>
                    <th>École:</th>
                    <th>Nom du Professeur:</th>
                    <th>Niveau:</th>
                    <th>Matière:</th>
                </tr>
                <tbody>
                    <tr>
                        <td>${contenu.ecole}</td>
                        <td>${contenu.prof}</td>
                        <td>${contenu.niveau}</td>
                        <td>${contenu.matiere}</td>
                    </tr>
                </tbody>
                <tr>
                    <th>Date du QCM:</th>
                    <th>Durée (en heures):</th>
                    <th>Calculatrice autorisée:</th>
                    <th>Documents autorisés:</th>
                </tr>
                <tbody>
                    <tr>
                        <td>${contenu.date}</td>
                        <td>${contenu.duree}</td>
                        <td>${contenu.calculatrice}</td>
                        <td>${contenu.documents}</td>
                    </tr>
                </tbody>
                <tr>
                    <th colspan="2">Autres Consignes:</th>
                    <th>Points pour une réponse correcte:</th>
                    <th>Points pour une réponse incorrecte:</th>
                </tr>
                <tbody>
                    <tr>
                        <td colspan="2">${contenu.autreconsignes}</td>
                        <td>${contenu.notation[0]}</td>
                        <td>${contenu.notation[1]}</td>
                    </tr>
                </tbody>
            </table>
            <br><br><u><h1><strong>Questions:</strong></h1></u>
        `;

        contenu.questions.forEach((question, index) => {
    apercuHtml += `
        <div>
            <ul><strong id="colortr2">Question ${index + 1}:</strong> ${question.texte}</ul>
            <li><strong>Réponse A:</strong> ${question.options[0]}</li>
            <li><strong>Réponse B:</strong> ${question.options[1]}</li>
            <li><strong>Réponse C:</strong> ${question.options[2]}</li>
            <li><strong>Réponse D:</strong> ${question.options[3]}</li>
            <p><strong id="colorrpco">Réponse correcte:</strong> ${question.reponse}</p><hr>
        </div>
    `;
});

        $('#qcmAperçu').html(apercuHtml);
    });

    $('#qcmTable tr').click(function() {
        $(this).addClass('selected-row').siblings().removeClass('selected-row');
    });
});

$(document).ready(function() {
        $('.select-btn').on('click', function() {
            var qcmData = $(this).data('qcm');
            $('#qcmData').val(JSON.stringify(qcmData)); // Mettre à jour la valeur du champ caché
            // Le reste de votre code
        });
    });


</script>
</body>
</html>
