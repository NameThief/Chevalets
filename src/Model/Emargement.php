<?php

namespace App\Model;

class Emargement
{
    private string $objet;
    private string $date;
    private string $heureDebut;
    private string $heureFin;
    private array $animateurs;
    private array $participants;

    public function getObjet(): string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): void
    {
        $this->objet = $objet;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getHeureFin(): string
    {
        return $this->heureFin;
    }

    public function setHeureFin(string $heureFin): void
    {
        $this->heureFin = $heureFin;
    }

    public function getHeureDebut(): string
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(string $heureDebut): void
    {
        $this->heureDebut = $heureDebut;
    }

    public function getAnimateurs(): array
    {
        return $this->animateurs;
    }

    public function setAnimateurs(array $animateurs): void
    {
        $this->animateurs = $animateurs;
    }

    public function addAnimateur(Personne $personne) {
        $this->animateurs[] = $personne;
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function setParticipants(array $participants): void
    {
        $this->participants = $participants;
    }
    public function addParticipant(Personne $personne) {
        $this->participants[] = $personne;
    }


}
