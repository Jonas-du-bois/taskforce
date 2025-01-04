<?php

namespace M521\Taskforce\dbManager;

use \Exception;
use DateTime;

class Task
{
    private $id;
    private $titre;
    private $description;
    private $userIds = [];
    private $dateEcheance;
    private $statut;
    private $createdAt;

    public function __construct(
        string $titre,
        string $description = null,
        array $userIds = null,
        string $dateEcheance = null,
        string $statut = 'a_faire',
        int $id = 0,
    ) {
        if (empty($titre)) {
            throw new Exception('Il faut un titre pour la tâche.');
        }
        if (!in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
            throw new Exception('Le statut est invalide. Valeurs acceptées : a_faire, en_cours, termine.');
        }

        if (!empty($dateEcheance) && !$this->isValidDate($dateEcheance)) {
            throw new Exception('La date limite est invalide. Utilisez le format YYYY-MM-DD.');
        }

        $this->titre = $titre;
        $this->description = $description;
        $this->userIds = $userIds ?? [];
        $this->dateEcheance = $dateEcheance;
        $this->statut = $statut;
        $this->createdAt = (new DateTime())->format('Y-m-d H:i:s');
        $this->id = $id;
    }

    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    // Getters
    public function rendId(): int
    {
        return $this->id;
    }

    public function rendTitre(): string
    {
        return $this->titre;
    }

    public function rendDescription(): ?string
    {
        return $this->description;
    }

    public function rendUserIds(): array
    {
        return $this->userIds;
    }

    public function rendDateEcheance(): ?string
    {
        return $this->dateEcheance;
    }

    public function rendStatut(): string
    {
        return $this->statut;
    }

    public function rendCreatedAt(): string
    {
        return $this->createdAt;
    }

    // Méthodes pour formater les données
    public function getFormattedDateEcheance(): ?string
    {
        if ($this->dateEcheance) {
            $d = DateTime::createFromFormat('Y-m-d', $this->dateEcheance);
            return $d ? $d->format('d.m.Y') : null;
        }
        return null;
    }

    public function getFormattedStatut(): string
    {
        $statutTraduction = [
            'a_faire' => 'À faire',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
        ];
        return $statutTraduction[$this->statut] ?? 'Inconnu';
    }

    // Setters
        public function setTitre(string $titre): void
    {
        if (empty($titre)) {
            throw new Exception('Il faut un titre pour la tâche.');
        }
        $this->titre = $titre;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setDateEcheance(?string $dateEcheance): void
    {
        if (!empty($dateEcheance) && !$this->isValidDate($dateEcheance)) {
            throw new Exception('La date limite est invalide. Utilisez le format YYYY-MM-DD.');
        }
        $this->dateEcheance = $dateEcheance;
    }

    public function setStatut(string $statut): void
    {
        if (!in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
            throw new Exception('Le statut est invalide. Valeurs acceptées : a_faire, en_cours, termine.');
        }
        $this->statut = $statut;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
}
