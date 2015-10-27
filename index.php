<?php
    session_start();
    require_once 'model/Partie.php';
    require_once 'model/Joueur.php';
    require_once 'model/Cellule.php';

    // Si une partie n'est pas en cours (donc page de creation en cours)
    if(!isset($_SESSION["partie"])) {
        // Création d'une partie
        if (isset($_POST["joueur1"]) && isset($_POST["joueur2"]) && isset($_POST["couleurJoueur2"]) && isset($_POST["couleurJoueur2"])) {
            if ($_POST["joueur1"] != $_POST["joueur2"] && $_POST["couleurJoueur1"] != $_POST["couleurJoueur2"]) {
                $j1 = new \model\Joueur($_POST["joueur1"], $_POST["couleurJoueur1"]);
                $j2 = new \model\Joueur($_POST["joueur2"], $_POST["couleurJoueur2"]);
                $_SESSION["partie"] = serialize(new \model\Partie($j1, $j2));
            } else
                $error = "La couleur et/ou le nom des joueurs sont identiques.";
        }
    }

    // Si une partie est en cours (on utilise un deuxième if, au cas où la partie vient d'être créée).
    if(isset($_SESSION["partie"])){
        $partie = unserialize($_SESSION["partie"]);
    }

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Entropie - Projet "Programmation Web Serveur</title>
    <link rel="stylesheet" href="public/styles.css"/>
</head>
<body>
    <h1>Entropie</h1>
    <?php if(!isset($_SESSION["partie"])) { ?>
    <form action="" method="post">
        <h3>Joueur 1 :</h3>
        <input type="text" name="joueur1"/>
        <input type="color" name="couleurJoueur1"/>

        <h3>Joueur 2 :</h3>
        <input type="text" name="joueur2"/>
        <input type="color" name="couleurJoueur2"/>

        <button type="submit">Débuter la partie.</button>
    </form>
    <?php } else {
        echo $partie->toHtml();
    } ?>
</body>
</html>