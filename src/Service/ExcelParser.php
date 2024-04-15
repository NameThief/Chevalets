<?php

namespace App\Service;

use App\Model\Personne;
use App\Model\Reunion;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelParser
{
    private string $excelFilePath;
    public function parse(Reunion $reunion): void {
        // Charger le fichier Excel
        $reader = IOFactory::createReader('Xls'); // ou Xlsx selon le format du fichier Excel
        $spreadsheet = $reader->load($this->getExcelFilePath());

        $sheetNames = ["Informations", "Objectifs", "OrdreDuJour", "Animateurs", "Participants"];

        foreach ($sheetNames as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            $highestRow = $worksheet->getHighestDataRow();

            // Extraction des données selon la feuille de calcul
            switch ($sheetName) {
                case "Informations":
                    // Vérifier si la cellule B1 contient des données avant d'extraire les titres
                    if (!empty($worksheet->getCell('B1')->getValue())) {
                        // Première cellule de la plage de données pour les titres
                        $reunion->setObjet($worksheet->getCell('B1' )->getValue());
                    }
                    // Vérifier si la cellule B2 contient des données avant d'extraire les dates
                    if (!empty($worksheet->getCell('B2')->getValue())) {
                        // Première cellule de la plage de données pour les dates
                        $reunion->setDate($worksheet->getCell('B2')->getValue());
                    }
                    if (!empty($worksheet->getCell('B3')->getValue())){
                        // Première cellule de la plage de données pour l'heure de début
                        $reunion->setHeureDebut($worksheet->getCell('B3')->getValue());
                    }
                    if (!empty($worksheet->getCell('B4')->getValue())){
                        // Première cellule de la plage de données pour l'heure de fin
                        $reunion->setHeureFin($worksheet->getCell('B4')->getValue());
                    }
                    break;
                case "Objectifs":
                    for ($row = 2; $row <= $highestRow; $row++) {
                        if (!empty($worksheet->getCell('B' . $row)->getValue())) {
                            $reunion->addObjectif($worksheet->getCell('B' . $row)->getValue());
                        }
                    }
                    break;
                case "OrdreDuJour":
                    for ($row = 2; $row <= $highestRow; $row++) {
                        if (!empty($worksheet->getCell('B' . $row)->getValue())) {
                            $reunion->addOrdreDuJour($worksheet->getCell('B' . $row)->getValue());
                        }
                    }
                    break;
                case "Animateurs":
                    for ($row = 2; $row <= $highestRow; $row++) {
                        // Vérifier si les colonnes B, C et E ne sont pas vides avant d'extraire les données
                        if (!empty($worksheet->getCell('B' . $row)->getValue()) && !empty($worksheet->getCell('C' . $row)->getValue()) && !empty($worksheet->getCell('E' . $row)->getValue())) {
                            $personne = new Personne();
                            $personne->setNom(mb_strtoupper($worksheet->getCell('B' . $row)->getValue(), 'ISO-8859-1'));
                            $personne->setPrenom($worksheet->getCell('C' . $row)->getValue());
                            $personne->setFonction($worksheet->getCell('E' . $row)->getValue());
                            $personne->setCivilite($worksheet->getCell('A' . $row)->getValue());
                            $personne->setService($worksheet->getCell('D' . $row)->getValue());
                            $reunion->addAnimateur($personne);
                        }
                    }
                    break;

                case "Participants":
                    for ($row = 2; $row <= $highestRow; $row++) {
                        // Vérifier si les colonnes B, C et E ne sont pas vides avant d'extraire les données
                        if (!empty($worksheet->getCell('B' . $row)->getValue()) && !empty($worksheet->getCell('C' . $row)->getValue()) && !empty($worksheet->getCell('E' . $row)->getValue())) {
                            $personne = new Personne();
                            $personne->setNom(mb_strtoupper($worksheet->getCell('B' . $row)->getValue(), 'ISO-8859-1'));
                            $personne->setPrenom($worksheet->getCell('C' . $row)->getValue());
                            $personne->setFonction($worksheet->getCell('E' . $row)->getValue());
                            $personne->setCivilite($worksheet->getCell('A' . $row)->getValue());
                            $personne->setService($worksheet->getCell('D' . $row)->getValue());
                            $reunion->addParticipant($personne);
                        }
                    }
                    break;

                default:
                    // Ne rien faire pour les autres feuilles de calcul
                    break;
            }
        }
    }

    public function getExcelFilePath(): string
    {
        return $this->excelFilePath;
    }

    public function setExcelFilePath(string $excelFilePath): void
    {
        $this->excelFilePath = $excelFilePath;
    }
}