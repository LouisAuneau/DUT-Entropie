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

    private function getCelluleSuivanteDisponible($direction, $cellulesPrecedantes){
        $celluleSuivante = $this->getCelluleSuivante($direction);
        if(is_null($celluleSuivante) || $celluleSuivante->getJoueur() != null){
            array_push($cellulesPrecedantes, $this);
            return $cellulesPrecedantes;
        }
        else {
            array_push($cellulesPrecedantes, $this);
            return $celluleSuivante->getCelluleSuivanteDisponible($direction, $cellulesPrecedantes);
        }
    }

    public function getCellulesSuivantesDisponibles(){
        $cellulesSuivantesDisponibles = [];

        // On ajoute les cases suivantes dans chaque direction.
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("no", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("n", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("ne", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("e", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("se", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("s", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("so", []));
        $cellulesSuivantesDisponibles =array_merge($cellulesSuivantesDisponibles, $this->getCelluleSuivanteDisponible("o", []));

        // On ne tient pas compte dans le cas ou la case suivante est cette propre case.
        foreach($cellulesSuivantesDisponibles as $key => $celluleSuivante){
            if($celluleSuivante == $this)
                unset($cellulesSuivantesDisponibles[$key]);
        }

        return $cellulesSuivantesDisponibles;
    }

    public function deplacable(){
        $partie = unserialize($_SESSION["partie"]);

        // Si la case n'appartient à aucun joueur (il n'y a pas de pion dessus), on ne peut la déplacer.
        if($this->joueur == null)
            return false;

        // Si la case appartient au joueur adverse, on ne peut la déplacer.
        if($this->joueur != $partie->getJoueurCourant())
            return false;

        // Si on a aucune case disponible pour déplacer le pion, on ne le déplace pas
        if(empty($this->getCellulesSuivantesDisponibles())){
            return false;
        }


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
            if($etape == 2 && in_array($this, $partie->getCelluleADeplacer()->getCellulesSuivantesDisponibles()))
                return "<a class='pion possible' href='?etape=2&x=".$this->x."&y=".$this->y."' style='background-color:".$joueurCourant->getCouleur()."'></a>";
            else
                return "";
        }

        // Si il y a un joueur sur la case
        else{
            if($etape == 1 && $this->deplacable())
                return "<a class='pion' href='?etape=".$etape."&x=".$this->x."&y=".$this->y."' style='background-color:".$this->joueur->getCouleur()."'></a>";
            else if($etape == 2 && $this == $partie->getCelluleADeplacer())
                return "<div class='pion deplacement' style='background-color:".$this->joueur->getCouleur()."'></a>";
            else
                return "<div class='pion' style='background-color:".$this->joueur->getCouleur()."'></div>";
        }
    }
}