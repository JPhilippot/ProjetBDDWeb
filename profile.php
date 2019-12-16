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
    $message="Vous avez été mis en attente pour devenir contributeur.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seek My Spot</title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./style.css">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <?php if(isset($message)){echo "<script>alert('$message')</script>";}?>
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
    <div id="container">
        <div id="content">
            <?php
            if(!$user->isContributor()){
                echo "<div class='dropdown'>
                <a href='./profile.php?contrib=true'><button class='dropbtn'>Devenir contributeur</button></a>
                </div>";
            }
            else{
                echo "<div class='dropdown'>
                <a href='./creation.php'><button class='dropbtn'>Créer un événement</button></a>";
            }
            ?>

            <h1><b>Bienvenue <?php echo $_SESSION['user_session'];?> !</b></h1>
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
