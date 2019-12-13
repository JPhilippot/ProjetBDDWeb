<?php

header('Content-type: text/html; charset=utf-8');
include_once('config.php');

//Connexions utilisateurs
if(isset($_POST['log'])){
    $login= trim($_POST['login']);
    $pass= trim($_POST['pass']);

    if($user->login($login,$pass)){
        if(isset($_POST['remember'])){
            $cookie_name="user";
            $cookie_value=$_SESSION['user_session'];
            setcookie($cookie_name,$cookie_value, time() + (86400 * 30));
        }
        $user->redirect('profile.php');
    }
    else{
        $error="Informations incorrectes";
    }
} else if(isset($_POST['reg'])){
    $login= trim($_POST['login']);
    $email= trim($_POST['email']);
    $pass= trim($_POST['pass']);


    if($user->register($email,$login,$pass)){
        $user->redirect('profile.php');
    }
    else{
        echo "ERROR<br>";
    }
}
if(isset($_GET['deco'])){
    $user->logout();
    $user->redirect('index.php');
}


$stmt = $dbh->prepare("SELECT COUNT(*) FROM EVENEMENT;");
$stmt->execute();
$nbrow =  $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $dbh->prepare("SELECT * FROM EVENEMENT, LOCALISATION WHERE EVENEMENT.ID_LOC =  LOCALISATION.ID_LOC;");
$stmt->execute();
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/css/ol.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>
    <title>Carte des Events !</title>
    <link rel="shortcut icon" href="img/favicon.ico">
  
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <script type="text/javascript" src="map.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>

<body style="margin: 0; height: 100%; overflow: hidden">
<div id="Menu">

<div class="dropdown" id="globe-btn">
    <a href="./index.php"><img src="./img/globe.png" /></a>
</div>
<table style="margin:0;">
    <th>

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
    <th>
        <div id="connection" class="dropdown">
            <button class="dropbtn" onclick=generationForm('log')>Se connecter</button>
        </div>
    </th>
    <th>
        <div id="enregister" class="dropdown">
            <button class="dropbtn" onclick=generationForm('reg')>S'enregistrer</button>
        </div>
    </th>
</table>
</div>
<div style="color:white;margin:1%; align-text:center">
    <h2>Voici la carte des Evénements !</h2> <p>Depuis cette carte vous pouvez visualiser tous les évènements répertoriés sur notre site au travers du monde. Vous pouvez également les visualiser sous forme de liste en cliquant <a href="./event.php">ici</a>.</p>
</div>

    <div id="map" class="map" style="display: inline-block; margin-top:2.5%;
            height:800px;
            width: 100%;
            backface-visibility: hidden;
        "></div>
    <img id="markerProto" class="marker" src="./img/marker.png" style='width: 50px; height: 45px; display: none;' />
    <div id="popupProto" style="display:none; font-size:18pt; color:black; width:100px; height:100px"></div>

    <script type="text/javascript">
        var map = new ol.Map({
            target: 'map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            loadTilesWhileAnimating: true,
            loadTilesWhileInteracting: true,
            view: new ol.View({
                center: ol.proj.fromLonLat([1.8883335, 46.603354]),
                zoom: 6
            })
        });
    </script>
    </div>

    <script>
        <?php
        foreach ($events as $row) {

            echo 'addMarker(' . $row["ID_Event"] . ',"' . $row["Titre"] . '","' . $row["login"] . '",' . $row["Latitude"] . ',' . $row["Longitude"] . ',"' . $row["Adresse"] . '");
           $("#marker' . $row["ID_Event"] . '").show();
                $("#marker' . $row["ID_Event"] . '").mouseover(function () {
                    if($("#popup' . $row["ID_Event"] . '").is(":visible")){
                     $("#popup' . $row["ID_Event"] . '").hide(); }
                    else{
                        $("#popup' . $row["ID_Event"] . '").show();
                        }});
            
             $("#popup' . $row["ID_Event"] . '").attr("style","width:50%;height:100%;background-color:white;border-radius: 10px;padding:5px;").hide();';
        }
        ?>
    </script>
</body>
</html>