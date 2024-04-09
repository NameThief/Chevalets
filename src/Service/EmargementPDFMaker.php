<?php

namespace App\Service;

use App\Model\Emargement;
use FPDF;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmargementPDFMaker
{
    private FPDF $fpdf;

    private int $currentPosY;


    public
    function __construct()
    {
        $this->fpdf = new FPDF(); // Initialisez votre objet FPDF
    }

    public
    function getOutput(): string
    {
        return $this->fpdf->Output('S');
    }

    public
    function generatePDF(
        array $personnes,
        string $titre,
        string $date,
        string $hdebut,
        string $hfin,
    ): string
    {
        // Ajouter une nouvelle page
        $this->fpdf->AddPage();
        // Définition des valeurs pour les lignes verticales et horizontales du tableau
        $w = 45; // Position verticale de départ
        $c1 = 5; // Position horizontale de départ
        $c2 = $c1 + 150;
        $c3 = $c2 + 50;
        $this->fpdf->SetFont('Arial', '', 14);
        $this->fpdf->Text(80, 34, "Duree : de " . stripslashes($hdebut) . " a " . stripslashes($hfin));
        // Logique pour générer le PDF des listes d'émargements

        $this->fpdf->SetFont('Arial', '', 8);
        $this->fpdf->Text($c1 + 3, $w - 1, "Fonction");
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c2 + 3, $w - 3, "Emargement");
        $this->fpdf->Line($c1, $w, $c3, $w);
        $this->fpdf->Line($c2, $w, $c2, 271);
        $w += 10;
        $nb_page = 1;
        $this->fpdf->Text($c2 + 3, 285, "Page : " . $nb_page);

        $logoPath = "assets/img/logo.jpg";
        // Ajouter le titre
        $titreEncoded = mb_convert_encoding($titre, 'ISO-8859-1', 'UTF-8');
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(0, 10, $titreEncoded, 0, 1, 'C');

        // Ajouter la date
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(0, 10, $date, 0, 1, 'C');

        $this->fpdf->SetFont('Arial', '', 10);
        // Ajout du logo
        $this->fpdf->Image($logoPath, 4, 4, 32);
        // Lignes du tableau
        $this->fpdf->Line($c1, $w, $c1, 271); // Ligne verticale gauche
        $this->fpdf->Line($c1 + 150, $w, $c1 + 150, 271); // Ligne verticale milieu
        $this->fpdf->Line($c1 + 200, $w, $c1 + 200, 271); // Ligne verticale droite
        $this->fpdf->Line($c1, $w, $c1 + 200, $w); // Ligne horizontale du haut
        $this->fpdf->Line($c1, $w, $c3, $w);

        foreach ($personnes['animateurs'] as $animateur) {
            $nom = str_replace("\\'", "'", $animateur['nom']); //stripslashes
            $prenom = str_replace("\\'", "'", $animateur['prenom']);
            $fonction = str_replace("\\'", "'", $animateur['service'] . " - " . $animateur['fonction']);
            if ($nom != "" && $prenom != ""){
                $this->fpdf->Line($c1, $w, $c3, $w);
                $this->fpdf->SetFont('Arial', '', 10);
                $nomEncoded = mb_convert_encoding(mb_strtoupper($nom, 'ISO-8859-1'), 'ISO-8859-1', 'UTF-8');
                $fonctionEncoded = mb_convert_encoding($fonction, 'ISO-8859-1', 'UTF-8');
                $this->fpdf->Text($c1 + 3, $w - 6, mb_convert_encoding($animateur['civilite'], 'ISO-8859-1', 'UTF-8') . " " . mb_convert_encoding($prenom, 'ISO-8859-1', 'UTF-8') . " " . $nomEncoded);
                $this->fpdf->SetFont('Arial', '', 8);
                $this->fpdf->Text($c1 + 3, $w - 1, $fonctionEncoded);
                $w += 12;
            }
        }
        // Ligne vide après les animateurs
        $this->fpdf->Line($c1, $w, $c3, $w);
        $this->fpdf->Text($c1 + 3, $w - 6, '');
        $w += 12;

        // Retourner le contenu du PDF sous forme de chaîne
        return $this->fpdf->Output('S');
    }

    public function getCurrentPosY(): int
    {
        return $this->currentPosY;
    }

    public function setCurrentPosY(int $currentPosY): void
    {
        $this->currentPosY = $currentPosY;
    }
}
