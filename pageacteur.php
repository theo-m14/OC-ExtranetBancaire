<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_GET['id_acteur'])) {
    header('Location: http://localhost/oc-extranetbancaire/');
    exit();
}
include("src/bdd/bddcall.php");
$bdd = bddcall();
$currentactor = currentactor($bdd, $_GET['id_acteur']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
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
            <h3>Commentaires</h3>
            <form method="post">
                <textarea name="newpost" placeholder="Entrer votre commentaire" rows="3" cols="35"></textarea>
                <button name="like" class="likebutton"></button>
                <button name="dislike" class="dislikebutton"></button>
            </form>
        </div>
        <?php
        for ($i = 0; $i < $nbre_post['Nbpost']; $i++) {
            $currentpost = $catchallactorpost->fetch();
            $prenom = getnameuserpost($bdd, $currentpost['id_user']); ?>
            <div class="post">
                <p class="post_info"><?php echo $prenom . "  :  " . $currentpost['date_add'] ?></p>
                <p class="post_contenu"><?php echo $currentpost['post'] ?></p>
            </div>
        <?php
        }
        ?>
    </div>
    <?php include("views/footer.php") ?>
</body>

</html>