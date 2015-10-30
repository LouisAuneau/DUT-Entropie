<?php
namespace model;


class Joueur{
    /**
     * @var string $prenom Prénom du joueur.
     */
    private $prenom;

    /**
     * @var string couleur Couleur des pions du joueur, au format hexadécimal (#FFFFFF).
     */
    private $couleur;


    // -------------------------------------------------------------------------------------------------


    /**
     * Création d'un joueur.
     * @param string $prenom Prénom du joueur.
     * @param string $couleur Couleur des pions du joueur, au format hexadécimal (#FFFFFF).
     */
    public function __construct($prenom, $couleur){
        $this->prenom = $prenom;
        $this->couleur = $couleur;
    }


    /**
     * Accesseur pour le prénom du joueur.
     * @return string Prénom du joueur.
     */
    public function getPrenom(){ return $this->prenom; }


    /**
     * Accesseur pour la couleur du joueur.
     * @return string Couleur des pions du joueur, au format hexadécimal (#FFFFFF).
     */
    public function getCouleur(){ return $this->couleur; }
}