<?php
namespace model;

class Partie {

    private $joueur1;
    private $joueur2;
    private $joueurCourant;
    private $etape;
    private $plateau;
    private $celluleADeplacer;

    public function __construct($joueur1, $joueur2) {
        $this->joueur1 = $joueur1;
        $this->joueur2 = $joueur2;
        $this->joueurCourant = $joueur1; // Le joueur 1 commence à joueur.
        $this->etape = 1; // On commence par l'étape 1 : le choix d'un pion a déplacer.
        $this->plateau = new Plateau($joueur1, $joueur2);
    }

    public function getPlateau() {
        return $this->plateau;
    }

    public static function charger(){
        if(isset($_SESSION["partie"]))
            return unserialize($_SESSION["partie"]);
        else
            return null;
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
                if ($cellule->getJoueur() == $this->joueur1 && $cellule->deplacable())
                    $joueur1Gagne = false;
                if ($cellule->getJoueur() == $this->joueur2 && $cellule->deplacable())
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
}