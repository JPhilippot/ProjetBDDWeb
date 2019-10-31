<?php //Page personnel
include_once ('config.php');                //Pour pouvoir avoir accès a la variable $user
if(!$user->isLoggedin()){
   $user->redirect('index.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="./style.css">
    <script src="jquery-3.4.1.min.js"></script>
    <script src="form.js"></script>
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
                    <button class="dropbtn">Evénements</button>
                    <div class="dropdown-content">
                        <a href="./carte.php">Carte</a>            <!--carte en dur????-->
                        <a href="./event.php">Liste</a>
                    </div>
                </div>
            </th>
        </table>
    </div>
    <div id ="up">
        <a href="#Menu"><img id="arrrow" src="img/up.png"/></a>
        
    </div>
    <div id="MainContainer">
        
        <div class="pacc">
            <p>
                <h1><b>Bienvenue <?php echo $_SESSION['user_session'];?> !</b></h1>
            </p>
        </div>
        
        <div class="pacc">
            <p>Afficher la liste des evenement auquel <?php echo $_SESSION['user_session'];?> est inscrit</p>
            <p><?php
            $user->listevent();
            ?>
        </p>
    </div>
</div>
</body>

</html>