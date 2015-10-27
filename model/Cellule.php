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
}