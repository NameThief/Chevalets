<?php

namespace App\Model;

class Personne
{
    private string $nom;
    private string $prenom;
    private string $fonction;
    private string $civilite;
    private string $service;
    private string $email;

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getFonction(): string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): void
    {
        $this->fonction = $fonction;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCivilite(): string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): void
    {
        $this->civilite = $civilite;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): void
    {
        $this->service = $service;
    }

}