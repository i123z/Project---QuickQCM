<?php
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
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Qcm</title>
    <link rel="icon" href="images/logo2.png" type="image/png">
    <link rel="stylesheet" href="Acc.css">
   
    <style>
       .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 400px; 
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    header{
        width: 100%; /* Largeur pleine */
        position: fixed; /* Position fixe */
        background-color: black;
        text-align: center;
        align-items: center;
        z-index: 1000; /* Profondeur z pour assurer la superposition */
    }
    main {
  margin-top: 60px; /* Marge supérieure pour le contenu principal, égale à la hauteur de l'en-tête */
}
    #acceuil h1{
            font-size: 40px;
        }

    .text{
        font-size: 17px;
        
    }

    #features {
        background-color:  #f3e6cae1;
    }
    #about{
        background-color: #d1000ade;
        color: white;
    }
    
    footer{
            background-color: black;
            color: white;
        }

    #junia:hover{
            color: white;cursor: pointer;
        }

    #junia:active{
            color:#D1000A;cursor: pointer;
        }

    .style{
            color: white;
        }

    .but{
        background-color: #D1000A;
        color: white;
        }
    .but:hover{
            background-color:#a9030b;
            color: white;
        }
    .but:active{
            color: white;
        }
    .btn:active{
            background-color: black;
        }
    a:hover{
            color: #D1000A;
        } 
    .bouton {
        background-color: black;
        color:white
    }   
    .bouton:hover{
        background-color:#D1000A;
        color:white
    }
    .bouton:active{
        background-color: black;
    }
    .bouton2 {
        background-color:#D1000A;
        color:white
    }   
    .bouton2:hover{
        background-color:black;
        color:white
    }
    .bouton2:active{
        background-color:#D1000A;
    }


        /* Style pour la photo de profil */
        .profile-picture {
            width: 53px; /* Largeur de l'image */
            height: 53px; /* Hauteur de l'image */
            border-radius: 50%; /* Coins arrondis pour un aspect circulaire */
            margin-right: 15px; /* Marge à droite de l'image */
            cursor:pointer; /* Curseur pointeur au survol */
        }

        .profile-picture:hover{ border-style:solid;border-color:#D1000A}
    </style>

</head>
<body>
<header>
    
        <nav class="navbar navbar-expand-lg ">
            
                <img alt="logo du site" class="img-fluid" height="" src="images/logo.png" width="150"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <div class="collapse navbar-collapse my-2 my-lg-0" id="navbarSupportedContent3">
                    <ul class="navbar-nav  ms-lg">
                        <li class="nav-item ms-lg-4">
                            <a class="nav-link" href="#features"><label class="text">Résultats</label></a>
                        </li>
                        <li class="nav-item ms-lg-4">
                            <a class="nav-link" href="historiqueqcm.php"><label class="text">Historique</label></a>
                        </li>
                        
                        <li class="nav-item ms-lg-4">
                            <a class="nav-link" href="#support"><label class="text">Contact</label></a>
                        </li>
                        <li class="nav-item ms-lg-4">
                            <a class="nav-link" href="#support"><label class="text">Déconnexion</label></a>
                        </li><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <li class="nav-item ms-lgg">
                        <a href="profil.php"><img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="photo de profil" class="profile-picture"></a>
                        </li>
                    </ul>
                </div>
          
        </nav>
    
    </header>




<div id="acceuil">
    <section class="py_ bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0"><br>
                    <h1 class=" fw-bold"><span style="font-size: 35px;">Bonjour <span class="text-primary"><?php echo $userData['Nom']; ?></span>,</span><br><br><br>bienvenue sur la plateforme<br>de création & correction de QCM</h1>
                    <p class="lead my-4">Commencez dès maintenant en choisissant l'une des options ci-dessous :</p><a class="btn btn-lg bouton" href="Creation.php">Créer un QCM</a><label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label><a class="btn btn-lg bouton2" href="Correction.php">Corriger un QCM</a>
                </div>
                <div class="col-lg-6"><img alt="" class="img-fluid" src="images/prof.png"></div>
            </div>
            <div class="row mt-5">
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="mb-3"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none"viewBox="0 0 24 24" size="80px">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                          </svg>
                    </div>
                    <h6>Creation & correction rapide</h6>
                    
                </div>
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="mb-3"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v5m-3 0h6M4 11h16M5 15h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1Z"/>
                          </svg>
                    </div>
                    <h6>Evaluation en ligne</h6>
                   
                </div>
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="mb-3"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 7 2 2 4-4m-5-9v4h4V3h-4Z"/>
                          </svg>
                    </div>
                    <h6>Gestion de notes</h6>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="mb-3"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/>
                          </svg>
                    </div>
                    <h6>Enregistrement des épreuves</h6>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="features">
        <section class="py-3">
            <div class="container">
                <div class="row justify-content-center text-center mt-5">
                    <div class="col-md-8">
                        
                        <h2 class="fw-bold display-5">Explorer aussi :</h2>
                        <p class="lead mb-4">
                            Découvrez d'autres possibilités et options sur notre plateforme.</p><br><br><br>
                    </div>
                    <div class="col-lg-8">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <div class=" features border rounded-3 p-5">
                                    <h3 class="fw-bold mb-0"><span style="color:#D1000A">Résultats QCM</span></h3>
                                    <p class="my-4 lead">Consultez les résultats des étudiants aprés correction des QCM pour les gérer.</p><a class="btn bouton" href="">Parcourir</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="features border rounded-3 p-5">
                                    <h3 class="fw-bold mb-0"><span style="color:#D1000A">Historique des QCM</span></h3>
                                    <p class="my-4 lead">Consultez vos brouillons et QCM déjà réalisés pour les modifier et les réutiliser.</p><a class="btn bouton" href="">Parcourir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br><br><br><br><br><br><br>
            </div>
        </section>
</div>


<div id="support">
    <section class="py-5">
        <div class="container"><br><br><br><br>
            <div class="row">
                
               <div class="col-md-6">
                    <div class="d-sm-flex mt-md-0 mt-4">
                        <div class="me-4">
                            <svg class="bi bi-headset" fill="currentColor" height="96" viewbox="0 0 16 16" width="96" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z"></path></svg>
                        </div>
                        <div class="pe-lg-3">
                            <h2 class="display-5 fw-semibold pb-2">Support</h2>
                            <p class="lead">N'hésitez pas à nous contacter si vous rencontrez des problèmes avec la plateforme, ou si vous avez des messages à nous transmettre. Votre retour est précieux pour nous aider à améliorer notre service et à répondre au mieux à vos besoins.<br>&nbsp;</p><br><a class="btn bouton" href="contact_us.html">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-sm-flex">
                        <div class="me-4">
                            <svg class="bi bi-box-arrow-right" fill="currentColor" height="96" viewbox="0 0 16 16" width="96" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" fill-rule="evenodd"></path>
                            <path d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" fill-rule="evenodd"></path></svg>
                        </div>
                        <div class="pe-lg-3">
                            <h2 class="display-5 fw-semibold pb-2">Déconnexion</h2>
                            <p class="lead">En vous déconnectant, vous assurez la sécurité et la confidentialité de votre compte. Cela vous permet de mettre fin à votre session en un clic, ce qui empêche tout accès non autorisé à vos informations, surtout si vous partagez votre appareil ou utilisez un ordinateur public.<br>&nbsp;</p>
                            <button id="dec"class="btn bouton" >Déconnexion</button>

                            <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close"></span>
            <p>Êtes-vous sûr de vouloir vous déconnecter ? Vous allez quitter la session définitivement.</p>
            <button id="confirm" class="btn btn-danger">Oui</button>
            <button style="background-color:white; color:black;" onmouseover="this.style.backgroundColor='black'; this.style.color='white';" onmouseout="this.style.backgroundColor='white'; this.style.color='black';"id="cancel" class="btn btn-secondary">Non</button>
        </div>
    </div>
                        </div>
                    </div>
                </div>
            </div><br><br><br><br><br><br>
        </div>
       
    </section>
    
</div>

<footer class="py-4">
    <div class="container">
        <div class="row pt-4 mb-4 mb-lg-5">
            <div class="col-12 col-lg-3 pe-lg-0 mb-4 mb-lg-0">
                <img alt="Free Frontend Logo" class="img-fluid mb-3" height="" src="images/logo.png" width="200">
                <p class="small text-muted mb-3">Inscrivez votre adresse e-mail si vous voulez recevoir les dernières mises à jour sur les fonctionnalités et les versions.</p>
                <div class="input-group">
                    <input class="form-control bg-light" placeholder="Adresse Email" type="text"> <button class="btn but py-2" type="button">Subscribe</button>
                </div>
            </div>
            <div class="col-12 col-lg-2"></div>
            <div class="col-6 col-lg-2">
                <ul class="list-unstyled">
                    <li class="nav-item">
                        <a class="style text-decoration-none" href="profil.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="style text-decoration-none" href="Acc.php">Acceuil</a>
                    </li>
                    <li class="nav-item">
                        <a class="style text-decoration-none" href="Creation.php">Creer Qcm</a>
                    </li>
                    <li class="style nav-item">
                        <a class="style text-decoration-none" href="Correction.php">Corriger Qcm</a>
                    </li>
                    
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <ul class="list-unstyled">
                    <li class="nav-item">
                        <a class="style text-decoration-none" href="#features">Resultats Qcm</a>
                    </li>
                    <li class="nav-item">
                        <a class="style text-decoration-none" href="#features">Historique Qcm</a>
                    </li>

                    <li class="nav-item">
                        <a class="style text-decoration-none" href="contact_us.html">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="style text-decoration-none" href="#support">Déconnexion</a>
                    </li>

                </ul>
            </div>
            <div class="col-12 col-lg-1"></div>
            <div class="col-12 col-lg-2 small mt-4 mt-lg-0">
                <ul class="list-unstyled">
                    <li class="nav-item">
                        <a class="style text-decoration-none" href=""><svg class="w-[17px] h-[17px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M13.135 6H15V3h-1.865a4.147 4.147 0 0 0-4.142 4.142V9H7v3h2v9.938h3V12h2.021l.592-3H12V6.591A.6.6 0 0 1 12.592 6h.543Z" clip-rule="evenodd"/>
                          </svg>
                          
                          
                          Facebook</a>
                    </li>
                    <li class="nav-item">
                        <a class="style text-decoration-none" href=""><svg class="w-[17px] h-[17px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12.51 8.796v1.697a3.738 3.738 0 0 1 3.288-1.684c3.455 0 4.202 2.16 4.202 4.97V19.5h-3.2v-5.072c0-1.21-.244-2.766-2.128-2.766-1.827 0-2.139 1.317-2.139 2.676V19.5h-3.19V8.796h3.168ZM7.2 6.106a1.61 1.61 0 0 1-.988 1.483 1.595 1.595 0 0 1-1.743-.348A1.607 1.607 0 0 1 5.6 4.5a1.601 1.601 0 0 1 1.6 1.606Z" clip-rule="evenodd"/>
                            <path d="M7.2 8.809H4V19.5h3.2V8.809Z"/>
                          </svg>
                          
                          Linkedin</a>
                    </li>
                    <li class="nav-item">
                        <a class="style text-decoration-none" href=""><svg class="w-[17px] h-[17px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 5.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.343 8.343 0 0 1-2.605.981A4.13 4.13 0 0 0 15.85 4a4.068 4.068 0 0 0-4.1 4.038c0 .31.035.618.105.919A11.705 11.705 0 0 1 3.4 4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 6.1 13.635a4.192 4.192 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 2 18.184 11.732 11.732 0 0 0 8.291 20 11.502 11.502 0 0 0 19.964 8.5c0-.177 0-.349-.012-.523A8.143 8.143 0 0 0 22 5.892Z" clip-rule="evenodd"/>
                          </svg>
                          
                          Twiter</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-top d-lg-none"></div>
        <div class="d-block d-lg-flex justify-content-between py-3 py-lg-2">
            <div class="small mb-2 mb-lg-0 text-muted">
                © 2024 Quick qcm - Tous droits réservés || Designed By :  <a id="junia"href="https://www.linkedin.com/in/walid-nassir-elhak-0a4870224/"><label> @Walid Nassir ElHak</label></a>
                -    <a id="junia"href="https://www.linkedin.com/in/ilias-zaki-b5841a226/"><label>@Ilias Zaki</label></a>    -    <a id="junia"href="https://www.linkedin.com/in/younes-kharbach-b2a772225/"><label>Younes Kharbach </label></a>
                
            </div>
            <div class="small">
                <a class=" d-block d-lg-inline text-muted ms-lg-2 mb-2 mb-lg-0" href="">Privacy Policy</a> <a class="d-block d-lg-inline text-muted ms-lg-2" href="">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script>
        document.getElementById('dec').addEventListener('click', function(event) {
            event.preventDefault(); 
            document.getElementById('myModal').style.display = 'block';
        });

        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'none';
        });

        document.getElementById('cancel').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'none';
        });

        document.getElementById('confirm').addEventListener('click', function() {
            fetch('logout.php')
                .then(response => {
                    if (response.ok) {
                        window.location.href = 'index.html';
                    } else {
                        alert('Erreur lors de la déconnexion. Veuillez réessayer.');
                    }
                });
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = 'none';
            }
        }
    </script>


</body>
</html>