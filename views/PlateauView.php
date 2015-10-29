<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Entropie - Projet "Programmation Web Serveur</title>
    <link rel="stylesheet" href="public/styles.css"/>
</head>
<body>
    <h1>Entropie</h1>
    <?php echo $plateau; ?>
    <div id="commandes">
        <p><?php echo $joueurCourant; ?>, c'est votre tour.</p>
        <a href="index.php?quitter=1" title="Quitter la partie"><img src="public/img/icone_quitter.png" alt="Quitter la partie" style="height: 12px"/></a>
        <a href="index.php?retour=1" title="Retour en arrière"><img src="public/img/icone_retour.png" alt="Retour en arrière"/></a>
    </div>
</body>
</html>