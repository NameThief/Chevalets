<?php

namespace App\Service;

use App\Model\CompteRendu;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class WordReader
{

    protected string $wordFilePath;
    protected string $spreadsheet;
    protected $sheetNames = [];
    protected $animateurs = [];
    protected $participants = [];
    protected $objectifs = [];
    protected $ordredujour = [];

//    public function __construct($excelFilePath)
//    {
//        // Charger le fichier Excel
//        $reader = IOFactory::createReader('Xls'); // ou Xlsx selon le format du fichier Excel
//        $this->spreadsheet = $reader->load($excelFilePath);
//
//        // Récupérer les noms de toutes les feuilles
//        $this->sheetNames = $this->spreadsheet->getSheetNames();
//    }

    public function parse(CompteRendu $compteRendu)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls'); // ou Xlsx selon le format du fichier Excel
        $spreadsheet = $reader->load($this->getWordFilePath());

        $sheetNames = ["Animateurs", "Participants", "Informations" ,"Objectifs", "OrdreDuJour"]; // CODE COPIER COLLER VENDREDI SOIR A ADAPTER LUNDI !!

        foreach ($sheetNames as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            $highestRow = $worksheet->getHighestDataRow();
// JUSQUE ICI
            $PHPWord = new PhpWord();
            $section = $PHPWord->addSection();

            $section->addText('Liste des animateurs :');
            foreach ($this->animateurs as $animateur) {
                $section->addText($animateur);
            }

            $tempFileName = tempnam(sys_get_temp_dir(), 'CR');
            $PHPWord->save($tempFileName, 'Word2007');
            $content = file_get_contents($tempFileName);
            unlink($tempFileName);

            return $content;
        }
    }
//    public function extractData()
//    {
//        foreach ($this->sheetNames as $sheetName) {
//            $sheet = $this->spreadsheet->getSheetByName($sheetName);
//            $highestRow = $sheet->getHighestRow();
//
//            switch ($sheetName) {
//                case 'Animateurs':
//                    $this->extractColumnData($sheet, $highestRow, $this->animateurs);
//                    break;
//                case 'Participants':
//                    $this->extractColumnData($sheet, $highestRow, $this->participants);
//                    break;
//                case 'Objectifs':
//                    $this->extractColumnData($sheet, $highestRow, $this->objectifs);
//                    break;
//                case 'OrdreDuJour':
//                    $this->extractColumnData($sheet, $highestRow, $this->ordredujour);
//                    break;
//            }
//        }
//    }

    private function extractColumnData($sheet, $highestRow, &$targetArray)
    {
        for ($row = 2; $row <= $highestRow; ++$row) {
            $cellValue = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $targetArray[] = $cellValue;
        }
    }
    public function getWordFilePath(): string
    {
        return $this->wordFilePath;
    }

    public function setWordFilePath(string $wordFilePath): void
    {
        $this->wordFilePath = $wordFilePath;
    }
}