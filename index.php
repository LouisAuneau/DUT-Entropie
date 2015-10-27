<?php
    session_start();
    require_once 'model/Partie.php';
    require_once 'model/Plateau.php';
    require_once 'model/Joueur.php';
    require_once 'model/Cellule.php';
    require_once 'controller/GeneralController.php';
    //\Controller\GeneralController::run();

    $joueur1 = new \model\Joueur("Louis", "#000");
    $joueur2 = new \model\Joueur("Autre", "#fff");
    $partie = new \model\Partie();
    $partie->debutPartie($joueur1, $joueur2);

