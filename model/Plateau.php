<?php
namespace model;


class Plateau {
    private $cellules = [];

    public function __construct($joueur1, $joueur2){
        for($x = 0; $x < 5; $x++){
            for($y = 0; $y < 5; $y++){
                $this->cellules[$x][$y] = new Cellule($x, $y);

                // Ajout des pions du joueur 1
                if($y == 0 || ($y == 1 && ($x == 0 || $x == 4)))
                    $this->cellules[$x][$y]->setJoueur($joueur1);

                // Ajout des pions du joueur 2
                if($y == 4 || ($y == 3 && ($x == 0 || $x == 4)))
                    $this->cellules[$x][$y]->setJoueur($joueur2);
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

    public function toHtml(){
        $str = '<table><form method="get" action="">';
            for($y = 0; $y < 5; $y++){
                $str .= "<tr>";
                for($x = 0; $x < 5; $x++){
                    $str .= "<td";

                    if(($x+$y)%2 == 0)
                        $str .= ' class="pair">';
                    else
                        $str .= ">";

                    $str .= $this->cellules[$x][$y]->toHtml();
                    $str .= "</td>";
                }
                $str .= "</tr>";
            }
        $str .= "</form></table>";
        return $str;
    }
}