<?php
namespace model;

use \model\Cellule;

class Partie {
    /**
     * @var array Tableau de tableau. Le premier tableau contenant les tableaux de chaques colonnes.
     */
    private $plateau = [];

    public function setCellule($x, $y, $contenu){
        $this->plateau[$x][$y] = $contenu;
    }

    public function getCellule($x, $y){
        return $this->plateau[$x][$y];
    }

    public function __construct($j1, $j2){
        for($x = 0; $x < 5; $x++){
            $this->plateau[$x] = [];
            for($y = 0; $y < 5; $y++){
                $this->plateau[$x][$y] = new Cellule();

                // Placement des pions du joueur 1
                if($y == 0 || ($y == 1 && ($x == 0 || $x == 4)))
                    $this->plateau[$x][$y]->setJoueur($j1);

                // Placement des pions du joueur 2
                if($y == 4 || ($y == 3 && ($x == 0 || $x == 4)))
                    $this->plateau[$x][$y]->setJoueur($j2);
            }
        }
    }

    public function toHtml(){
        $str = "<table>";
        for($y = 0; $y < 5; $y++){
            $str .= "<tr>";
            for($x = 0; $x < 5; $x++){
                $str .= "<td>";
                $str .= $this->getCellule($x, $y)->toHtml();
                $str .= "</td>";
            }
            $str .= "</tr>";
        }
        $str .= "</table>";

        return $str;
    }
}