<?php
include_once('config.php');

if(!$user->isLoggedin() || !$user->isAdministrateur()){
    $user->redirect('index.php');
}

if(isset($_POST['newTheme'])){ //L'administrateur crée un nouveau thème en replissant le formulaire
    try{
        $stmt=$dbh->prepare('INSERT INTO Theme(Nom, login_Administrateur) VALUES(:tnom,:ulogin)');
        $stmt->bindParam(":tnom",trim($_POST['nomTheme'],"\t\n\'"));
        $stmt->bindParam(":ulogin",$_SESSION['user_session']);
        $stmt->execute();
    }
    catch(PDOException $e){
        $error="Une erreur est survenue ! " . $e->getMessage();
    }
}

if(isset($_GET['supprTh'])){ //L'administrateur supprime un theme
    try{
        $stmt=$dbh->prepare("DELETE FROM Theme WHERE Nom=:tnom");
        $stmt->bindParam(':tnom',$_GET['supprTh']);
        $stmt->execute();
    }
    catch(PDOException $e){
        $error="Une erreur est survenue! " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Seek My Spot</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./style.css">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <link rel="shortcut icon" href="img/favicon.ico">
    <?php
    if(isset($error)){
        echo "<script>alert($error);</script>";
    }
    ?>
</head>

<body>

    <div id="Menu">
        <table>
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
    <div id ="up">
        <a href="#Menu"><img id="arrow" src="img/up.png"/></a>
    </div>
    <div id="content">
        <h1>Bienvenue <?php echo $_SESSION['user_session'];?></h1>
        <h2>Validation contributeurs:</h2>
        <?php
        echo "Il n'y a pas de contributeur a valider<br />";
        ?>

        <h2>Liste des contributeurs:</h2>
        <?php
        try{
            $stmt=$dbh->prepare("SELECT * FROM Contributeur");
            $stmt->execute();
            echo "<ul>";
            foreach ($stmt as $row) {
                echo "<li>{$row['login']} <a href='admin.php?supprContr={$row['login']}'><button class='btn btn-warning'>Supprimer compte</button></a></li>";
            }
            echo "</ul>";
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
        ?>
        <h2>Création de thème:</h2>
        <p>Voici les thèmes déjà existants:
            <?php
            $stmt=$dbh->prepare("SELECT Nom FROM Theme");
            $stmt->execute();
            echo "<br>";
            foreach($stmt as $row){
                echo $row['Nom'] . " <a href='admin.php?supprTh={$row['Nom']}'><button class='btn btn-warning'>Supprimer</button></a> <br>";
            }
            ?>
        </p>
        <p>
            Seul les caractères alphanumériques sont autorisés.
            <form class='form-inline' style="margin-left: 40%; " method="POST">
                <input class='form-control' type="text" name="nomTheme" pattern="[a-zA-Z0-9\s]+"/><br />
                <input class='form-control' type="submit" name="newTheme" value="Créer thème" />
            </form>
            <p>
            </div>
        </body>
        </html>