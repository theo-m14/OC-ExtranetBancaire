<?php
session_start();
if (isset($_POST['logout']) && $_POST['logout'] == "true") {
    session_destroy();
    session_start();
}
if (!isset($_SESSION['username']) || !isset($_GET['id_acteur'])) {
    header('Location: http://localhost/oc-extranetbancaire/');
    exit();
}
include("src/bdd/bddcall.php");
$bdd = bddcall();
$currentactor = currentactor($bdd, $_GET['id_acteur']);
$currentuser = log_user($bdd, $_SESSION['username']);
if (isset($_POST['newpost'])) { //Ecriture du commentaire dans la bdd
    if (preg_match("#.{2,}#", $_POST['newpost'])) {  //Verification présence de au moins 2caractères
        $post_register = $bdd->prepare('INSERT INTO post(id_acteur,id_user,post) 
                        VALUES(:id_acteur, :id_user, :post)');
        $post_register->execute(array(
            'id_acteur' => $_GET['id_acteur'],
            'id_user' => $currentuser['id_user'],
            'post' => $_POST['newpost']
        ));
    }
}
/* Vérification bouton like/dislike */
if (isset($_POST['like']) || isset($_POST['dislike'])) {
    $usercurrentactor = log_usercurrentactor($bdd, $_SESSION['username'], $currentactor['id_acteur']);
    if (isset($_POST['like'])) {
        if (isset($usercurrentactor['vote'])) { //Si l'utilisateur a déja voté on actualise
            $usercurrentactor['vote'] = 1;
            $updatebdd_like = $bdd->prepare("UPDATE vote SET vote=1 WHERE id_user=? AND id_acteur=?");
            $updatebdd_like->execute(array($currentuser['id_user'], $currentactor['id_acteur']));
        } else {
            $writebdd_like = $bdd->prepare("INSERT INTO vote(id_user,id_acteur,vote)
                                            VALUES(:id_user, :id_acteur, :vote)");
            $writebdd_like->execute(array(
                'id_user' => $currentuser['id_user'],
                'id_acteur' => $currentactor['id_acteur'],
                'vote' => 1
            ));
        }
    } else {
        if (isset($usercurrentactor['vote'])) {
            $usercurrentactor['vote'] = -1;
            $updatebdd_dislike = $bdd->prepare("UPDATE vote SET vote=-1 WHERE id_user=? AND id_acteur=?");
            $updatebdd_dislike->execute(array($currentuser['id_user'], $currentactor['id_acteur']));
        } else {
            $writebdd_dislike = $bdd->prepare("INSERT INTO vote(id_user,id_acteur,vote)
                                            VALUES(:id_user, :id_acteur, :vote)");
            $writebdd_dislike->execute(array(
                'id_user' => $currentuser['id_user'],
                'id_acteur' => $currentactor['id_acteur'],
                'vote' => -1
            ));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GBAF :<?php echo $currentactor['nom']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="public/css/headercss.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>

<body>
    <?php include("views/header.php"); ?>
    <div class="section_acteur">
        <img src="public/img/<?php echo $currentactor['logo']  ?>" alt="actorthumbnails" class="logoactor_actorpage">
        <h2 class="actortitle"><?php echo $currentactor['nom']; ?></h2>
        <div class="actordesc_actorpage">
            <p> <?php echo nl2br($currentactor['description']); ?></p>
        </div>
    </div>
    <div class="section_commentaire">
        <?php
        $postcurrentactor = $bdd->prepare("SELECT COUNT(*) AS Nbpost FROM post WHERE id_acteur=?");
        $postcurrentactor->execute(array($currentactor['id_acteur']));
        $nbre_post = $postcurrentactor->fetch();
        $catchallactorpost = catchactorpost($bdd, $currentactor['id_acteur']); ?>
        <div class="topbar_post">
            <h3 id="section_commentaire">Commentaires</h3>
            <form method="post" action="#section_commentaire">
                <textarea name="newpost" placeholder="Entrer votre commentaire" rows="3" cols="35"></textarea>
                <input type="submit" value="Envoyer">
                <div class="like_dislike">
                    <div class="like_info">
                        <button name="like" class="likebutton" value="1"></button>
                        <label for="like"><?php echo likecounter($bdd, $currentactor) ?></label>
                    </div>
                    <div class="dislike_info">
                        <button name="dislike" class="dislikebutton" value="1"></button>
                        <label for="dislike"><?php echo dislikecounter($bdd, $currentactor) ?></label>
                    </div>
                </div>
            </form>
        </div>
        <?php
        if ($nbre_post['Nbpost'] < 6) { //Si moins de 6commentaire on les affiche tous
            for ($i = 0; $i < $nbre_post['Nbpost']; $i++) {
                $currentpost = $catchallactorpost->fetch();
        ?>
                <div class="post">
                    <p class="post_info"><?php echo htmlspecialchars($currentpost['prenom']) . "  :  " . $currentpost['date_add'] ?></p>
                    <p class="post_contenu"><?php echo htmlspecialchars($currentpost['post']) ?></p>
                </div>
            <?php
            }
        } else {

            if (!isset($_GET['page'])) {
                $_GET['page'] = 1;
                $page = 1;
            } else {
                $page = $_GET['page'];
            }
            if ($_GET['page'] > 1) {
                for ($i = 0; $i < 6 * ($_GET['page'] - 1); $i++) { //on parcours jusqu'a le comm voulu
                    $currentpost = $catchallactorpost->fetch();
                }
            }
            if ($_GET['page'] == 1 || $_GET['page'] == 2) {  //Eviter le duplicata de commentaire en page 2
                $currentpost = $catchallactorpost->fetch();
            }
            for ($i = 0; $i < 6 && isset($currentpost['prenom']); $i++) { //On affiche 6 commentaire && tant qu'un comm existe on affiche
            ?>
                <div class="post">
                    <p class="post_info"><?php echo htmlspecialchars($currentpost['prenom']) . "  :  " . $currentpost['date_add'] ?></p>
                    <p class="post_contenu"><?php echo htmlspecialchars($currentpost['post']) ?></p>
                </div>
            <?php
                $currentpost = $catchallactorpost->fetch();
            }

            ?>

            <div class="post_pagination">
                <!-- Gérance de l'affichage suivant ou précéd. selon le nombre de commentaire-->
                <?php if ($_GET['page'] != 1) { ?>
                    <a href="pageacteur.php?<?php echo "id_acteur=" . $currentactor['id_acteur'] . "&page=" . ($page - 1); ?>#section_commentaire">Page suivante</a>
                <?php }
                echo "<p>" . $_GET['page'] . "</p>";
                if ($_GET['page'] <= ($nbre_post['Nbpost'] / 6)) { ?>
                    <a href="pageacteur.php?<?php echo "id_acteur=" . $currentactor['id_acteur'] . "&page=" . ($page + 1); ?>#section_commentaire">Page précédente</a>
                <?php } ?>
            </div>
    </div>
<?php }
        include("views/footer.php") ?>
</body>

</html>