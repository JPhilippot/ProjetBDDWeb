<?php
	
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
                <?php 
                if($user->isLoggedin()){
                    echo '<div class="dropdown">
                    <a href="./profile.php"><button class="dropbtn">Mon profil</button></a>
                    </div>';
                } else {
                 echo '<div class="dropdown">
                 <a href="./index.php"><button class="dropbtn">Accueil</button></a>
                 </div>';   
             }
             ?>
         </th>
         <?php
         if($user->isLoggedin()){
            echo '<th>
            <div class="dropdown">
            <a href="./profile.php?deco=true"><button class="dropbtn">Se déconnecter</button></a>
            </div>
            </th>';
        }
        ?>
        <th>
            <div class="dropdown">
                <button class="dropbtn">Evénements</button>
                <div class="dropdown-content">
                    <a href="./carte.php">Carte</a>
                    <a href="./event.php">Liste</a>
                </div>
            </div>
        </th>
        <?php
        if(!$user->isLoggedin()){
            echo '<th>
            <div id="connection" class="dropdown">
            <button class="dropbtn" onclick =generationForm("log")>Se connecter</button>
            </div>
            </th>
            <th>
            <div id="enregister" class="dropdown">
            <button class="dropbtn" onclick =generationForm("reg")>' . "S'enregistrer</button>
            </div>
            </th>";
        }
        ?>
    </table>
</div>
</body>
</html>