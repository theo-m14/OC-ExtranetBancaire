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
    $allactorpost = $bdd->query("SELECT DATE_FORMAT(p.date_add, '%d/%m/%Y') date_add, p.post post, a.prenom prenom
                                FROM post p
                                LEFT JOIN account a
                                ON p.id_user=a.id_user
                                WHERE id_acteur='$acteur_id' 
                                ORDER BY id_post DESC");
    return $allactorpost;
}

function likecounter($bdd, $currentactor)
{
    $counter = $bdd->prepare("SELECT COUNT(*) as Nblike FROM vote WHERE id_acteur=? AND vote=1");
    $counter->execute(array($currentactor['id_acteur']));
    $likecounter = $counter->fetch();
    return $likecounter['Nblike'];
}

function dislikecounter($bdd, $currentactor)
{
    $counter = $bdd->prepare("SELECT COUNT(*) as Nbdislike FROM vote WHERE id_acteur=? AND vote=-1");
    $counter->execute(array($currentactor['id_acteur']));
    $dislikecounter = $counter->fetch();
    return $dislikecounter['Nbdislike'];
}

function log_usercurrentactor($bdd, $username, $id_acteur) /* Récupération de l'utilisateur et de son vote sur l'acteur courant */
{
    $log_user = $bdd->query("SELECT a.prenom prenom, a.id_user id_user, v.vote vote
                            FROM account a
                            LEFT JOIN vote v
                            ON a.id_user = v.id_user
                            WHERE a.username='$username' AND v.id_acteur='$id_acteur'");
    $info_user = $log_user->fetch();
    return $info_user;
}
/* Fonction pour les modifications de profil*/
function modifnom($bdd, $nouvellevaleur, $iduser)
{
    $modifinfo = $bdd->prepare('UPDATE account SET nom=? WHERE id_user=?');
    $modifinfo->execute(array($nouvellevaleur, $iduser));
}

function modifprenom($bdd, $nouvellevaleur, $iduser)
{
    $modifinfo = $bdd->prepare('UPDATE account SET prenom=? WHERE id_user=?');
    $modifinfo->execute(array($nouvellevaleur, $iduser));
}

function modifusername($bdd, $nouvellevaleur, $iduser)
{
    $modifinfo = $bdd->prepare('UPDATE account SET username=? WHERE id_user=?');
    $modifinfo->execute(array($nouvellevaleur, $iduser));
}
function modifpassword($bdd, $nouvellevaleur, $iduser)
{
    $modifinfo = $bdd->prepare('UPDATE account SET password=? WHERE id_user=?');
    $modifinfo->execute(array($nouvellevaleur, $iduser));
}
