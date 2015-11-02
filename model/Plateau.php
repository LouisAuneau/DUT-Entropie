<?php
namespace model;


class Plateau {
    /**
     * @var array $cellules Cellules du plateau. Tableau à deux dimension. La première représente l'axe X, la deuxième l'axe Y ($cellules[X][Y]).
     */
    private $cellules = [];


    // -------------------------------------------------------------------------------------------------


    /**
     * Con,structeur du plateau. Initialise toutes les cellules et place les pions des joueurs 1 et 2 à leur position au départ du jeu.
     * @param Joueur $joueur1 Joueur 1 du jeu, placé en haut du plateau au départ, commence la partie.
     * @param Joueur $joueur2 Joueur 2 du jeu, placé en bas du plateau au départ.
     */
    public function __construct(Joueur $joueur1, Joueur $joueur2){
        // On créer 25 cellules, 5 en X par 5 en Y.
        for($x = 0; $x < 5; $x++){
            for($y = 0; $y < 5; $y++){
                $this->cellules[$x][$y] = new Cellule($x, $y);

                // Ajout des pions du joueur 1 à leurs position initiales.
                if($y == 0 || ($y == 1 && ($x == 0 || $x == 4)))
                    $this->cellules[$x][$y]->setJoueur($joueur1);

                // Ajout des pions du joueur 2 à leurs position initiales.
                if($y == 4 || ($y == 3 && ($x == 0 || $x == 4)))
                    $this->cellules[$x][$y]->setJoueur($joueur2);
            }
        }
    }

    /**
     * Permet de récupérer une cellule du plateau selon ses coordonnées.
     * @param int $x Coordonnée en X de la cellule (horizontale).
     * @param int $y Coordonnée en Y de la cellule (verticale).
     * @return Cellule|null Retourne la cellule demandé ou null si les coordonnées sont incorrectes.
     */
    public function getCellule($x, $y){
        if($x >= 0 && $x < 5 && $y >= 0 && $y < 5) // Il faut que les deux coordonnées soient comprises entre 0 et 5.
            return $this->cellules[$x][$y];
        else
            return null;
    }


    /**
     * Permet de récupérer le plateau en HTML prêt pour l'affichage.
     * @return string Le plateau au format HTML.
     */
    public function toHtml(){
        $str = '<table>';
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
        $str .= "</table>";
        return $str;
    }
}