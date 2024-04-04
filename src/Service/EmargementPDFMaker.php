<?php

namespace App\Service;

use App\Model\Emargement;
use FPDF;

class EmargementPDFMaker {
    private FPDF $fpdf;

    public function __construct() {
        $this->fpdf = new FPDF(); // Initialisez votre objet FPDF
    }

    public function getOutput(): string {
        return $this->fpdf->Output('S');
    }
    public function generatePDF(
        array $noms,
        array $prenoms,
        array $fonctions,
        string $titre,
        string $date,
        array $services,
        array $civilites
    ): string {
        // Ajouter une nouvelle page
        $this->fpdf->AddPage();

        // Logique pour générer le PDF des listes d'émargements
        $this->emargement($noms, $prenoms, $fonctions, $titre, $date, $services, $civilites);

        // Retourner le contenu du PDF sous forme de chaîne
        return $this->fpdf->Output('S');
    }

    public function emargement(
        array $noms,
        array $prenoms,
        array $fonctions,
        string $titre,
        string $date,
        array $services,
        array $civilites
    ): void {
        // Logique de mise en page pour les listes d'émargements
        // Utilisez les paramètres pour remplir le contenu du PDF
        // Par exemple :
        $this->fpdf->SetFont('Arial', '', 10);
        foreach ($noms as $key => $nom) {
            $prenom = $prenoms[$key];
            $fonction = $fonctions[$key]; // Utiliser $fonctions à la place de $services si c'est correct
            $civilite = $civilites[$key];
            $service = $services[$key];

            // Convertir les caractères spéciaux si nécessaire
            $civilite = mb_convert_encoding($civilite, 'ISO-8859-1', 'UTF-8');
            $prenom = mb_convert_encoding($prenom, 'ISO-8859-1', 'UTF-8');
            $nom = mb_convert_encoding($nom, 'ISO-8859-1', 'UTF-8');

            // Ajouter une ligne dans le PDF avec les informations de l'émargement
            $this->fpdf->Cell(0, 10, $civilite . " " . $prenom . " " . $nom, 0, 1);
        }
    }
}
