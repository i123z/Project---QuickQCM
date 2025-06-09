<?php
session_start(); // Démarrer la session


// Affichage des messages
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success'] ."</div>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

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

            $recupnbrqcm = $bdd->prepare('SELECT COUNT(*) AS nombre_qcm FROM qcm WHERE id_prof = ?;');
            $recupnbrqcm->execute(array($id_prof));
            $nombreQCM = $recupnbrqcm->fetch(PDO::FETCH_ASSOC)['nombre_qcm'];
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
<html lang="en">
<head>
<meta charset="utf-8">


<title>Compte d'utilisateur</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="profilstyle.css" rel="stylesheet">
<link rel="icon" href="images/logo2.png" type="image/png">
<style type="text/css">
    	body{
    margin-top:20px;
    background-color: #f0e9da;
}
/* User Cards */
.user-box {
    width: 110px;
    margin: auto;
    margin-bottom: 20px;
    
}
h6{color: #D1000A;}
.user-box img {
    width: 100%;
    border-radius: 50%;
	padding: 3px;
	background: #fff;
	-webkit-box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.2);
    -ms-box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.2);
    box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.2);
}

.profile-card-2 .card {
	position:relative;
}

.profile-card-2 .card .card-body {
	z-index:1;
}

.profile-card-2 .card::before {
    content: "";
    position: absolute;
    top: 0px;
    right: 0px;
    left: 0px;
	border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    height: 112px;
    background-color: #f0e9da;
}

.profile-card-2 .card.profile-primary::before {
	background-color: #D1000A;
}
.profile-card-2 .card.profile-success::before {
	background-color: #15ca20;
}
.profile-card-2 .card.profile-danger::before {
	background-color: #fd3550;
}
.profile-card-2 .card.profile-warning::before {
	background-color: #ff9700;
}
.profile-card-2 .user-box {
	margin-top: 30px;
}

.profile-card-3 .user-fullimage {
	position:relative;
}

.profile-card-3 .user-fullimage .details{
	position: absolute;
    bottom: 0;
    left: 0px;
	width:100%;
}

.profile-card-4 .user-box {
    width: 110px;
    margin: auto;
    margin-bottom: 10px;
    margin-top: 15px;
}

.profile-card-4 .list-icon {
    display: table-cell;
    font-size: 30px;
    padding-right: 20px;
    vertical-align: middle;
    color: #223035;
}

.profile-card-4 .list-details {
	display: table-cell;
	vertical-align: middle;
	font-weight: 600;
    color: #223035;
    font-size: 15px;
    line-height: 15px;
}

.profile-card-4 .list-details small{
	display: table-cell;
	vertical-align: middle;
	font-size: 12px;
	font-weight: 400;
    color: #808080;
}

/*Nav Tabs & Pills */
.nav-tabs .nav-link {
    color: #223035;
	font-size: 12px;
    text-align: center;
	letter-spacing: 1px;
    font-weight: 600;
	margin: 2px;
    margin-bottom: 0;
	padding: 12px 20px;
    text-transform: uppercase;
    border: 1px solid transparent;
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
	
}
.nav-tabs .nav-link:hover{
    border: 1px solid transparent;
}
.nav-tabs .nav-link i {
    margin-right: 2px;
	font-weight: 600;
}

.top-icon.nav-tabs .nav-link i{
	margin: 0px;
	font-weight: 500;
	display: block;
    font-size: 20px;
    padding: 5px 0;
}

.nav-tabs-primary.nav-tabs{
	border-bottom: 1px solid #D1000A;
}

.nav-tabs-primary .nav-link.active, .nav-tabs-primary .nav-item.show>.nav-link {
    color: #D1000A;
    background-color: #fff;
    border-color:#D1000A #fff;
    
}

.nav-tabs-success.nav-tabs{
	border-bottom: 1px solid #15ca20;
}

.nav-tabs-success .nav-link.active, .nav-tabs-success .nav-item.show>.nav-link {
    color: #15ca20;
    background-color: #fff;
    border-color: #15ca20 #15ca20 #fff;
    border-top: 3px solid #15ca20;
}

.nav-tabs-info.nav-tabs{
	border-bottom: 1px solid #D1000A;
}

.nav-tabs-info .nav-link.active, .nav-tabs-info .nav-item.show>.nav-link {
    color: #D1000A;
    background-color: #fff;
    border-color: #D1000A #D1000A #fff;
    border-top: 3px solid #D1000A;
}

.nav-tabs-danger.nav-tabs{
	border-bottom: 1px solid #fd3550;
}

.nav-tabs-danger .nav-link.active, .nav-tabs-danger .nav-item.show>.nav-link {
    color: #fd3550;
    background-color: #fff;
    border-color: #fd3550 #fd3550 #fff;
    border-top: 3px solid #fd3550;
}

.nav-tabs-warning.nav-tabs{
	border-bottom: 1px solid #ff9700;
}

.nav-tabs-warning .nav-link.active, .nav-tabs-warning .nav-item.show>.nav-link {
    color: #ff9700;
    background-color: #fff;
    border-color: #ff9700 #ff9700 #fff;
    border-top: 3px solid #ff9700;
}

.nav-tabs-dark.nav-tabs{
	border-bottom: 1px solid #223035;
}

.nav-tabs-dark .nav-link.active, .nav-tabs-dark .nav-item.show>.nav-link {
    color: #223035;
    background-color: #fff;
    border-color: #223035 #223035 #fff;
    border-top: 3px solid #223035;
}

.nav-tabs-secondary.nav-tabs{
	border-bottom: 1px solid #75808a;
}
.nav-tabs-secondary .nav-link.active, .nav-tabs-secondary .nav-item.show>.nav-link {
    color: #75808a;
    background-color: #fff;
    border-color: #75808a #75808a #fff;
    border-top: 3px solid #75808a;
}

.tabs-vertical .nav-tabs .nav-link {
    color: #223035;
    font-size: 12px;
    text-align: center;
    letter-spacing: 1px;
    font-weight: 600;
    margin: 2px;
    margin-right: -1px;
    padding: 12px 1px;
    text-transform: uppercase;
    border: 1px solid transparent;
    border-radius: 0;
    border-top-left-radius: .25rem;
    border-bottom-left-radius: .25rem;
}

.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #dee2e6;
}

.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical .nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 1px solid #dee2e6;
    border-right: 0;
    border-left: 1px solid #dee2e6;
}

.tabs-vertical-primary.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #D1000A;
}

.tabs-vertical-primary.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-primary.tabs-vertical .nav-tabs .nav-link.active {
    color: #D1000A;
    background-color: #fff;
    border-color: #D1000A#D1000A #fff;
    border-bottom: 1px solid #D1000A;
    border-right: 0;
    border-left: 3px solid #D1000A;
}

.tabs-vertical-success.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #15ca20;
}

.tabs-vertical-success.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-success.tabs-vertical .nav-tabs .nav-link.active {
    color: #15ca20;
    background-color: #fff;
    border-color: #15ca20 #15ca20 #fff;
    border-bottom: 1px solid #15ca20;
    border-right: 0;
    border-left: 3px solid #15ca20;
}

.tabs-vertical-info.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #D1000A;
}

.tabs-vertical-info.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-info.tabs-vertical .nav-tabs .nav-link.active {
    color: #D1000A;
    background-color: #fff;
    border-color: #D1000A #D1000A #fff;
    border-bottom: 1px solid #D1000A;
    border-right: 0;
    border-left: 3px solid #D1000A;
}

.tabs-vertical-danger.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #fd3550;
}

.tabs-vertical-danger.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-danger.tabs-vertical .nav-tabs .nav-link.active {
    color: #fd3550;
    background-color: #fff;
    border-color: #fd3550 #fd3550 #fff;
    border-bottom: 1px solid #fd3550;
    border-right: 0;
    border-left: 3px solid #fd3550;
}

.tabs-vertical-warning.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #ff9700;
}

.tabs-vertical-warning.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-warning.tabs-vertical .nav-tabs .nav-link.active {
    color: #ff9700;
    background-color: #fff;
    border-color: #ff9700 #ff9700 #fff;
    border-bottom: 1px solid #ff9700;
    border-right: 0;
    border-left: 3px solid #ff9700;
}

.tabs-vertical-dark.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #223035;
}

.tabs-vertical-dark.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-dark.tabs-vertical .nav-tabs .nav-link.active {
    color: #223035;
    background-color: #fff;
    border-color: #223035 #223035 #fff;
    border-bottom: 1px solid #223035;
    border-right: 0;
    border-left: 3px solid #223035;
}

.tabs-vertical-secondary.tabs-vertical .nav-tabs{
	border:0;
	border-right: 1px solid #75808a;
}

.tabs-vertical-secondary.tabs-vertical .nav-tabs .nav-item.show .nav-link, .tabs-vertical-secondary.tabs-vertical .nav-tabs .nav-link.active {
    color: #75808a;
    background-color: #fff;
    border-color: #75808a #75808a #fff;
    border-bottom: 1px solid #75808a;
    border-right: 0;
    border-left: 3px solid #75808a;
}

.nav-pills .nav-link {
    border-radius: .25rem;
    color: #223035;
    font-size: 12px;
    text-align: center;
	letter-spacing: 1px;
    font-weight: 600;
    text-transform: uppercase;
	margin: 3px;
    padding: 12px 20px;
	-webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease; 

}

.nav-pills .nav-link:hover {
    background-color:#f4f5fa;
}

.nav-pills .nav-link i{
	margin-right:2px;
	font-weight: 600;
}

.top-icon.nav-pills .nav-link i{
	margin: 0px;
	font-weight: 500;
	display: block;
    font-size: 20px;
    padding: 5px 0;
}

.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    color: #fff;
    background-color: #D1000A;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(220,92,65);
}

.nav-pills-success .nav-link.active, .nav-pills-success .show>.nav-link {
    color: #fff;
    background-color: #15ca20;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(21, 202, 32, .5);
}

.nav-pills-info .nav-link.active, .nav-pills-info .show>.nav-link {
    color: #fff;
    background-color:#D1000A;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(220,92,65);
}

.nav-pills-danger .nav-link.active, .nav-pills-danger .show>.nav-link{
    color: #fff;
    background-color: #fd3550;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(253, 53, 80, .5);
}

.nav-pills-warning .nav-link.active, .nav-pills-warning .show>.nav-link {
    color: #fff;
    background-color: #ff9700;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(255, 151, 0, .5);
}

.nav-pills-dark .nav-link.active, .nav-pills-dark .show>.nav-link {
    color: #fff;
    background-color: #223035;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(34, 48, 53, .5);
}

.nav-pills-secondary .nav-link.active, .nav-pills-secondary .show>.nav-link {
    color: #fff;
    background-color: #75808a;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(117, 128, 138, .5);
}
.card .tab-content{
	padding: 1rem 0 0 0;
}

.z-depth-3 {
    -webkit-box-shadow: 0 11px 7px 0 rgba(0,0,0,0.19),0 13px 25px 0 rgba(0,0,0,0.3);
    box-shadow: 0 11px 7px 0 rgba(0,0,0,0.19),0 13px 25px 0 rgba(0,0,0,0.3);
}
h4{
    color: black;
}
footer {
            background-color: black; /* Couleur de fond */
            color: white; /* Couleur du texte */
            text-align: center; /* Alignement du texte au centre */
            padding: 2px; /* Espacement intérieur */
            position: fixed; /* Position fixe par rapport à la fenêtre */
            bottom: 0; /* Positionnement au bas de la fenêtre */
            width: 100%; /* Largeur de 100% */
        }
#dec {            background-color: #D1000A; /* Couleur de fond */
            color: #fff; /* Couleur du texte */
            padding: 10px; /* Espacement intérieur */
            border: none; /* Supprime la bordure */
            border-radius: 4px; /* Coins arrondis */
            cursor: pointer; /* Curseur pointeur au survol */
            width: 100%; /* Largeur de 100% */
        }
#dec:hover{background-color: black;}
#save:hover{background-color: black;}
.btnacc{background-color:black;color: white;border-radius: 5px;cursor: pointer;align-items: center;}
.btnacc:hover{background-color:rgba(0, 0, 0, 0.751);color: white;}
#navg {
    background-color:white;
}
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
    </style>
</head>
<body>
<nav class=" z-depth-3">
  <ol id="navg"class="breadcrumb">
    <li class=""><a href="Acc.php"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" id="IconChangeColor" height="15" width="19"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" id="mainIconPathAttribute" fill="black"></path></svg><span style="color:black;"> Acceuil &nbsp;</span></a></li>
    <li class="" ><span style="color:#D1000A;"> / Profil</span></li>
  </ol>
</nav><br>
   
<div class="container">
    
<div class="row">
    
<div class="col-lg-4">
<div class="profile-card-4 z-depth-3">
<div class="card">
<div class="card-body text-center bg-primary rounded-top">
<div class="user-box">
<img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="photo de profil">
</div>
<h3 class="mb-1 text-white"><?php echo $userData['Nom']; ?></h3>
<h7 class="text-light">Professeur</h7>
</div>
<div class="card-body">
<ul class="list-group shadow-none">
    <li class="list-group-item">
        <div class="list-icon">
        <i class="fa fa-phone-square"></i>
        </div>
        <div class="list-details">
        <span><?php echo $userData['nom_utilisateur']; ?></span>
        <small>Nom d'utilisateur</small>
        </div>

<li class="list-group-item">
<div class="list-icon">
<i class="fa fa-phone-square"></i>
</div>
<div class="list-details">
<span><?php echo $userData['numero_tel']; ?></span>
<small>Numéro de téléphone</small>
</div>
</li>
<li class="list-group-item">
<div class="list-icon">
<i class="fa fa-envelope"></i>
</div>
<div class="list-details">
<span><?php echo $userData['email']; ?></span>
<small>Addresse email</small>
</div>
</li>
</ul>
<br>

<button id="dec" class="btn btn-primary">Déconnexion</button>

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
</div>
<div class="col-lg-8">
    
    
<div class="card z-depth-3">
<div class="card-body">
<ul class="nav nav-pills nav-pills-primary nav-justified">
<li class="nav-item">
<a href="javascript:void();" data-target="#profile" data-toggle="pill" class="nav-link active show"><i class="icon-user"></i> <span class="hidden-xs">Profil</span></a>

<li class="nav-item">
<a href="javascript:void();" data-target="#edit" data-toggle="pill" class="nav-link"><i class="icon-note"></i> <span class="hidden-xs">Modifier</span></a>
</li>
</ul>
<div class="tab-content p-3">
<div class="tab-pane active show" id="profile">
<h4 class="mb-3">Profil d'utilisateur</h4>
<div class="row">
<div class="col-md-6">
<h6>Matiéres</h6>
<ul>
<?php
// Boucle à travers les données des matières
foreach ($matiereData as $matiere) {
    echo "<li>".$matiere['nom_matiere']."</li>";
}
?>
</ul>
<h6>Niveaux</h6>
<ul><li>JM1</li><li>JM2</li><li>JM3</li><li>JM Soutenance</li></ul>
</div>

<div class="col-md-12">
<h4 class="mt-2 mb-3"><span class="fa fa-clock-o ion-clock float-right"></span>Statistiques</h4>
<div class="row text-center mt-4">
    <div class="col p-2">
    <h4 class="mb-1 line-height-5"><?php echo $nombreQCM; ?></h4>

    <small class="mb-0 font-weight-bold">QCM Crées</small>
    </div>


    </div>
</div>
</div>

</div>

<div class="tab-pane" id="edit">






<form action="modifier_profil.php" method="post" enctype="multipart/form-data">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Nom</label>
            <div class="col-lg-9">
                <input  type="text" disabled placeholder="<?php echo htmlspecialchars($userData['Nom']); ?>">
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Email</label>
            <div class="col-lg-9">
                <input  type="email" disabled placeholder="<?php echo htmlspecialchars($userData['email']); ?>">
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Nom d'utilisateur</label>
            <div class="col-lg-9">
                <input type="text" disabled placeholder="<?php echo htmlspecialchars($userData['nom_utilisateur']); ?>">
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Nouveau mot de passe</label>
            <div class="col-lg-9">
                <input class="form-control" type="password" name="nouveau_mdp" placeholder="Entrez votre nouveau mot de passe"><br>
                <input type="checkbox" id="show_password" onclick="togglePassword()"> 
                <label for="show_password">Afficher le mot de passe</label>
            
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
                <input type="reset" class="btn btn-secondary" value="Annuler">
                <input type="submit" class="btn btn-primary" value="Sauvegarder les modifications">
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
	
</script>
<footer>
    <p>© 2024 Quick qcm - Tous droits réservés</p>
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

        function togglePassword() {
            const passwordField = document.getElementById('nouveau_mdp');
            const showPasswordCheckbox = document.getElementById('show_password');
            if (showPasswordCheckbox.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>


</body>
</html>