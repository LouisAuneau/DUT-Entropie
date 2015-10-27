<?php
namespace Controller;


use model\Joueur;
use model\Partie;

class GeneralController {
    private $partie;

    /**
     * Cette fonction statique va se lancer à chaque chargement de page et effectuer tous les traitements nécessaires.
     * C'est le point d'entrée de l'application.
     */
    public static function run(){
        // Si une partie n'est pas lancée
        if(!isset($_SESSION["partie"])) {
            // Traitement si les informations ont étées remplies pour débuter la partie.
            if(isset($_GET["joueur1"]) && isset($_GET["joueur2"]) && isset($_GET["couleurJoueur1"]) && isset($_GET["couleurJoueur2"])){
                self::lancerPartie($_GET["joueur1"], $_GET["couleurJoueur1"], $_GET["joueur2"], $_GET["couleurJoueur2"]);
            }

            // Affichage de la vue formulaire
            self::affichage("CreerPartie");
        }

        // Si une partie est déjà lancée
        else{
            $partie = unserialize($_SESSION["partie"]);
            $donnees["plateau"] = $partie->toHtml();
            $donnees["joueurCourant"] = $partie->getJoueurCourant()->getPrenom();

            self::affichage("Plateau", $donnees);
        }
    }


    private static function affichage($view, $donnees = null){
        // Si on a des données, on les extraits pour les afficher dans la vue.
        if(!is_null($donnees))
            extract($donnees);

        // Si le fhichier de la vue spécifié existe, on l'affiche avec les données extraites auparavant.
        if(file_exists("./views/".$view."View.php")){
            ob_start();
            require_once "./views/".$view."View.php";
            $page = ob_get_clean();
            echo $page;
        }
    }

    private static function lancerPartie($j1, $couleurJ1, $j2, $couleurJ2){
        // On oblige les informations des deux joueurs à être différentes
        if($j1 != $j2 &&  $couleurJ1 != $couleurJ2){
            $j1 = new Joueur($j1, $couleurJ1);
            $j2 = new Joueur($j2, $couleurJ2);
            $_SESSION["partie"] = serialize(new Partie($j1, $j2)); // On créer la partie
            header('Location: index.php'); // On recharge pour lancer la partie.
        } else{
            header('Location: index.php'); // Si erreur on recharge le formulaire.
        }
    }
}