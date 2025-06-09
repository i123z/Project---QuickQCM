<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    try {
        $bdd = new PDO("mysql:host=localhost:3308;dbname=projetqcm;charset=utf8mb4", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $recupUser = $bdd->prepare('SELECT * FROM professeurs WHERE email = ? AND mdp = ?');
        $recupUser->execute(array($email, $mdp));
        $userData = $recupUser->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $_SESSION['email'] = $email;
            $_SESSION['mdp'] = $mdp;
            $_SESSION['id_prof'] = $userData['id'];
            header('Location: index.php');
            exit();
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion Professeur</h1>
    <form method="post" action="">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="mdp">Mot de passe :</label>
        <input type="password" id="mdp" name="mdp" required>
        <br>
        <button type="submit">Se connecter</button>
    </form>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
</body>
</html>
