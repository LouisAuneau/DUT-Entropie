<?php
    session_start();
    require_once 'model/Partie.php';
    require_once 'model/Plateau.php';
    require_once 'model/Joueur.php';
    require_once 'model/Cellule.php';
    require_once 'controller/EntropieController.php';
    require_once 'views/View.php';
    \Controller\EntropieController::demarrer();

