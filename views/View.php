<?php
namespace views;


class View {
    public static function affichage($view, $donnees = null){
        if(file_exists("views/".$view."View.php")){
            if(!is_null($donnees))
                extract($donnees);
            ob_start();
            require_once "views/".$view."View.php";
            $page = ob_get_clean();
            echo $page;
        }
    }
}