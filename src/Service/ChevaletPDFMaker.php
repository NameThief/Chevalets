<?php

namespace App\Service;

use App\Model\Chevalet;
use FPDF;

class ChevaletPDFMaker {

    public function addChevaletToPDF (FPDF $pdf,Chevalet $chevalet):void
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
        // Couleur et police de texte
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 16);
        //      Ligne Haut
        $pdf->Line(0, 49, 297, 49);
        //	    Ligne Bas
        $pdf->Line(0, 161, 297, 161);
        //      Ligne gauche
        $pdf->Line(23.5, 0, 23.5, 210);
        //      Ligne Droite
        $pdf->Line(273.5, 0, 273.5, 210);
        //      ligne Milieu
        $pdf->Line(0, 105, 297, 105);
        $pdf->SetLeftMargin($x + 60);
        $pdf->SetX($x + 60);
        $pdf->SetY(105 + 5);
        $pdf->Image($cutPath, 10, 47, 6);
        $pdf->Image($cutPath, 10, 159, 6);
        $pdf->Image($cutPath, 20.5, 10, 6);
        $pdf->Image($cutPath, 270.5, 10, 6);
        // ajout des informations textuelles
        $pdf->SetFont('Arial', 'B', 36);
        $pdf->Cell(0, 10, ucwords(strtolower($chevalet->getPrenom())) . ' ' . strtoupper($chevalet->getNom()), 0, 1);
        $pdf->SetFont('Arial', '', 30);
        $pdf->SetY($pdf->GetY() + 10);
        $pdf->Multicell(190, 10, $chevalet->getFonction());
    }
}