<?php
namespace model;

class Partie {

    private $joueur1;
    private $joueur2;
    private $joueurCourant;
    private $plateau;

    public function debutPartie($joueur1, $joueur2){
        $this->joueur1 = $joueur1;
        $this->joueur2 = $joueur2;
        $this->plateau = new Plateau();
        $_SESSION["partie"] = serialize($this);
    }

    public function getPlateau(){
        return $this->plateau;
    }

}