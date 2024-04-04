<?php

namespace App\Model;

class Emargement
{
    protected string $nom;
    protected string $prenom;
    protected string $fonction;
    protected string $titre;
    protected string $date;
    protected string $service;
    protected string $civilite;

// Les getters et les setters pour les propriétés nom, prenom et fonction sont identiques à ceux de la classe Chevalet

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): void
    {
        $this->service = $service;
    }

    public function getCivilite(): string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): void
    {
        $this->civilite = $civilite;
    }

    public function getFonction(): string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): void
    {
        $this->fonction = $fonction;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
}