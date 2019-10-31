<?php
include_once('config.php');

if(!isset($_GET['lastevent'])){
    $user->redirect('event.php');
} else {
    try{
        $stmt=$dbh->prepare("SELECT * FROM Evenement, Localisation WHERE Evenement.ID_Loc=Localisation.ID_Loc AND ID_Event=:levent");
        $stmt->bindParam(":levent", $_GET['lastevent']);
        $stmt->execute();
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if($stmt->rowCount()){
            $titre=$row['Titre'];
        }
        else{
            echo "ERROR";
            die();
        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        die();
    }
}
if(isset($_GET['inscription'])){
    //push dans la table
    try{
        $stmt=$dbh->prepare("INSERT INTO S_inscrit VALUES(:levent, :ulogin)");
        $stmt->bindParam(":levent",$_GET['lastevent']);
        $stmt->bindParam(":ulogin",$_SESSION['user_session']);
        $stmt->execute();
    }
    catch(PDOException $e){
        echo $e->getMessage();
        die();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/css/ol.css" type="text/css">
</style>
<script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>
<title>Nom du site</title>

<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="./style.css">

<style>
    .map {
        display: inline-block;
        margin-left: 22%;
        margin-right: 25%;
        height: 500px;
        width: 900px;
        backface-visibility: hidden;
    }
</style>
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
<div id="MainContainer">
    <div class="pacc">
        <p>
            <h1><b><?php echo $row['Titre']; ?></b>
            </h1>
        </p>
    </div>

    <div id="map" class="map"></div>
    <script type="text/javascript">
        var map = new ol.Map({
            target: 'map',
            layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat([<?php echo $row['Longitude'] . ", " . $row['Latitude'];?>]),
                zoom: 14
            })
        });
    </script>
    <div id="desc">
        <p><b>Description:</b><br> <?php echo $row['Descriptif'];?></p>
    </div>
    <div>
        <?php
        if($user->isLoggedin()){
            echo "<button><a href='contenu.php?inscription=true'>S'inscrire</a></button>"; 
        } else {
            echo '<button onclick='.'"'."alert('Vous devez être connecté(e) pour pouvoir vous inscrire')" . '"' . ">S'inscrire</button>";
        }
        ?>
    </div>
    <div>
        <div id="comzone">
            <p>
                <div>
                    <!-- <img class="pp" src ="./img/jm.png"/> -->
                    <h4><strong>Jean-Martin de Garonne :</strong></h4>
                </div> 
                Cet évènement revient chaque année à Capestang, c'est un incontournable de la pêche a la crevette tigrée ! A voir absolument !!
            </p>
        </div>
        <form action="/" method="post" id="chat_form">
            <input type="text" name="message" id="message" placeholder="Dîtes quelquechose..." size="50"/>
            <input type="submit" id="send_message" value="Envoyer"/>
        </form>
    </div>
</div>
</body>

</html>