<?php
namespace M521\Taskforce\dbManager;

use \Exception;

/**
 * Permet de simuler une personne ayant :
 *  - un id (facultatif)
 *  - un nom
 *  - un prénom
 *  - un email
 *  - un numéro de téléphone
 */
class Users {

    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $noTel;
    private $motDePasse;
    private $token;

    /**
     * Construit une nouvelle personne avec les paramètres spécifiés
     * @param int $prenom Prénom
     * @param string $nom Nom
     * @param string $email Email
     * @param string $noTel noTel
     * @param string $id Identifiant de la personne
     * @throws Exception Lance une expection si un des paramètres n'est pas spécifié
     */
    public function __construct(string $prenom, string $nom, string $email, string $noTel, string $motDePasse ,int $id = 0) {
        if (empty($prenom)) {
            throw new Exception('Il faut un prénom');
        }
        if (empty($nom)) {
            throw new Exception('Il faut un nom');
        }
        if (empty($email)) {
            throw new Exception('Il faut un email');
        }
        if (empty($noTel)) {
            throw new Exception('Il faut un numéro de téléphone');
        }
        if ($id < 0) {
            throw new Exception('Il faut un id valide');
        }
        if (empty($motDePasse)) {
            throw new Exception('Il faut un mot de passe valide');
        }

        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->email = $email;
        $this->noTel = $noTel;
        $this->id = $id;
        $this->motDePasse = $motDePasse;
        $this->token = bin2hex(random_bytes(16));
    }

    public function rendToken(): string {
        return $this->token;
    }
    /**
     * Rend l'id de la personne
     * @return int L'identifiant
     */
    public function rendId(): int {
        return (int)$this->id;
    }

    /**
     * Defini l'id de la personne
      @param int $id Identifiant de la personne
     */
    public function definiId($id): void {
        if ($id > 0) {
            $this->id = $id;
        }
    }

    /**
     * Rend le prénom
     * @return string Le prénom
     */
    public function rendPrenom(): string {
        return $this->prenom;
    }
    
    /**
     * Permet de changer le prénom
     * @param string $nouveauPrenom Nouveau prénom
     */
    public function changePrenom(string $nouveauPrenom) {
        if (!empty($nouveauPrenom)) {
            $this->prenom = $nouveauPrenom;
        }
    }

    /**
     * Rend le nom
     * @return string Le nom
     */
    public function rendNom(): string {
        return $this->nom;
    }
    
    /**
     * Permet de changer le nom
     * @param string $nouveauNom Nouveau nom
     */
    public function changeNom(string $nouveauNom) {
        if (!empty($nouveauNom)) {
            $this->nom = $nouveauNom;
        }
    }

    /**
     * Rend l'email
     * @return string L'email
     */
    public function rendEmail(): string {
        return $this->email;
    }
    
    /**
     * Permet de changer l'email
     * @param string $nouveauEmail Nouveau email
     */
    public function changeEmail(string $nouveauEmail) {
        if (!empty($nouveauEmail)) {
            $this->email = $nouveauEmail;
        }
    }

    /**
     * Rend le numéro de téléphone
     * @return string Le numéro de téléphone
     */
    public function rendNoTel(): string {
        return $this->noTel;
    }
    
    /**
     * Permet de changer le numero de téléphone
     * @param string $nouveauNoTel Nouveau numéro de téléphone
     */
    public function changeNoTel(string $nouveauNoTel) {
        if (!empty($nouveauNoTel)) {
            $this->noTel = $nouveauNoTel;
        }
    }
    
    /**
     * Rend le mot de passe
     * @return string Le mot de passe
     */
    public function rendMotDePasse() {
        return $this->motDePasse; 
    }

    /**
     * Rend une description complète de la personne
     * @return string La description de la personne
     */
    public function __toString(): string {
        return $this->id . " " .
                $this->prenom . " " .
                $this->nom . " " .
                $this->email . " " .
                $this->noTel . '<br>';
    }

}
