<?php
    session_start();
    require_once 'model/Partie.php';
    require_once 'model/Joueur.php';
    require_once 'model/Cellule.php';
    require_once 'controller/GeneralController.php';
    \Controller\GeneralController::run();
