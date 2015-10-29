<?php
namespace Controller;


use model\Joueur;
use model\Partie;
use views\View;

class EntropieController {
    private static $partie;

    public static function demarrer(){
        self::$partie = Partie::charger();

        self::debut();
        self::quitter();
        self::retour();
        self::avancer();
    }

    private static function retour(){
        if(!is_null(self::$partie)){
            if(isset($_GET["retour"])){
                self::$partie = self::$partie->retour();
                self::$partie->sauvegarder();
                header("Location: index.php");
            }
        }
    }

    private static function debut(){
        if(is_null(self::$partie)){
            if(isset($_GET["joueur1"]) && isset($_GET["joueur2"]) && isset($_GET["couleurJoueur1"]) && isset($_GET["couleurJoueur2"])
                && $_GET["joueur1"] != $_GET["joueur2"]
                && $_GET["couleurJoueur1"] != $_GET["couleurJoueur2"]
            ){
                $joueur1 = new Joueur($_GET["joueur1"], $_GET["couleurJoueur1"]);
                $joueur2 = new Joueur($_GET["joueur2"], $_GET["couleurJoueur2"]);
                $partie = new Partie($joueur1, $joueur2);
                $partie->sauvegarder();
                header("Refresh:0");
            } else{
                View::affichage("CreerPartie");
            }
        }
    }

    private static function gagnee(){
        if(!is_null(self::$partie)) {
            if(isset($_GET["gagnee"]) && self::$partie->gagnee() != false){
                $donnees["gagnant"] = self::$partie->gagnee()->getPrenom();
                $donnees["couleur"] = self::$partie->gagnee()->getCouleur();
                View::affichage("Gagne", $donnees);
                self::$partie->quitter();
                die(); // On s'arrête ici pour que le traitement des déplacements ne soit pas fait.
            }
            else if(self::$partie->gagnee() != false)
                header('Location: index.php?gagnee=1');
        }
    }

    private static function avancer(){
        if(!is_null(self::$partie)) {
            // On sauvegarde l'état du jeu avant d'avancer la partie
            self::$partie->setEtatPrecedant();

            // On effectue le mouvement demandé si il est conforme au modele stocké
            if(isset($_GET["etape"]) && isset($_GET["x"]) && isset($_GET["y"])){ // Si un mouvement est demandé
                if($_GET["etape"] == self::$partie->getEtape() && self::$partie->getPlateau()->getCellule($_GET["x"], $_GET["y"]) != null){ // L'étape demandé est bien l'étape en cours dans le modèle et la cellule demandé est dans le plateau

                    // Traitement pour l'étape 1
                    if($_GET["etape"] == 1){
                        $celluleADeplacer = self::$partie->getPlateau()->getCellule($_GET["x"], $_GET["y"]);
                        if($celluleADeplacer->getJoueur() == self::$partie->getJoueurCourant() && $celluleADeplacer->deplacable()){
                            self::$partie->setCelluleADeplacer($celluleADeplacer);
                            self::$partie->setEtape(2);
                            self::$partie->sauvegarder();
                        }
                    }

                    // Traitement pour l'étape 2
                    else if($_GET["etape"] == 2){
                        $celluleCible = self::$partie->getPlateau()->getCellule($_GET["x"], $_GET["y"]);
                        if(in_array($celluleCible, self::$partie->getCelluleADeplacer()->getCellulesSuivantesDisponibles())){
                            self::$partie->getCelluleADeplacer()->setJoueur(null);
                            $celluleCible->setJoueur(self::$partie->getJoueurCourant());
                            self::$partie->changerJoueurCourant();
                            self::$partie->setEtape(1);
                            self::$partie->sauvegarder();
                        }
                    }

                }
            }

            // On vérifie si il y a victoire, car ca stoppera la partie et n'affichera pas la grille
            self::gagnee();

            // On affiche le plateau et les infos
            $donnees["plateau"] = self::$partie->getPlateau()->toHtml();
            $donnees["joueurCourant"] = self::$partie->getJoueurCourant()->getPrenom();
            View::affichage("Plateau", $donnees);
        }
    }

    public static function quitter(){
        if(!is_null(self::$partie)){
            if(isset($_GET["quitter"])){
                self::$partie->quitter();
                header("Location: index.php");
            }
        }
    }
}