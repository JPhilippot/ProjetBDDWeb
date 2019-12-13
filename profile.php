<?php //Page personnel
include_once('config.php');
if (!$user->isLoggedin()) {
    $user->redirect('index.php');
}

if (isset($_GET['deco'])) {       //Deconnexion de l'utilisateur par le boutton "Se deconnecter"
    $user->logout();
    $user->redirect('index.php');
}

if (isset($_GET['contrib'])) {    //Si l'utilisateur fait une demande pour devenir contributeur
    $user->setContributeur();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Seek My Spot</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
</head>

<body>
    <div id="Menu">

        <div class="dropdown" id="globe-btn">
            <a href="./index.php"><img src="./img/globe.png" /></a>
        </div>
        <table style="margin:0;">
            <th>

                <div class="dropdown">
                    <a href="./profile.php"><button class="dropbtn">Mon profil</button></a>
                </div>
            </th>
            <th>

                <div class="dropdown">
                    <a href="./profile.php?deco=true"><button class="dropbtn">Se déconnecter</button></a>
                </div>
            </th>
            <th>
                <div class="dropdown">
                    <button class="dropbtn">Evénements</button>
                    <div class="dropdown-content">
                        <a href="./carte.php">Carte</a>
                        <a href="./event.php">Liste</a>
                    </div>
                </div>
            </th>
        </table>
    </div>
    <div id="MainContainer">
        <?php
        if (!$user->isContributor()) {
            echo "<div class='dropdown'>
                    <a href='./profile.php?contrib=true'><button class='dropbtn'>Devenir contributeur</button></a>
                    </div>";
        } else {
            echo "<div class='dropdown'>
                    <a href='./creation.php'><button class='dropbtn'>Créer un événement</button></a>";
        }
        ?>

        <div class="pacc">
            <p>
                <h1><b>Bienvenue <?php echo $_SESSION['user_session']; ?> !</b></h1>
            </p>
        </div>

        <div class="pacc">
            Voici les évenements auquels vous êtes inscrit:<br />
            <p>
                <?php $user->listevent(); ?>
            </p>

            <hr />
            <p>
                <?php
                if ($user->isContributor()) {
                    echo "Voici la liste des événements que vous avez crées:<br />";
                    $user->listeventCree();
                }
                ?>
            </p>
        </div>
    </div>
</body>

</html>