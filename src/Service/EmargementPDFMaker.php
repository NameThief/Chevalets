<?php

namespace App\Service;

use App\Model\Emargement;
use App\Model\Personne;
use FPDF;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmargementPDFMaker
{
    private FPDF $fpdf;

    private int $currentPosY;

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
        $w = 45; // Position verticale de départ
        $c1 = 5; // Position horizontale de départ
        $c2 = $c1 + 150;
        $c3 = $c2 + 50;
        $this->fpdf->SetFont('Arial', '', 14);
        $this->fpdf->Text(80, 34, "Duree : de " . stripslashes($emargement->getHeureDebut()) . " a " . stripslashes($emargement->getHeureFin()));
        // Logique pour générer le PDF des listes d'émargements

        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c1 + 30, $w - 4, "Animateurs");
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c2 + 15, $w - 4, "Emargement");
        $this->fpdf->Line($c1, $w, $c3, $w);
        $this->fpdf->Line($c2, $w, $c2, 271);
        $w += 10;
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
        // Lignes du tableau
        $this->fpdf->Line($c1, $w - 20, $c1, 271); // Ligne verticale gauche page 1
        $this->fpdf->Line($c1 + 150, $w - 20, $c1 + 150, 271); // Ligne verticale milieu page 1
        $this->fpdf->Line($c1 + 200, $w - 20, $c1 + 200, 271); // Ligne verticale droite page 1

        /** @var Personne $animateur */
        foreach ($emargement->getAnimateurs() as $animateur) {
            //$animateur->setNom(str_replace("\\'", "'", $animateur['nom']));
            //$animateur->setPrenom(str_replace("\\'", "'", $animateur['prenom']));
            //$animateur->setFonction(str_replace("\\'", "'", $animateur['service'] . " - " . $animateur['fonction']));
            if ($animateur->getNom() != "" && $animateur->getPrenom() != "") {
                $this->fpdf->Line($c1, $w, $c3, $w);
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c1 + 3, $w - 6, $animateur->getCivilite() . " " . mb_convert_encoding($animateur->getPrenom(), 'ISO-8859-1', 'UTF-8') . " " . mb_convert_encoding($animateur->getNom(), 'ISO-8859-1', 'UTF-8'));
                $this->fpdf->SetFont('Arial', '', 8);
                $this->fpdf->Text($c1 + 3, $w - 1, mb_convert_encoding($animateur->getService() . ' - ' . $animateur->getFonction(),  'ISO-8859-1', 'UTF-8'));
                $w += 12;
            }
        }
        // Ligne vide après les animateurs
        $this->fpdf->Line($c1, $w, $c3, $w);
        $this->fpdf->SetFont('Arial', '', 10);
        $this->fpdf->Text($c1 + 30, $w - 5, 'Participants');
        $w += 12;

        /** @var Personne $participant */
        foreach ($emargement->getParticipants() as $participant) {

            //$participant->setNom(str_replace("\\'", "'", $participant['nom']));
            //$prenom = str_replace("\\'", "'", $participant['prenom']);
            //$fonction = str_replace("\\'", "'", $participant['service'] . " - " . $participant['fonction']);

            if ($participant->getNom() != "" && $participant->getPrenom() != "") {
                $this->fpdf->Line($c1, $w, $c3, $w);
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c1 + 3, $w - 6, $participant->getCivilite() . " " . mb_convert_encoding($participant->getPrenom(), 'ISO-8859-1', 'UTF-8') . " " . mb_convert_encoding($participant->getNom(), 'ISO-8859-1', 'UTF-8'));
                $this->fpdf->SetFont('Arial', '', 8);
                $this->fpdf->Text($c1 + 3, $w - 1, mb_convert_encoding($participant->getService() . ' - ' . $participant->getFonction(),  'ISO-8859-1', 'UTF-8'));
                $w += 12;
            }

            if ($w >= 283) {
                $this->fpdf->AddPage();
                // Réinitialiser la position verticale
                $w = 45;
                // Réinitialiser le nombre de participants sur la page
                // Incrémenter le numéro de page
                $nb_page++;
                $this->fpdf->SetFont('Arial', '', 10);
                $this->fpdf->Text($c2 + 3, 285, "Page : " . $nb_page);

            }
        }
        $this->fpdf->Line($c1, $w, $c1, 10); // Ligne verticale gauche page 2
        $this->fpdf->Line(155, $w, 155, 10,); // Ligne verticale milieu page 2
        $this->fpdf->Line(205, $w, 205, 10); // Ligne verticale droite page 2
        $this->fpdf->Line($c1, $w, $c3, $w);
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
