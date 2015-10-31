<?php
namespace model;

class Partie {
    /**
     * @var Joueur $joueur1 Joueur 1 de la partie.
     */
    private $joueur1;

    /**
     * @var Joueur $joueur2 Joueur 2 de la partie.
     */
    private $joueur2;

    /**
     * @var Joueur $joueurCourant Joueur qui est en train de jouer.
     */
    private $joueurCourant;

    /**
     * @var int $etape Étape courante du jeu : 1 si le joueur choisi le pion à déplacer, 2 si il choisi où le déplacer.
     */
    private $etape;

    /**
     * @var Plateau $plateau Plateau du jeu.
     */
    private $plateau;

    /**
     * @var Cellule $celluleADeplacer Cellule choisie pour être déplacée à l'étape 1. Null au premier tour de jeu.
     */
    private $celluleADeplacer;

    /**
     * @var string $etatPrecedant État précèdent de l'objet, sérialisé (voir méthode serialize() de PHP) sous forme de chaîne de caractère. Sert si le joueur veut revenir en arrière.
     */
    private $etatPrecedant;


    // -------------------------------------------------------------------------------------------------


    /**
     * Constructeur de partie.
     * @param Joueur $joueur1 Joueur 1 de la partie. Il commencera à jouer.
     * @param Joueur $joueur2 Joueur 2 de la partie.
     */
    public function __construct($joueur1, $joueur2) {
        $this->joueur1 = $joueur1;
        $this->joueur2 = $joueur2;
        $this->joueurCourant = $joueur1; // Le joueur 1 commence à joueur.
        $this->etape = 1; // On commence par l'étape 1 : le choix d'un pion à déplacer.
        $this->plateau = new Plateau($joueur1, $joueur2); // On créer le plateau de jeu.
        $this->etatPrecedant = serialize($this); // On initialise le premier état.
    }


    /**
     * Accesseur du plateau de la partie.
     * @return Plateau Retourne le plateau de la partie.
     */
    public function getPlateau() {
        return $this->plateau;
    }


    /**
     * Méthode static permettant de charger la partie depuis la session si une partie est en cours.
     * @return Partie|null Retourne la partie si une est en cours, null sinon.
     */
    public static function charger(){
        if(isset($_SESSION["partie"]))
            return unserialize($_SESSION["partie"]);
        else
            return null;
    }


    /**
     * Mutateur de l'état précedant de la partie.
     */
    public function setEtatPrecedant(){
        $this->etatPrecedant = serialize($this);
    }

    public function retour(){
        return unserialize($this->etatPrecedant);
    }

    public function sauvegarder(){
        $_SESSION["partie"] = serialize($this);
    }

    public function quitter(){
        unset($_SESSION["partie"]);
    }

    public function getEtape(){
        return $this->etape;
    }

    public function setEtape($etape){
        $this->etape = $etape;
    }

    public function getJoueurCourant(){
        return $this->joueurCourant;
    }

    public function changerJoueurCourant(){
        if($this->joueurCourant == $this->joueur1)
            $this->joueurCourant = $this->joueur2;
        else
            $this->joueurCourant = $this->joueur1;
    }

    public function setCelluleADeplacer($cellule){
        $this->celluleADeplacer = $cellule;
    }

    public function getCelluleADeplacer(){
        return $this->celluleADeplacer;
    }

    public function gagnee(){
        $joueur1Gagne = true;
        $joueur2Gagne = true;

        for($x = 0; $x < 5; $x++){
            for($y = 0; $y < 5; $y++) {
                $cellule = $this->getPlateau()->getCellule($x, $y);
                if ($cellule->getJoueur() == $this->joueur1 && !$cellule->gagnante())
                    $joueur1Gagne = false;
                if ($cellule->getJoueur() == $this->joueur2 && !$cellule->gagnante())
                    $joueur2Gagne = false;
            }
        }

        if($joueur1Gagne)
            return $this->joueur1;
        else if($joueur2Gagne)
            return $this->joueur2;
        else
            return false;
    }

    /**
     * Pour récupérer la liste des pions isolés d'un joueur. Un pion isolé est un pion n'ayant aucun pion accolé à lui.
     * @param Joueur $joueur Joueur dont on veut récupérer les pions isolés.
     * @return array Tableau de Cellules contenant les pions isolés.
     */
    public function pionsIsoles(Joueur $joueur){
        $pionsIsoles = [];

        // On parcours toutes les cellules
        for($x = 0; $x < 5; $x++){
            for($y = 0; $y < 5; $y++) {
                $cellule = $this->getPlateau()->getCellule($x, $y);
                // Si la cellule appartient au joueur demandé, et qu'elle est isolée, on l'ajoute.
                if ($cellule->getJoueur() == $joueur && $cellule->isolee())
                    array_push($pionsIsoles, $cellule);
            }
        }

        return $pionsIsoles;
    }
}