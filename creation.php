<?php
include_once('config.php');
if(!$user->isLoggedin() || !$user->isContributor()){
    $user->redirect("index.php");
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

        <form method="post">
            <label for="title">Titre:</label><br />
            <input type="text" name="title" id="title" placeholder="Titre"><br />
            <label for="theme">Thème:</label><br />
            <select type="select" name="theme" id="theme">
                <option value="" selected>Choisir</option>
                <?php
                $stmt=$dbh->prepare('SELECT Nom FROM Theme');
                $stmt->execute();
                foreach($stmt as $row){
                    echo "<option value='$row[0]'>$row[0]</option>";
                }
                ?>
            </select><br />
            <label for="date">Date:</label><br />
            <input type="date" name="date" value="date" /><br /> <!--a changer avec l'adresse-->
            <label for="adress">Adresse: </label><br />
            <input type="text" name="adress" placeholder="Adresse" /><br />
            <label for="effect">Effectif maximum:</label><br />
            <input type="number" name="effect" value=0 /><br />
            <label for="desc">Description: </label><br />
            <textarea name="desc" id="desc" rows="5" cols="80" maxlength=300 placeholder="Description(300 caractères max)"></textarea><br />

            <input type="submit" value="Créer" name="create"/>
        </form>

    </div>
</body>
</html>
