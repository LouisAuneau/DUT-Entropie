<?php
namespace Controller;

use model\Joueur;
use model\Partie;
use views\View;

class EntropieController {
    /**
     * @var Partie $partie Stock la partie en cours à chaque démarrage. Null si aucune partie n'est commencée.
     */
    private static $partie;


    // -------------------------------------------------------------------------------------------------


    /**
     * Cette méthode est le point d'entrée du jeu, elle récupère la partie en cours, puis execute les différents fonctionnalités du jeu.
     */
    public static function demarrer(){
        self::$partie = Partie::charger(); // Charge la partie (null si aucune partie en cours).

        self::debut(); // Traitement du début d'une partie (formulaire de prénom, et initialisation de la partie).
        self::quitter(); // Traitement au cas où on demande à arrêter la partie en cours.
        self::retour(); // Traitement si le joueur demande à annuler un coup.
        self::avancer(); // Traitement pour avancer dans le jeu.
    }


    /**
     * Cette méthode vérifie si un joueur demande à annuler un coup et donc à retourner à l'état précédant de la partie.
     * Si c'est le cas, le modèle est affecté en conséquence.
     */
    private static function retour(){
        if(!is_null(self::$partie)){
            if(isset($_GET["retour"])){ // Si un retour est demandé :
                // On remet la partie à l'état précedant, on sauvegarde et on rafraichi pour afficher les changements.
                self::$partie = self::$partie->retour();
                self::$partie->sauvegarder();
                header("Location: index.php");
            }
        }
    }


    /**
     * Cette méthode effecture tous les traitements nécessaires au début d'une partie :
     * Affichage du formulaire demandant les prenoms et couleurs des joueurs.
     * Création de la partie dans le modèle si les informations du formulaires ont été remplies.
     */
    private static function debut(){
        if(is_null(self::$partie)){
            // Si le joueur a rempli le formulaire, avec deux prénoms différents et deux couleurs différentes, on créer la partie avec les informations données.
            if(isset($_GET["joueur1"]) && isset($_GET["joueur2"]) && isset($_GET["couleurJoueur1"]) && isset($_GET["couleurJoueur2"])
                && $_GET["joueur1"] != $_GET["joueur2"]
                && $_GET["couleurJoueur1"] != $_GET["couleurJoueur2"]
            ){
                $joueur1 = new Joueur($_GET["joueur1"], $_GET["couleurJoueur1"]);
                $joueur2 = new Joueur($_GET["joueur2"], $_GET["couleurJoueur2"]);
                $partie = new Partie($joueur1, $joueur2);
                $partie->sauvegarder();
                header("Refresh:0");
            }
            // Si le formulaire n'est pas rempli où qu'il y'a une erreur dans la saisie, on l'affiche à nouveau.
            else{
                View::affichage("CreerPartie");
            }
        }
    }


    /**
     * Cette méthode vérifie si un joueur a gagné, et si c'est le cas, affiche la page de félicitation du vainqueur.
     */
    private static function gagnee(){
        if(!is_null(self::$partie)) {
            // Si la page de victoire est demandée, et que dans le modèle, la partie est en effet gagnée, on affiche la page. (Il se peut qu'un joueur appel manuelement la page "?gagne", alors que ce n'est pas le cas, on utilise donc le modèle pour vérifier.
            if(isset($_GET["gagnee"]) && self::$partie->gagnee() != false){
                $donnees["gagnant"] = self::$partie->gagnee()->getPrenom();
                $donnees["couleur"] = self::$partie->gagnee()->getCouleur();
                View::affichage("Gagne", $donnees);
                self::$partie->quitter(); // on stop la partie.
                die(); // On s'arrête ici pour que le traitement des déplacements ne soit pas fait.
            }
            // Si on constate un vainqueur, on demande la page de victoire.
            else if(self::$partie->gagnee() != false)
                header('Location: index.php?gagnee=1');
        }
    }


    /**
     * Cette méthode est la principale méthode du controleur, qui va faire avancer le jeu à chaque étape en modifiant le modèle.
     * Ainsi, selon l'étape demandé, et les cases demandées, la méthode va vérifier la validité des informations, et effectuer les déplacements ainsi que l'affichage.
     * La méthode se charge aussi de sauvegarder le modèle avant et après les modifications afin de pouvoir revenir en arrière si besoin.
     */
    private static function avancer(){
        if(!is_null(self::$partie)) {
            // On sauvegarde l'état du jeu avant d'avancer la partie.
            self::$partie->setEtatPrecedant();

            // On effectue le mouvement demandé si il est conforme au modele stocké
            if(isset($_GET["etape"]) && isset($_GET["x"]) && isset($_GET["y"])){ // Si un mouvement est demandé
                if($_GET["etape"] == self::$partie->getEtape() && self::$partie->getPlateau()->getCellule($_GET["x"], $_GET["y"]) != null){ // L'étape demandé est bien l'étape en cours dans le modèle et la cellule demandé est dans le plateau.

                    // Traitement pour l'étape 1
                    if($_GET["etape"] == 1){
                        $celluleADeplacer = self::$partie->getPlateau()->getCellule($_GET["x"], $_GET["y"]);
                        // On effectue le déplacement uniquement si la case à déplacer appartient au joueur qui joue, et si elle est déplacable.
                        if($celluleADeplacer->getJoueur() == self::$partie->getJoueurCourant() && $celluleADeplacer->deplacable()){
                            self::$partie->setCelluleADeplacer($celluleADeplacer);
                            self::$partie->setEtape(2); // On passe à l'étape suivante.
                            self::$partie->sauvegarder();
                        }
                    }

                    // Traitement pour l'étape 2
                    else if($_GET["etape"] == 2){
                        $celluleCible = self::$partie->getPlateau()->getCellule($_GET["x"], $_GET["y"]);
                        // On effectue le déplacement uniquement si la case cible du déplacement est bien une des cases possibles pour déplacer le pion spécifié à l'étape précedante.
                        if(in_array($celluleCible, self::$partie->getCelluleADeplacer()->getCellulesSuivantesDisponibles())){
                            self::$partie->getCelluleADeplacer()->setJoueur(null); // On vide la cellule où le pion se trouvait.
                            $celluleCible->setJoueur(self::$partie->getJoueurCourant()); // On met le pion dans la cellule cible.
                            self::$partie->changerJoueurCourant();
                            self::$partie->setEtape(1);
                            self::$partie->sauvegarder();
                        }
                    }

                }
            }

            // Si un message d'erreur est demandé, on l'affiche.
            if(isset($_GET["message"])) {
                if ($_GET["message"] == "isole")
                    $donnees["erreur"] = "Ce pion est isolé, vous devez rompre son isolement pour le déplacer.";
                else if ($_GET["message"] == "isolement")
                    $donnees["erreur"] = "Un pion est isolé, or ce pion ne peut pas rompre l'isolement ! Choisissez en un autre.";
                else
                    $donnees["erreur"] = "Ce pion ne peut pas être déplacé car il n'a pas de pion allié à côté.";
            }

            // On vérifie si il y a victoire, car cela stoppera la partie et n'affichera pas la grille.
            self::gagnee();

            // On affiche le plateau et les infos
            $donnees["plateau"] = self::$partie->getPlateau()->toHtml();
            $donnees["joueurCourant"] = self::$partie->getJoueurCourant()->getPrenom();
            View::affichage("Plateau", $donnees);
        }
    }


    /**
     * Cette méthode vérifie si on a demander à quitter la partie, si c'est le cas, la partie est fermée et la page rechaargée.
     */
    public static function quitter(){
        if(!is_null(self::$partie)){
            if(isset($_GET["quitter"])){ // Si on a demandé à quitter
                self::$partie->quitter(); // La partie est supprimée dans le modèle.
                header("Location: index.php");
            }
        }
    }
}