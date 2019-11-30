<?php //Page personnel
include_once('config.php');
if(!$user->isLoggedin()){
    $user->redirect('index.php');
}

if(isset($_GET['deco'])){       //Deconnexion de l'utilisateur par le boutton "Se deconnecter"
    $user->logout();
    $user->redirect('index.php');
}

if(isset($_GET['contrib'])){    //Si l'utilisateur fait une demande pour devenir contributeur
    $user->setContributeur();
}
if(isset($_GET['delete'])){
    try{
        $_GET['delete']=[];
        $stmt=$dbh->prepare('SELECT * FROM Evenement WHERE ID_Event=:event AND login=:login');  //Verification de l'identite de l'utilisateur
        $stmt->bindParam(":event",$_GET['event']);
        $stmt->bindParam(":login",$_SESSION['user_session']);
        $stmt->execute();

        if($stmt->rowCount()){
            $stmt=$dbh->prepare('DELETE FROM Evenement WHERE ID_Event=:event');
            $stmt->bindParam(":event",$_GET['event']);
            $stmt->execute();
            $user->redirect("profile.php");
        }
    }
    catch(PDOExecption $e){
        echo $e->getMessage();
        $error="Une erreur est survenue pendant la suppression!";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Seek My Spot</title>
        <link rel="stylesheet" type="text/css" href="./style.css">
        <link rel="shortcut icon" href="img/favicon.ico">
        <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="form.js"></script>
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
        <div id="MainContainer">
            <?php
            if(!$user->isContributor()){
                echo "<div class='dropdown'>
                    <a href='./profile.php?contrib=true'><button class='dropbtn'>Devenir contributeur</button></a>
                    </div>";
            }else{
                echo "<div class='dropdown'>
                    <a href='./creation.php'><button class='dropbtn'>Créer un événement</button></a>";
            }
            ?>

            <div class="pacc">
                <p>
                    <h1><b>Bienvenue <?php echo $_SESSION['user_session'];?> !</b></h1>
                </p>
            </div>

            <div class="pacc">
                Voici les évenements auquels vous êtes inscrit:<br />
                <p>
                    <?php $user->listevent();?>
                </p>

                <hr />
                <p>
                    <?php
                    if($user->isContributor()){
                        echo "Voici la liste des événements que vous avez crées:<br />";
                        $user->listeventCree();
                    }
                    ?>
                </p>
            </div>
        </div>
    </body>
</html>
