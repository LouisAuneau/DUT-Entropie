<?php
namespace model;


class Cellule {
    /**
     * @var Joueur Joueur à qui appartient la case. Si la case n'appartient à aucun joueur, sa valeur est null.
     */
    private $joueur;
    /**
     * @var boolean Booléen qui dit si oui ou non le joueur pourra cliquer sur cette case pour le tour en question. cette valeur est décidée par la classe Partie.
     */
    private $jouable;

    /**
     * @param Joueur $joueur
     * @param boolean $jouable
     */
    public function __construct($joueur = null, $jouable = false){
        $this->joueur = $joueur;
    }

    public function getJoueur(){ return $this->joueur; }
    public function setJoueur($joueur){ $this->joueur = $joueur; }

    public function toHtml(){
        if(!is_null($this->joueur)) {
            $str = "<div class=\"pion";
            if($this->jouable)
                $str.= " active";
            $str .= "\" style=\"background-color : " . $this->joueur->getCouleur() . "\"></div>";
            return $str;
        }
        else
            return " ";
    }
}