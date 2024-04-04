<?php

namespace App\Service;

use App\Model\Chevalet;
use FPDF;


class ChevaletPDFMaker
{
    private FPDF $fpdf;

    public function __construct()
    {
        $this->fpdf = new FPDF();
    }

    public function getOutput(): string {
        $this->fpdf->Output();
    }

    public function addChevaletToPDF(Chevalet $chevalet ): void
    {
        $this->fpdf->AddPage("L");
        $cutPath = "assets/img/cut.gif";
        $logoPath = "assets/img/logo.jpg";

        // Positionnement et dimensions des images
        $x = 24;
        $y = 105;

        // Couleurs
        $this->fpdf->SetDrawColor(192, 192, 192);

        // Ajout du logo
        $this->fpdf->Image($logoPath, $x + 1, $y + 1, 38);

        // Logique pour créer le chevalet PDF avec les données fournies
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->SetTextColor(0);

        // Lignes
        $this->fpdf->Line(0, 49, 297, 49); // Haut
        $this->fpdf->Line(0, 161, 297, 161); // Bas
        $this->fpdf->Line(23.5, 0, 23.5, 210); // Gauche
        $this->fpdf->Line(273.5, 0, 273.5, 210); // Droite
        $this->fpdf->Line(0, 105, 297, 105); // Milieu

        // Marges
        $this->fpdf->SetLeftMargin($x + 60);
        $this->fpdf->SetX($x + 60);
        $this->fpdf->SetY(105 + 5);

        // Images
        $this->fpdf->Image($cutPath, 10, 47, 6);
        $this->fpdf->Image($cutPath, 10, 159, 6);
        $this->fpdf->Image($cutPath, 20.5, 10, 6);
        $this->fpdf->Image($cutPath, 270.5, 10, 6);

        // Ajout des informations textuelles
        // Nom et prénom en gras
        $this->fpdf->SetFont('Arial', 'B', 36);

// Convertir le prénom en minuscules avec prise en charge UTF-8
        $prenom = mb_strtolower($chevalet->getPrenom(), 'UTF-8');

// Convertir le nom en majuscules avec prise en charge UTF-8
        $nom = mb_strtoupper($chevalet->getNom(), 'UTF-8');

// Concaténer le prénom converti avec le nom
        $prenomNom = ucwords($prenom) . ' ' . $nom;

        $this->fpdf->Cell(0, 10, mb_convert_encoding($prenomNom, 'ISO-8859-1', 'UTF-8'), 0, 1);

// Fonction en style normal
        $this->fpdf->SetFont('', '', 0, '', true);
        $this->fpdf->SetY($this->fpdf->GetY() + 10); // Espacement vertical

// Convertir la fonction avec prise en charge UTF-8
        $fonction = mb_convert_encoding($chevalet->getFonction(), 'ISO-8859-1', 'UTF-8');
        $this->fpdf->Multicell(190, 10, $fonction);
    }
}