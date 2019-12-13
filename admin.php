<?php
include_once('config.php');
if (!$user->isLoggedin() && !$user->isAdministrator()) {
    $user->redirect('index.php');
}
if (isset($_POST['newTheme'])) { //L'administrateur crée un nouveau thème en replissant le formulaire
    try {
        echo "oui<br />";
        $stmt = $dbh->prepare('INSERT INTO Theme(Nom,login_Administrateur) VALUES(:tnom,:ulogin)');
        $stmt->bindParam(":tnom", $_POST['nomTheme']);
        $stmt->bindParam(":ulogin", $_SESSION['user_session']);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Une erreur est survenue !<br>$e->getMessage()";
    }
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
    <?php
    if (isset($error)) {
        echo "<script>alert($error);</script>";
    }
    ?>
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
        <h1>Bienvenue <?php echo $_SESSION['user_session']; ?></h1>
        <h2>Validation contributeurs:</h2>
        <?php
        echo "Il n'y a pas de contributeur a valider<br />";
        ?>
        <h2>Création de thème:</h2>
        <p>Voici les thèmes déjà existants:
            <?php
            $stmt = $dbh->prepare("SELECT Nom FROM Theme");
            $stmt->execute();
            foreach ($stmt as $row) {
                echo $row['Nom'] . " ";
            }
            ?>
        </p>
        <form method="POST">
            <input type="text" name="nomTheme" /><br />
            <input type="submit" name="newTheme" value="Créer thème" />
        </form>
    </div>
</body>

</html>