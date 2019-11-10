<?php
include_once('config.php');
if(!$user->isLoggedin() && !$user->isAdministrator()){
    $user->redirect('index.php');
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
        <!--Faire une liste des events-->
        <?php
        $user->listevent();
        ?>
        <!--Faire un form de creation de theme-->
    </div>
</body>
</html>
