
<?php
// Connexion à la base de données
session_start();
try {
  $bdd = new PDO("mysql:host=localhost:3308;dbname=projetqcm;port=3308;charset=utf8mb4", 'root', '');
  $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}

// Vérification si le formulaire a été soumis
if(isset($_POST['Connexion'])){
    // Vérification si les champs email et mot de passe ne sont pas vides
    if(!empty($_POST['email']) AND !empty($_POST['password'])){
        // Nettoyage de l'email et hachage du mot de passe
        $email = htmlspecialchars($_POST['email']);
        $mdp = $_POST['password'];

        $recupUser = $bdd->prepare('SELECT * FROM professeurs WHERE email = ? AND mdp = ?');
        $recupUser->execute(array($email, $mdp));
        if($recupUser->rowCount() > 0){
          $_SESSION[ 'email'] = $email;
          $_SESSION['mdp'] =$mdp;
          $_SESSION['id'] = $recupUser->fetch()['id'];
          header('Location: Acc.php');
        
        } else {
        echo "<div class='alert alert-danger'>"."Votre mot de passe ou email est incorrect."."</div>";
        }
      } else {
    echo "Veuillez compléter tous les champs...";
  }
}
?>




<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="login_css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="login_css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="login_css/style.css">
    <link rel="icon" href="images/logo2.png" type="image/png">
    <title>Professeur login</title>
    <script>
        // Fonction pour vérifier la navigation en avant
        function preventForwardNavigation() {
            // Ajoute une nouvelle entrée à l'historique
            history.pushState(null, null, location.href);
            
            // Ajoute un événement pour détecter les changements de l'historique
            window.addEventListener('popstate', function (event) {
                // Si l'utilisateur essaie de naviguer en avant, renvoie le navigateur en arrière
                history.pushState(null, null, location.href);
            });
        }
    </script>
    <!-- Custom CSS -->
    <style>
      .header {
        background-color: black; /* Change this color to the one you desire */
        padding: 5px 0; /* Add padding to the top and bottom of the header */
      }

      .header img {
        max-width: 190px; /* Adjust the maximum width of the logo */
      }

      /* CSS for the button */
      .btn-primary {
        background-color: #D1000A; /* Button background color */
        border-color: #D1000A; /* Button border color */
      }

      .btn-primary:hover{background-color: black;}
      .btn-primary:active { background-color:#D1000A;} /* Button background color on hover or focus */
       
    </style>
  </head>
  <body onload="preventForwardNavigation()">
    <!-- Header -->
    <header class="header">
      <div class="container text-center">
        <img src="images/logo.png" alt="Logo de votre site">
      </div>
    </header>

    <div class="d-md-flex half">
      <div class="bg" style="background-image: url('images/bg_1.jpg');"></div>
      <div class="contents">
        <div class="container">
          <div class="row align-items-center justify-content-center">
            <div class="col-md-12">
              <div class="form-block mx-auto">
                <div class="text-center mb-5">
                  <h3>CONNEXION <strong>PROFESSEUR</strong></h3>
                  <!-- <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur adipisicing.</p> -->
                </div>
                <form action="" method="POST">
                  <div class="form-group first">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Votre-email@gmail.com" required>
                  </div>
                  <div class="form-group last mb-3">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Votre Mot de passe" required><br>
                    <input type="checkbox" id="show_password" onclick="togglePassword()"> 
                    <label for="show_password">Afficher le mot de passe</label>
                  </div>
                  
                  <div class="d-sm-flex mb-5 align-items-center">
                    <span class="ml-auto"><a href="#" class="forgot-pass">Mot de passe oublié ?</a></span> 
                  </div>
                  
                  <button type="submit" class="btn btn-block btn-primary"name ="Connexion">Connexion</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
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
