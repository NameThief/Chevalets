<?php

namespace App\Service;

use App\Model\Chevalet;
use FPDF;


class ChevaletPDFMaker
{

    public function addChevaletToPDF(FPDF $pdf, Chevalet $chevalet ): void
    {
        $pdf->AddPage("L");
        $cutPath = "assets/img/cut.gif";
        $logoPath = "assets/img/logo.jpg";

        // Positionnement et dimensions des images
        $x = 24;
        $y = 105;

        // Couleurs
        $pdf->SetDrawColor(192, 192, 192);

        // Ajout du logo
        $pdf->Image($logoPath, $x + 1, $y + 1, 38);

        // Logique pour créer le chevalet PDF avec les données fournies
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(0);

        // Lignes
        $pdf->Line(0, 49, 297, 49); // Haut
        $pdf->Line(0, 161, 297, 161); // Bas
        $pdf->Line(23.5, 0, 23.5, 210); // Gauche
        $pdf->Line(273.5, 0, 273.5, 210); // Droite
        $pdf->Line(0, 105, 297, 105); // Milieu

        // Marges
        $pdf->SetLeftMargin($x + 60);
        $pdf->SetX($x + 60);
        $pdf->SetY(105 + 5);

        // Images
        $pdf->Image($cutPath, 10, 47, 6);
        $pdf->Image($cutPath, 10, 159, 6);
        $pdf->Image($cutPath, 20.5, 10, 6);
        $pdf->Image($cutPath, 270.5, 10, 6);

        // Ajout des informations textuelles
        // Nom et prénom en gras
        $pdf->SetFont('Arial', 'B', 36);

// Convertir le prénom en minuscules avec prise en charge UTF-8
        $prenom = mb_strtolower($chevalet->getPrenom(), 'UTF-8');

// Convertir le nom en majuscules avec prise en charge UTF-8
        $nom = mb_strtoupper($chevalet->getNom(), 'UTF-8');

// Concaténer le prénom converti avec le nom
        $prenomNom = ucwords($prenom) . ' ' . $nom;

        $pdf->Cell(0, 10, mb_convert_encoding($prenomNom, 'ISO-8859-1', 'UTF-8'), 0, 1);

// Fonction en style normal
        $pdf->SetFont('', '', 0, '', true);
        $pdf->SetY($pdf->GetY() + 10); // Espacement vertical

// Convertir la fonction avec prise en charge UTF-8
        $fonction = mb_convert_encoding($chevalet->getFonction(), 'ISO-8859-1', 'UTF-8');
        $pdf->Multicell(190, 10, $fonction);
    }
}