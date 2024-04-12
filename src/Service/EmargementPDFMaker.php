<?php

namespace App\Service;

use App\Model\Emargement;
use App\Model\Personne;
use FPDF;

class EmargementPDFMaker
{
    private FPDF $fpdf;

    public function __construct()
    {
        $this->fpdf = new FPDF(); // Initialisez votre objet FPDF
    }

    public function getOutput(): string
    {
        return $this->fpdf->Output('S');
    }

    public function generatePDF(Emargement $emargement): string
    {
        // Ajouter une nouvelle page
        $this->fpdf->AddPage();
        // Définition des valeurs pour les lignes verticales et horizontales du tableau
        // Position verticale de départ
        $y = 45;
        // Position horizontale de départ
        $c1 = 5;
        $c2 = $c1 + 150;
        $c3 = $c2 + 50;
        // Ligne horizontale haut du tableau Page 1
        $this->fpdf->Line(5, 35, 205, 35);
        $this->fpdf->SetFont('Arial', '', 14);
        $this->fpdf->Text(80, 34, "Duree : de " . stripslashes($emargement->getHeureDebut()) . " a " . stripslashes($emargement->getHeureFin()));

        // Logique pour générer le PDF des listes d'émargements
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c1 + 30, $y - 4, "Animateurs");
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c2 + 15, $y - 4, "Emargement");
        $this->fpdf->Line($c1, $y, $c3, $y);

        $y += 10;
        $nb_page = 1;
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c2 + 3, 285, "Page : " . $nb_page);

        $logoPath = "assets/img/logo.jpg";
        // Ajouter le titre
        $titreEncoded = mb_convert_encoding($emargement->getObjet(), 'ISO-8859-1', 'UTF-8');
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(0, 10, $titreEncoded, 0, 1, 'C');

        // Ajouter la date
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Cell(0, 10, $emargement->getDate(), 0, 1, 'C');

        $this->fpdf->SetFont('Arial', '', 10);
        // Ajout du logo
        $this->fpdf->Image($logoPath, 4, 4, 32);

        /** @var Personne $animateur */
        foreach ($emargement->getAnimateurs() as $animateur) {
            $hauteur_ligne = 12;
            if ($this->fpdf->GetY() + $hauteur_ligne > 270) {
                $this->fpdf->AddPage();
                // Réinitialiser la position verticale
                $this->fpdf->SetY(45);
                // Incrémenter le numéro de page
                $nb_page++;
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c2 + 3, 285, "Page : " . $nb_page);

            }
            if ($animateur->getNom() != "" && $animateur->getPrenom() != "") {
                $this->fpdf->Line($c1, $y, $c3, $y);
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c1 + 3, $y - 6, $animateur->getCivilite() . " " . mb_convert_encoding($animateur->getPrenom(), 'ISO-8859-1', 'UTF-8') . " " . mb_convert_encoding($animateur->getNom(), 'ISO-8859-1', 'UTF-8'));
                $this->fpdf->SetFont('Arial', '', 8);
                $this->fpdf->Text($c1 + 3, $y - 1, mb_convert_encoding($animateur->getService() . ' - ' . $animateur->getFonction(), 'ISO-8859-1', 'UTF-8'));
                $y += 12;
            }
        }
        // Ligne vide après les animateurs
        $this->fpdf->Line($c1, $y, $c3, $y);
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c1 + 30, $y - 5, 'Participants');
        $y += 12;

        /** @var Personne $participant */
        foreach ($emargement->getParticipants() as $participant) {
            if ($this->fpdf->GetY() + $hauteur_ligne > 270) {
                $this->fpdf->AddPage();
                // Réinitialiser la position verticale
                $this->fpdf->SetY(45);
                // Incrémenter le numéro de page
                $nb_page++;
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c2 + 3, 285, "Page : " . $nb_page);

                // Dessiner les lignes du tableau sur la nouvelle page
                // Ligne verticale gauche
                $this->fpdf->Line($c1, $this->fpdf->GetY(), $c1, 271);
                // Ligne verticale milieu
                $this->fpdf->Line($c1 + 150, $this->fpdf->GetY(), $c1 + 150, 271);
                // Ligne verticale droite
                $this->fpdf->Line($c1 + 200, $this->fpdf->GetY(), $c1 + 200, 271);
                // Ligne horizontale haut
                $this->fpdf->Line(5, 35, 205, 35);
            }

            if ($participant->getNom() != "" && $participant->getPrenom() != "") {
                $this->fpdf->Line($c1, $y, $c3, $y);
                $this->fpdf->Line($c1, $y, $c1, 35); // Ligne verticale gauche page 2
                $this->fpdf->Line(155, $y, 155, 35,); // Ligne verticale milieu page 2
                $this->fpdf->Line(205, $y, 205, 35); // Ligne verticale droite page 2
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c1 + 3, $y - 6, $participant->getCivilite() . " " . mb_convert_encoding($participant->getPrenom(), 'ISO-8859-1', 'UTF-8') . " " . mb_convert_encoding($participant->getNom(), 'ISO-8859-1', 'UTF-8'));
                $this->fpdf->SetFont('Arial', '', 8);
                $this->fpdf->Text($c1 + 3, $y - 1, mb_convert_encoding($participant->getService() . ' - ' . $participant->getFonction(), 'ISO-8859-1', 'UTF-8'));
                $y += 12;
            }

            if ($y >= 283) {
                $this->fpdf->AddPage();
                $this->fpdf->Line(5, 35, 205, 35);
                // Réinitialiser la position verticale
                $y = 45;
                // Réinitialiser le nombre de participants sur la page
                // Incrémenter le numéro de page
                $nb_page++;
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c2 + 3, 285, "Page : " . $nb_page);

            }
        }

        return $this->fpdf->Output('S');
    }
}
