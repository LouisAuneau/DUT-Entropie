<?php
namespace model;


class Cellule {
    private $joueur;
    private $x;
    private $y;

    public function __construct($x, $y){
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(){
        return $this->x;
    }

    public function getY(){
        return $this->y;
    }

    public function getJoueur(){
        return $this->joueur;
    }

    public function setJoueur($joueur){
        $this->joueur = $joueur;
    }

    public function getCelluleSuivante($direction){
        if(isset($_SESSION["partie"])){
            $plateau = unserialize($_SESSION["partie"])->getPlateau();
            switch ($direction){
                case "no":
                    return $plateau->getCellule($this->x - 1, $this->y - 1);
                    break;
                case "n" :
                    return $plateau->getCellule($this->x, $this->y - 1);
                    break;
                case "ne" :
                    return $plateau->getCellule($this->x + 1, $this->y - 1);
                    break;
                case "e" :
                    return $plateau->getCellule($this->x + 1, $this->y);
                    break;
                case "se" :
                    return $plateau->getCellule($this->x + 1, $this->y + 1);
                    break;
                case "s" :
                    return $plateau->getCellule($this->x, $this->y + 1);
                    break;
                case "so" :
                    return $plateau->getCellule($this->x - 1, $this->y + 1);
                    break;
                case "o" :
                    return $plateau->getCellule($this->x - 1, $this->y);
                    break;
                default:
                    return null;
                    break;
            }
        } else{
            return null;
        }
    }

    public function deplacable(){
        $partie = unserialize($_SESSION["partie"]);

        if($this->joueur == null)
            return false;

        if($this->joueur != $partie->getJoueurCourant())
            return false;

        // On vérifie que le pion a au moins un poin voisin du même joueur pour pouvoir être déplacé.
        for($x = -1; $x <= 1; $x++){
            for($y = -1; $y <= 1; $y++){
                $cellule = $partie->getPlateau()->getCellule($this->x + $x, $this->y + $y);
                if($cellule != $this && $cellule != null && $cellule->getJoueur() == $this->joueur) // On ne prend pas en compte si la cellule examiné est cette cellule (x et y = 0) ou si elle est hors plateau (null).
                    return true;
            }
        }

        return false;
    }

    public function toHtml(){
        $partie = unserialize($_SESSION["partie"]);
        $etape = $partie->getEtape(); // L'affichage dépend de l'étape
        $joueurCourant = $partie->getJoueurCourant();

        // Si il n'y pas de joueur sur la case
        if(is_null($this->joueur)){
            // Si l'étape est la 1, on a rien à afficher, si c'est la 2, on affiche les cases où le déplacement est possible.
            if($etape == 2)
                return "<a class='pion possible' href='?etape=2' style='background-color:".$joueurCourant->getCouleur()."'></a>";
            else
                return "";
        }

        // Si il y a un joueur sur la case
        else{
            if($etape == 1 && $this->deplacable())
                return "<a class='pion' href='?etape=".$etape."&x=".$this->x."&y=".$this->y."' style='background-color:".$this->joueur->getCouleur()."'></a>";
            else if($etape == 2 && $this == $partie->getCelluleADeplacer())
                return "<a class='pion deplacement' href='?etape=".$etape."&x=".$this->x."&y=".$this->y."' style='background-color:".$this->joueur->getCouleur()."'></a>";
            else
                return "<div class='pion' style='background-color:".$this->joueur->getCouleur()."'></div>";
        }
    }
}