<?php
session_start(); // Démarrer la session

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['email']) && isset($_SESSION['mdp'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Connectez-vous à la base de données
            $bdd = new PDO("mysql:host=localhost:3308;dbname=projetqcm;charset=utf8mb4", 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Récupérer l'email de l'utilisateur connecté
            $email = $_SESSION['email'];

            // Récupérer le nouveau mot de passe du formulaire
            $nouveau_mdp = $_POST['nouveau_mdp'];

            if (!empty($nouveau_mdp)) {
                // Préparer la requête pour mettre à jour le mot de passe sans hachage
                $updateMdp = $bdd->prepare('UPDATE professeurs SET mdp = ? WHERE email = ?');
                $updateMdp->execute(array($nouveau_mdp, $email));

                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le mot de passe a été mis à jour avec succès.";
                header('Location: profil.php');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Veuillez entrer un nouveau mot de passe.";
                header('Location: profil.php');
                exit;
            }
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
} else {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header('Location: login.php');
    exit;
}
?>
