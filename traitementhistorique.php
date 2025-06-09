<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['qcmData'])) {
    $qcmData = json_decode($_POST['qcmData'], true);



    // Écrire le JSON dans un fichier
    $nomFichier = $qcmData['nom_fichier']; // Supposons que 'nom_fichier' contient le nom du fichier
    $file_path = 'latexQCM/' . $nomFichier . '.json'; // Utilisation de '/' au lieu de '\\' pour la compatibilité avec Linux
    $json_data = json_encode($qcmData); // Convertir les données en JSON
    file_put_contents($file_path, $json_data);

    // Exécuter votre script Python pour générer le PDF
    shell_exec('python latexQCM/Json_lecture.py ' . $nomFichier . '.json');

    // Spécifier le chemin du fichier PDF généré par le script Python
    $cheminFichierPDF = 'latexQCM/' . $nomFichier . '.pdf';

    // Envoyer les en-têtes HTTP pour forcer le téléchargement
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"Questionnaire.pdf\"");
    readfile($cheminFichierPDF);

    exit; // Terminer le script après avoir renvoyé le fichier PDF
} else {
    echo 'Erreur : Accès invalide.';
}
?>
