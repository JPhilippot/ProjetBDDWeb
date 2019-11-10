<?php //Page personnel
include_once ('config.php');
if(!$user->isLoggedin()){
    $user->redirect('index.php');
}

if(isset($_GET['deco'])){
    $user->logout();
    $user->redirect('index.php');
}

if(isset($_GET['contrib'])){
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
            <table>
                <th>

                    <div class="dropdown">
                        <a href="./profile.php"><button class="dropbtn">Mon profile</button></a>
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
                <p>
                    Voici les évenements auquels vous êtes inscrit:
                </p>
                <p>
                    <?php $user->listevent();?>
                </p>
            </div>
        </div>
    </body>

</html>
