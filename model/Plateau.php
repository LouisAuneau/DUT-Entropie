<?php
namespace model;


class Plateau {
    private $cellules = [];

    public function __construct(){
        for($x = 0; $x < 5; $x++){
            for($y = 0; $y < 5; $y++){
                $this->cellules[$x][$y] = new Cellule($x, $y);
            }
        }
    }

    public function getCellules(){
        return $this->cellules;
    }

    public function getCellule($x, $y){
        if($x >= 0 && $x < 5 && $y >= 0 && $y < 5)
            return $this->cellules[$x][$y];
        else
            return null;
    }
}