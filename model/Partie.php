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
     * Mutateur donnant l'état précedant de la partie.
     */
    public function setEtatPrecedant(){
        $this->etatPrecedant = serialize($this);
    }


    /**
     * Permet de faire un retour en arrière dans la partie. La réassignation interne à la classe étant interdire, on retourn l'état précèdant qui devra être sauvegarder.
     * @return Partie Même partie mais à l'état précedant.
     */
    public function retour(){
        return unserialize($this->etatPrecedant);
    }


    /**
     * Permet de sauvegarder la partie à tout instant dans la session pour la recharger plus tard. L'utilisation de la sérialisation (voir serialize() dans la doc PHP) permet de tout conserver sans aucune perte.
     */
    public function sauvegarder(){
        $_SESSION["partie"] = serialize($this);
    }


    /**
     * Permet de quitter la partie à tout moment en supprimant la sauvegarde faite dans les sessions.
     */
    public function quitter(){
        unset($_SESSION["partie"]);
    }


    /**
     * Accesseur permettant de récupérer l'étape courante de la partie.
     * @return int 1 si le joueur choisi un pion à déplacer. 2 si il choisi l'endroit où le déplacer.
     */
    public function getEtape(){
        return $this->etape;
    }


    /**
     * Mutateur de l'étape de la partie.
     * @param int $etape 1 si le joueur va choisir un pion à déplcer, 2 si il va choisir où le déplacer.
     */
    public function setEtape($etape){
        $this->etape = $etape;
    }


    /**
     * Accesseur pour récupérer le joueur en train de jouer.
     * @return Joueur Joueur en train de jouer.
     */
    public function getJoueurCourant(){
        return $this->joueurCourant;
    }


    /**
     * Permet de changer le joueur courant, entre deux tours notamment. Si le joueur 1 jouait, on passe au 2 et vice-versa.
     */
    public function changerJoueurCourant(){
        if($this->joueurCourant == $this->joueur1)
            $this->joueurCourant = $this->joueur2;
        else
            $this->joueurCourant = $this->joueur1;
    }


    /**
     * Mutateur pour désigner une cellule qui sera déplacée à l'étape 2.
     * @param Cellule $cellule Cellule à déplacer.
     */
    public function setCelluleADeplacer(Cellule $cellule){
        $this->celluleADeplacer = $cellule;
    }


    /**
     * Accesseur pour récupérer la cellule qui sera déplacée lors de l'étape 2.
     * @return Cellule Cellule qui va être déplacée.
     */
    public function getCelluleADeplacer(){
        return $this->celluleADeplacer;
    }


    /**
     * Permet de savoir si la partie est gagnée ou non et par qui.
     * @return bool|Joueur Faux si la partie n'est pas gagnée, le joueur gagnant sinon.
     */
    public function gagnee(){
        $joueur1Gagne = true;
        $joueur2Gagne = true;

        // On parcours les cellules du plateau.
        for($x = 0; $x < 5; $x++){
            for($y = 0; $y < 5; $y++) {
                $cellule = $this->getPlateau()->getCellule($x, $y);
                // Si une cellule d'un des deux joueur n'est pas gagnante, il n'est pas gagnant.
                if ($cellule->getJoueur() == $this->joueur1 && !$cellule->gagnante())
                    $joueur1Gagne = false;
                if ($cellule->getJoueur() == $this->joueur2 && !$cellule->gagnante())
                    $joueur2Gagne = false;
            }
        }

        if($joueur1Gagne && $joueur2Gagne) // Si il y a égalité, c'est le joueur courant qui gagne car il vient de faire le déplacement.
            return $this->joueurCourant;
        else if($joueur1Gagne)
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