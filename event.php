<?php
include_once('config.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
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
            <th>
                <div class="dropdown">
                    <button class="dropbtn">Evénements</button>
                    <div class="dropdown-content">
                        <a href="./event.html">Carte</a>            <!--carte en dur????-->
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
    <div id ="up">
            <a href="#Menu"><img id="arrrow" src="img/up.png"/></a>
    </div>
    <div id="MainContainer">
        <!--Display tout les event jusqu'a 50, faire plusieures pages-->   
        <div class="pacc">
        <p>
        <table id="listevent">
        <tr>
            <th>Titre</th><th>Date</th><th>Thème</th>
        </tr>
        <?php
            $stmt=$dbh->prepare("SELECT * FROM Evenement");
            $stmt->execute();
            foreach($stmt as $row){
                echo "<tr>";
                echo "<td><a href='contenu.php?lastevent=" . $row['ID_Event'] . "'>" . $row['Titre'] . "</a></td><td>" . $row['Date'] . "</td><td>" . $row['Nom'] . "</td>";
                echo "</tr>";
            }

        ?>
        </table>
        </p>
        </div>
   </div>
</body>

</html>
