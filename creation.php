<?php
include_once('config.php');
if (!$user->isLoggedin() || !$user->isContributor()) {
    $user->redirect("index.php");
}

if (isset($_GET['deco'])) {
    $user->logout();
    $user->redirect('index.php');
}

if (isset($_POST['create'])) {
    $user->createEvent($_POST['title'], $_POST['theme'], $_POST['date'], $_POST['adress'], $_POST['effect'], $_POST['desc']);
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
    <script type="text/javascript" src="map.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/css/ol.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>
    <link rel="shortcut icon" href="img/favicon.ico">

</head>

<body>

    <div id="Menu">

        <div class="dropdown" id="globe-btn">
            <a href="./index.php"><img src="./img/globe.png" /></a>
        </div>
        <table style="margin:0;">
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
    <img id="markerProto" class="marker" src="./img/marker.png" style='width: 50px; height: 45px; display: none;' />
    <div id="popupProto" style="display:none; font-size:18pt; color:black; width:100px; height:100px"></div>

    <div id="MainContainer">
        <h1>Création d'événements:</h1>

        <div id="map" class="map" style="float:right; display: inline-block; margin-top:2.5%;
            height:400px;
            width: 50%;
            backface-visibility: hidden;
        "></div>


        <form method="post">
            <label for="title">Titre:</label><br />
            <input id="title" type="text" name="title" id="title" placeholder="Titre"><br />
            <label for="theme">Thème:</label><br />
            <select type="select" name="theme" id="theme">
                <option value="" selected>Choisir</option>
                <?php
                $stmt = $dbh->prepare('SELECT Nom FROM Theme');
                $stmt->execute();
                foreach ($stmt as $row) {
                    echo "<option value='$row[0]'>$row[0]</option>";
                }
                ?>
            </select><br />
            <label for="date">Date:</label><br />
            <input type="date" name="date" value="date" /><br />
            <label for="adress">Adresse: </label><br />
            <input id='address' type="text" name="address" placeholder="Adresse" /><br />
            <input type="button" name="verifAdresse" value="Vérifier l'adresse" onclick="getAddress();" /><br />
            <label for="effect">Effectif maximum:</label><br />
            <input type="number" name="effect" value=0 /><br />
            <label for="desc">Description: </label><br />
            <textarea name="desc" id="desc" rows="5" cols="50" maxlength=300 placeholder="Description(300 caractères max)"></textarea><br />

            <input type="submit" value="Créer" name="create" />
        </form>

        <div id="otherAddresses" style="display:none;  overflow-y: scroll; height: 300px;">
            <h2>Autres addresses correspondantes :</h2>
            <ol style="list-style-type: none;">

            </ol>
        </div>
    </div>



    <script>
        var coord = {
            lon: 1.8883335,
            lat: 46.603354
        };
        console.log(coord);
        console.log(coord.lon);
        console.log(coord.lat);

        var addresses = [];

        function getAddress() {
            $("ol").empty();
            // .remove();
            console.log("contenu du form: " + $('#address').serialize());
            var requiredAddress = $('#address').serialize().substr(8);
            console.log('form Address: ' + requiredAddress);
            console.log('url : https://nominatim.openstreetmap.org/search/' + requiredAddress + '?format=json&polygon=1&addressdetails=1')
            $.get("https://nominatim.openstreetmap.org/search/" + requiredAddress + "?format=json&polygon=1&addressdetails=1&limit=5", function(data) {
                // $.get("https://nominatim.openstreetmap.org/search/135%20pilkington%20avenue,%20birmingham?format=json&polygon=1&addressdetails=1", function(data) {


                console.log(data);
                if (data[0] == null) {
                    $("#otherAddresses").hide();
                    if (!$('#errAddress').length) {
                        $("#MainContainer").append("<h2 id='errAddress' style='margin-top:10px; padding:10px;'>Aucune adresse correspondante à votre saisie n'a été trouvée ! :(</h2>")
                    }
                } else {
                    if ($('#errAddress').length) {
                        $('#errAddress').remove();
                    }
                    coord.lon = data[0].lon;
                    coord.lat = data[0].lat;
                    addresses = []
                    console.log(coord);
                    // console.log(map)
                    map.getView().setCenter(ol.proj.transform([coord.lon, coord.lat], 'EPSG:4326', 'EPSG:3857'));
                    map.getView().setZoom(16);
                    addMarker(coord.lon + coord.lat, $('#title').serialize(), "Vous", coord.lat, coord.lon, requiredAddress);
                    $("#otherAddresses").show();

                    for (let i = 0; i < data.length; i++) {
                        addresses[i] = data[i]
                        console.log(data[i])
                        var $li = $("<li id='" + i + " 'class='bulist'>" + data[i].display_name + "</li>");
                        $li.on('click', function() {

                            map.getOverlays().getArray().slice(0)
                                .forEach(function(overlay) {
                                    map.removeOverlay(overlay);
                                });
                            console.log("I'm creating a marker at : [" + addresses[i].lat + ";" + addresses[i].lon + "]")
                            addMarker(addresses[i].lon + addresses[i].lat, $('#title').serialize(), "Vous", addresses[i].lat, addresses[i].lon, requiredAddress);
                            $('#address').val(addresses[i].display_name)

                        });
                        $("#otherAddresses > ol").append($li);
                    }
                }
            });
            $("#marker" + coord.lon + coord.lat).mouseover(function() {
                if ($("#popup" + coord.lon + coord.lat).is(":visible")) {
                    $("#popup" + coord.lon + coord.lat).hide();
                } else {
                    $("#popup" + coord.lon + coord.lat).show();
                }
            });

            $("#popup" + coord.lon + coord.lat).attr("style", "width:50%;height:100%;background-color:white;border-radius: 10px;padding:5px;").hide();


        }



        // console.log($("#MainContainer").height());
        // $("window").on("change", function() {
        //     $("#map").height = $("#MainContainer").height();
        // });
    </script>
    <script id="sMap" type="text/javascript">
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
                center: ol.proj.fromLonLat([coord.lon, coord.lat]),
                zoom: 5
            })
        });
    </script>


</body>

</html>