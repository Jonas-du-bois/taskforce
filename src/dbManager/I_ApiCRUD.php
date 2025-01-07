<?php

namespace M521\Taskforce\dbManager;

interface I_ApiCRUD
{
    // ---------- Méthodes pour les utilisateurs ----------

    /**
     * Ajoute un nouvel utilisateur.
     * @param Users $user L'objet utilisateur à ajouter.
     * @return int L'ID de l'utilisateur ajouté.
     */
    public function addUser(Users $user): int;

    /**
     * Met à jour un utilisateur existant.
     * @param int $id L'ID de l'utilisateur à mettre à jour.
     * @param Users $user L'objet utilisateur avec les nouvelles données.
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function updateUser(int $id, Users $user): bool;

    /**
     * Récupère un utilisateur par son email.
     * @param string $email L'email de l'utilisateur à récupérer.
     * @return array Un tableau d'objets Users correspondant aux utilisateurs trouvés.
     */
    public function getUser(string $email): array;

    /**
     * Récupère un utilisateur par son ID.
     * @param int $userId L'ID de l'utilisateur à récupérer.
     * @return array Un tableau d'objets Users correspondant aux utilisateurs trouvés.
     */
    public function getUserById(int $userId): array;

    /**
     * Récupère tous les utilisateurs.
     * @return array Un tableau d'objets Users correspondant aux utilisateurs trouvés.
     */
    public function getAllUsers(): array;

    /**
     * Supprime un utilisateur par son ID.
     * @param int $id L'ID de l'utilisateur à supprimer.
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function deleteUser(int $id): bool;

    /**
     * Vérifie les identifiants d'un utilisateur.
     * @param string $email L'email de l'utilisateur.
     * @param string $motDePasse Le mot de passe de l'utilisateur.
     * @return string Le résultat de la vérification ('success', 'wrong_password', 'email_not_found', 'not_confirmed').
     */
    public function verifyCredentials(string $email, string $motDePasse): string;

    /**
     * Compte le nombre total d'utilisateurs.
     * @return int Le nombre total d'utilisateurs.
     */
    public function countUsers(): int;

    /**
     * Récupère un utilisateur par son token.
     * @param string $token Le token de l'utilisateur.
     * @return array|null Un tableau d'objets Users correspondant à l'utilisateur trouvé ou null si aucun utilisateur n'est trouvé.
     */
    public function getUserByToken($token): ?array;

    /**
     * Confirme l'inscription d'un utilisateur.
     * @param int $userId L'ID de l'utilisateur à confirmer.
     * @return bool True si la confirmation a réussi, false sinon.
     */
    public function confirmRegistration(int $userId): bool;

    // ---------- Méthodes pour les tâches ----------

    /**
     * Crée une nouvelle tâche.
     * @param Task $task L'objet tâche à créer.
     * @return int L'ID de la tâche créée.
     */
    public function createTask(Task $task): int;

    /**
     * Associe des utilisateurs à une tâche.
     * @param int $taskId L'ID de la tâche.
     * @param array $userIds Un tableau des IDs des utilisateurs à associer.
     */
    public function assignUsersToTask(int $taskId, array $userIds): void;

    /**
     * Désassocie des utilisateurs d'une tâche.
     * @param int $taskId L'ID de la tâche.
     * @param array $userIds Un tableau des IDs des utilisateurs à désassocier.
     */
    public function unassignUsersFromTask(int $taskId, array $userIds): void;

    /**
     * Récupère une tâche par son ID.
     * @param int $taskId L'ID de la tâche à récupérer.
     * @return Task L'objet tâche correspondant à l'ID fourni.
     */
    public function getTaskById(int $taskId): Task;

    /**
     * Met à jour une tâche existante.
     * @param Task $task L'objet tâche avec les nouvelles données.
     * @param int $taskId L'ID de la tâche à mettre à jour.
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function updateTask(Task $task, int $taskId);

    /**
     * Supprime une tâche par son ID.
     * @param int $taskId L'ID de la tâche à supprimer.
     */
    public function deleteTask(int $taskId): void;

    /**
     * Récupère toutes les tâches.
     * @return array Un tableau d'objets Task correspondant aux tâches trouvées.
     */
    public function getAllTasks(): array;

    /**
     * Récupère toutes les tâches associées à un utilisateur donné.
     * @param int $userId L'ID de l'utilisateur.
     * @return array Un tableau d'objets Task correspondant aux tâches trouvées.
     */
    public function getTasksByUserId(int $userId): array;

    /**
     * Récupère les tâches associées à un utilisateur selon leur statut.
     * @param int $userId L'ID de l'utilisateur.
     * @param string $status Le statut des tâches (à faire, en cours, terminé).
     * @return array Un tableau d'objets Task correspondant aux tâches trouvées.
     */
    public function getTasksByUserIdAndStatus($userId, $status);

    /**
     * Récupère les tâches partagées d'un utilisateur.
     * @param int $userId L'ID de l'utilisateur.
     * @return array Un tableau contenant les informations des tâches partagées.
     */
    public function getTasksSharedByUserId(int $userId): array;

    /**
     * Récupère les tâches d'un utilisateur donné et les trie selon une colonne spécifiée.
     * @param int $userId L'ID de l'utilisateur.
     * @param string $sortColumn La colonne par laquelle trier les résultats.
     * @param string $order L'ordre de tri (ASC ou DESC).
     * @return array Un tableau d'objets Task correspondant aux tâches trouvées.
     */
    public function getTasksByUserIdSorted(int $userId, string $sortColumn = 'titre', string $order = 'ASC'): array;

    /**
     * Récupère les utilisateurs non assignés à une tâche donnée.
     * @param int $taskId L'ID de la tâche.
     * @return array Un tableau associatif contenant les informations des utilisateurs non assignés à la tâche.
     */
    public function getUsersNotAssignedToTask(int $taskId): array;

    /**
     * Récupère les utilisateurs assignés à une tâche donnée.
     * @param int $taskId L'ID de la tâche.
     * @return array Un tableau associatif contenant les informations des utilisateurs assignés à la tâche.
     */
    public function getUsersAssignedToTask(int $taskId): array;

    /**
     * Recherche des tâches pour un utilisateur donné.
     * @param string $query La chaîne de recherche pour filtrer les tâches par titre ou description.
     * @param int $userId L'ID de l'utilisateur.
     * @return array Un tableau d'objets Task correspondant aux tâches trouvées.
     */
    public function searchTasks(string $query, int $userId): array;

    /**
     * Recherche des tâches triées pour un utilisateur donné.
     * @param string $query La chaîne de recherche pour filtrer les tâches par titre ou description.
     * @param int $userId L'ID de l'utilisateur.
     * @param string $sortColumn La colonne par laquelle trier les résultats.
     * @param string $order L'ordre de tri (ASC ou DESC).
     * @return array Un tableau d'objets Task correspondant aux tâches trouvées.
     */
    public function searchTasksSorted(string $query, int $userId, string $sortColumn = 'dateEcheance', string $order = 'ASC'): array;
}