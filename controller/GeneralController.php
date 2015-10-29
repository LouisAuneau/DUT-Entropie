<?php
namespace Controller;


use model\Joueur;
use model\Partie;

class GeneralController {
    public static function demarrer() {
        // Si une partie est lancée
        if (isset($_SESSION["partie"])) {
            // On continu la partie si on a reçu des informations
            if (isset($_GET["etape"]) && isset($_GET["x"]) && isset($_GET["y"]))
                self::avancerPartie($_GET["etape"], $_GET["x"], $_GET["y"]);

            // Si la partie est gagnee, on vérifie que c'est vrai et on affiche la bonne vue et on arrête l'application, sinon on ne fait rien.
            if(isset($_GET["gagnee"])){
                $partie = unserialize($_SESSION["partie"]);
                if($partie->gagnee() != false){
                    $donnees["gagnant"] = $partie->gagnee()->getPrenom();
                    $donnees["couleur"] = $partie->gagnee()->getCouleur();
                    self::affichage("Gagne", $donnees);
                    $partie->quitter();
                    return false;
                }

            }

            // On récupère les données dans le modèle et on les stock pour les afficher
            $partie = unserialize($_SESSION["partie"]);
            $donnees["plateau"] = $partie->getPlateau()->toHtml();
            $donnees["joueurCourant"] = $partie->getJoueurCourant()->getPrenom();

            // On affiche le plateau et on sauvegarde
            self::affichage("Plateau", $donnees);
            $partie->sauvegarder();
        }

        // Si une partie n'est pas lancée
        else {
            // Traitement si les informations ont étées remplies pour débuter la partie.
            if (isset($_GET["joueur1"]) && isset($_GET["joueur2"]) && isset($_GET["couleurJoueur1"]) && isset($_GET["couleurJoueur2"])) {
                self::creerPartie($_GET["joueur1"], $_GET["couleurJoueur1"], $_GET["joueur2"], $_GET["couleurJoueur2"]);
            }

            // Affichage de la vue formulaire
            self::affichage("CreerPartie");
        }
    }


    private static function affichage($view, $donnees = null) {
        // Si on a des données, on les extraits pour les afficher dans la vue.
        if (!is_null($donnees)) extract($donnees);

        // Si le fhichier de la vue spécifié existe, on l'affiche avec les données extraites auparavant.
        if (file_exists("./views/" . $view . "View.php")) {
            ob_start();
            require_once "./views/" . $view . "View.php";
            $page = ob_get_clean();
            echo $page;
        }
    }

    private static function creerPartie($j1, $couleurJ1, $j2, $couleurJ2) {
        // On oblige les informations des deux joueurs à être différentes
        if ($j1 != $j2 && $couleurJ1 != $couleurJ2) {
            $j1 = new Joueur($j1, $couleurJ1);
            $j2 = new Joueur($j2, $couleurJ2);
            $_SESSION["partie"] = serialize(new Partie($j1, $j2)); // On créer la partie
            header('Location: index.php'); // On recharge pour lancer la partie.
        } else {
            header('Location: index.php'); // Si erreur on recharge le formulaire.
        }
    }

    private static function avancerPartie($etape, $x, $y) {
        $partie = unserialize($_SESSION["partie"]);

        // On vérifie la conformité des informations
        if ($partie->getEtape() != $etape) // L'étape en cours rentrée par l'utilisateur est bien celle en cours
            return false;

        if ($partie->getPlateau()->getCellule($x, $y) == null) // La cellule à déplacer n'est pas dans le plateau
            return false;

        // Si la partie est gagnée, on charge la vue gagnée
        if($partie->gagnee() != false)
            header('Location:index.php?gagnee=true');

        // Dans le cas ou on a fait l'étape 1 :
        if ($etape == 1) {
            if ($partie->getPlateau()->getCellule($x, $y)->deplacable()) { // Si la cellule est bien déplacable
                $partie->setCelluleADeplacer($partie->getPlateau()->getCellule($x, $y));
                $partie->setEtape(2);
                $partie->sauvegarder();
            }
        }

        // Dans le cas où on est à l'étape 2
        if($etape == 2){
            if(in_array($partie->getPlateau()->getCellule($x, $y), $partie->getCelluleADeplacer()->getCellulesSuivantesDisponibles())){
                $partie->getCelluleADeplacer()->setJoueur(null);
                $partie->getPlateau()->getCellule($x, $y)->setJoueur($partie->getJoueurCourant());
                $partie->changerJoueurCourant();
                $partie->setEtape(1);
                $partie->sauvegarder();
            }
        }
    }

}