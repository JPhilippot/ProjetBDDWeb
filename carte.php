<?php
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


$stmt = $dbh->prepare("SELECT COUNT(*) FROM Evenement;");
$stmt->execute();
$nbrow =  $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $dbh->prepare("SELECT * FROM Evenement, Localisation WHERE Evenement.ID_LOC = Localisation.ID_LOC;");
$stmt->execute();
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/css/ol.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>
    <title>Seek My Spot - Carte</title>

    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <script type="text/javascript" src="map.js"></script>
    <link rel="shortcut icon" href="img/favicon.ico">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>

<body>
    <div id="Menu">
        <table>
            <th>
                <?php
                if ($user->isLoggedin()) {
                    echo '<div class="dropdown"><a href="./profile.php"><button class="dropbtn">Mon profil</button></a> </div>';
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
            if (!$user->isLoggedin()) {
                echo '<th>
                <div id="connection" class="dropdown">
                <button class="dropbtn" onclick=generationForm("log")>Se connecter</button>
                </div>
                </th>
                <th>
                <div id="enregister" class="dropdown">
                <button class="dropbtn" onclick=generationForm("reg")>' . "S'enregistrer</button>
                </div>
                </th>";
            }
            ?>
        </table>
    </div>
    <div id="map" class="map" style="display: inline-block;
    height:800px;
    width: 100%;
    backface-visibility: hidden;
    "></div>
    <img id="markerProto" class="marker" src="./img/marker.png" style='width: 50px; height: 50px; display: none;' />
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
