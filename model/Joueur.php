<?php
namespace model;


class Joueur{
    private $prenom;
    private $couleur;

    public function __construct($prenom, $couleur){
        $this->prenom = $prenom;
        $this->couleur = $couleur;
    }

    public function getPrenom(){ return $this->prenom; }
    public function getCouleur(){ return $this->couleur; }
}