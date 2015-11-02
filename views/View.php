<?php
namespace views;


class View {
    /**
     * Permet l'affichage d'une vue.
     * @param string $view Nom de la vue (sans l'extension "View.php").
     * @param array|null $donnees Tableau des données qui seront affichées dans la vue.
     */
    public static function affichage($view, array $donnees = null){
        if(file_exists("views/".$view."View.php")){ // On vérifie que la vue existe.
            if(!is_null($donnees)) // On extrait les données ($donnees["var"] = "value" devient $var = "value") si il y'en a.
                extract($donnees);
            ob_start(); // ob_start lit toutes les inclusions et les sauvegardes dans un cache que l'on récupère et que l'on affiche avec echo (voir doc php).
            require_once "views/".$view."View.php";
            $page = ob_get_clean();
            echo $page;
        }
    }
}