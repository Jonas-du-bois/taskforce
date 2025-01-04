<?php

namespace M521\Taskforce\dbManager;

interface I_ApiCRUD {
    public function creeTable(): bool;

    //----------Users----------

    /**
     * Ajoute un utilisateur dans la base de données.
     * @param Users $users Instance de la classe Users à insérer.
     * @return int Identifiant de l'utilisateur inséré.
     */
    public function ajoutePersonne(Users $users): int;

    /**
     * Retourne une liste d'utilisateurs filtrée par nom.
     * @param string $nom Nom à rechercher.
     * @return array Tableau des utilisateurs correspondant.
     */
    public function rendPersonnes(string $nom): array;

    /**
     * Modifie les données d'un utilisateur spécifique.
     * @param int $id Identifiant de l'utilisateur.
     * @param Users $users Instance contenant les nouvelles données.
     * @return bool Retourne true si la modification a réussi, sinon false.
     */
    public function modifiePersonne(int $id, Users $user): bool;

    /**
     * Supprime un utilisateur.
     * @param int $id Identifiant de l'utilisateur.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimePersonne(int $id): bool;

    /**
     * Vérifie si les identifiants d'un utilisateur sont valides.
     * @param string $email Email de l'utilisateur.
     * @param string $motDePasse Mot de passe de l'utilisateur.
     * @return string Retourne un token si les identifiants sont valides.
     */
    public function verifierIdentifiants(string $email, string $motDePasse): string;

    /**
     * Compte le nombre total d'utilisateurs.
     * @return int Nombre d'utilisateurs.
     */
    public function compterNbUsers(): int;

    /**
     * Récupère un utilisateur par son token d'authentification.
     * @param string $token Token de l'utilisateur.
     * @return Users|null Retourne une instance de Users ou null si aucun utilisateur n'est trouvé.
     */
    public function getUserByToken($token): ?array;

    /**
     * Confirme l'inscription d'un utilisateur.
     * @param int $userId Identifiant de l'utilisateur.
     * @return bool Retourne true si la confirmation a réussi, sinon false.
     */
    public function confirmeInscription(int $userId): bool;

    //----------Tasks----------

    /**
     * Ajoute une tâche dans la base de données.
     * @param Task $tasks Instance de la classe Task à insérer.
     * @return int Identifiant de la tâche insérée.
     */
    public function createTask(Task $task): int;

    /**
     * Associe des utilisateurs à une tâche dans la table de jointure.
     * @param int $taskId ID de la tâche
     * @param array $userIds Liste des IDs des utilisateurs
     * @throws \Exception
     */
    public function assignUsersToTask(int $taskId, array $userIds): void;

    /**
     * Récupère une tâche en fonction de son ID.
     * @param int $taskId ID de la tâche
     * @return Task Objet Task récupéré
     * @throws \Exception Si la tâche n'existe pas
     */
    public function getTaskById(int $taskId): Task;

    /**
     * Met à jour une tâche existante.
     * @param Task $task Objet Task avec les nouvelles données
     * @throws \Exception Si la mise à jour échoue
     */
    public function updateTask(Task $task, int $taskId);

    /**
     * Supprime une tâche de la base de données.
     * @param int $taskId ID de la tâche à supprimer
     * @throws \Exception Si la suppression échoue
     */
    public function deleteTask(int $taskId): void;

    /**
     * Récupère toutes les tâches.
     * @return Task[] Tableau d'objets Task
     * @throws \Exception
     */
    public function getAllTasks(): array;

    public function getTasksByUserIdAndStatus($userId, $status);

}
