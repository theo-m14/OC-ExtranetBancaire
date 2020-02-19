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

function log_user($bdd, $username)
{
    $log_user = $bdd->query("SELECT * FROM account WHERE username='$username'");
    $info_user = $log_user->fetch();
    return $info_user;
}

function catchallactor($bdd)
{
    $allactor = $bdd->query('SELECT * FROM acteur');
    return $allactor;
}

function currentactor($bdd, $acteur_id)
{
    $actor = $bdd->query("SELECT * FROM acteur WHERE id_acteur='$acteur_id'");
    $currentactor = $actor->fetch();
    return $currentactor;
}

function catchactorpost($bdd, $acteur_id)
{
    $allactorpost = $bdd->query("SELECT * FROM post WHERE id_acteur='$acteur_id'");
    return $allactorpost;
}

function getnameuserpost($bdd, $id_user)
{
    $user = $bdd->query("SELECT prenom FROM account WHERE id_user='$id_user'");
    $currentuser = $user->fetch();
    $prenom = $currentuser['prenom'];
    return $prenom;
}
