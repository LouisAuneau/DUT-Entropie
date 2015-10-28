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

    public function sauvegarder(){
        $_SESSION["partie"] = serialize($this);
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

    public function gagne(){
        $joueur1Gagne = true;
        foreach($this->getPlateau()->getCellules() as $cellule){

        }
    }
}