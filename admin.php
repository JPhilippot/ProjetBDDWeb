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
if(isset($_GET['supprContr'])){
    try {

        $stmt=$dbh->prepare("DELETE FROM Contributeur WHERE login=:ulogin");
        $stmt->bindParam(":ulogin", $_GET['supprContr']);
        $stmt->execute();

    } catch (PDOException $e) {
        $error="Une erreur est survenue! " . $e->getMessage();
    }
}

if(isset($_GET['validContr'])){
    try{
        $stmt=$dbh->prepare("UPDATE Contributeur SET Attente=0 WHERE login=:ulogin");
        $stmt->bindParam(':ulogin',$_GET['validContr']);
        $stmt->execute();
    }
    catch(PDOException $e){
        echo $e->getMessage();
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
        <?php try{
            $stmt=$dbh->prepare("SELECT * FROM Contributeur WHERE Attente=1");
            $stmt->execute();
            if($stmt->rowCount()){
                echo "<ul>";
                foreach ($stmt as $row) {
                    echo "<li>{$row['login']} <a href='admin.php?validContr={$row['login']}'><button class='btn btn-primary'>Valider</button></a></li>";
                }
                echo "</ul>";
            }else{
                echo "Il n'y a pas de contributeur a valider<br />";
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
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
        } ?>
        <h2>Création de thème:</h2>
        <p>Voici les thèmes déjà existants:
            <?php
            $stmt = $dbh->prepare("SELECT Nom FROM Theme");
            $stmt->execute(); echo "<br>";
            foreach($stmt as $row){
                echo $row['Nom'] . " <a href='admin.php?supprTh={$row['Nom']}'><button class='btn btn-warning'>Supprimer</button></a> <br>";
            }            
            ?>
        </p>
        <div>
        <p>
            Seul les caractères alphanumériques sont autorisés.</p>
            <form class='form-inline' style="margin-left: 40%; " method="POST">
                <input class='form-control' type="text" name="nomTheme" pattern="[a-zA-Z0-9\s]+"/><br />
                <input class='form-control' type="submit" name="newTheme" value="Créer thème" />
            </form>
            </div>
    </div>
</body>

</html>