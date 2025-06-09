<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Chemin du fichier Excel
$filePath = 'Correction/Listes etudiants excel/qcm_data21_05_24_59_58.xlsx';

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($filePath);

    // Obtenir la feuille active
    $sheet = $spreadsheet->getActiveSheet();

    // Commencer la table HTML
    echo "<table border='1'>";

    // Parcourir toutes les lignes et colonnes
    foreach ($sheet->getRowIterator() as $row) {
        echo "<tr>";

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Parcourir toutes les cellules, même les vides

        // Pour chaque cellule dans la ligne
        foreach ($cellIterator as $cell) {
            if (!is_null($cell)) {
                // Afficher la valeur calculée de la cellule dans une cellule de tableau
                echo "<td>" . $cell->getCalculatedValue() . "</td>";
            }
        }

        echo "</tr>";
    }

    // Fermer la table HTML
    echo "</table>";
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo 'Erreur lors du chargement du fichier: ', $e->getMessage();
}
?>
