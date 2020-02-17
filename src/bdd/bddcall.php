<?php
function bddcall()
{
    try {           //Récupération BDD
        $bdd = new PDO('mysql:host=localhost;dbname=extranet_gbaf;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        return $bdd;
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
//Préparation d'enregistrement
function registeruser($bdd)
{
    $enregistrement_utilisateur = $bdd->prepare('INSERT INTO account(nom,prenom,username,password,question,reponse)
                                VALUES(:nom, :prenom, :username, :password, :question, :reponse)');
    return $enregistrement_utilisateur;
}
