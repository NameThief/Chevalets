<?php

namespace App\Service;

use App\Model\Chevalet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ChevaletsExcelParser
{
    private string $excelFilePath;

    public function parseFile(ChevaletPDFMaker $chevaletPDFMaker): void {
        // Charger le fichier Excel
        $reader = IOFactory::createReader('Xls'); // ou Xlsx selon le format du fichier Excel
        $spreadsheet = $reader->load($this->getExcelFilePath());
        $sheetNames = ["Animateurs", "Participants"];

        foreach ($sheetNames as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            $highestRow = $worksheet->getHighestDataRow();

            // Exclure la première ligne
            for ($row = 2; $row <= $highestRow; $row++) {
                // Accédez aux clés uniquement si elles existent
                if (!empty($worksheet->getCell('B' . $row)->getValue()) && !empty($worksheet->getCell('C' . $row)->getValue()) && !empty($worksheet->getCell('E' . $row)->getValue())) {
                    $chevalet = new Chevalet();
                    $chevalet->setNom(($worksheet->getCell('B' . $row)->getValue()));
                    $chevalet->setPrenom($worksheet->getCell('C' . $row)->getValue());
                    $chevalet->setFonction($worksheet->getCell('E' . $row)->getValue());
                    // Ajout du chevalet au PDF en utilisant l'objet $chevaletMaker
                    $chevaletPDFMaker->addChevaletToPDF($chevalet);
                }
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