<?php
include_once('config.php');

if(isset($_GET['deco'])){  //L'utilisateur se deconnecte avec le boutton de "Se deconnecter"
$user->logout();
$user->redirect('index.php');
}

if(!isset($_GET['lastevent'])){  //lastevent represente l'evenement que dont on va afficher les details
$user->redirect('event.php');
} else {
    try{
        $stmt=$dbh->prepare("SELECT * FROM Evenement, Localisation WHERE Evenement.ID_Loc=Localisation.ID_Loc AND ID_Event=:levent");  //Recuperation des informations de l'evenement
        $stmt->bindParam(":levent", $_GET['lastevent']);
        $stmt->execute();
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if($stmt->rowCount()){
            $titre=$row['Titre'];
            $idevent=$row['ID_Event'];
        }
        else{
            $erreur="Quelque chose s'est mal passée, l'événement est introuvable";
        }
        $event=$row;
    }
    catch(PDOException $e){
        $erreur="Quelque chose s'est mal passée, l'événement est introuvable";
    }
}

if(isset($_POST['log'])){           //Si l'utilisateur s'enregistre ou se connecte sur cette page
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
    $error="Information incorrectes";
}
} else if(isset($_POST['reg'])){
    $login= trim($_POST['login']);
    $email= trim($_POST['email']);
    $pass= trim($_POST['pass']);


    if($user->register($email,$login,$pass)){
        $user->redirect('profile.php');
    }
    else{
        $erreur="Impossible de vous enregistre";

    }
}

if(isset($_GET['inscription']) && $user->isLoggedin()){ //Si l'utilisateur s'inscrit a l'evenement
try{
        //Insertion de l'utilisateur dans la table S_inscrit
    $stmt=$dbh->prepare("INSERT INTO S_inscrit VALUES(:levent, :ulogin)");
    $stmt->bindParam(":levent",$_GET['lastevent']);
    $stmt->bindParam(":ulogin",$_SESSION['user_session']);
    $stmt->execute();

        //Mise à jour de EffectifActuel dans l'Evenement
    $stmt=$dbh->prepare("UPDATE Evenement SET EffectifActuel=EffectifActuel+1 WHERE ID_Event=:levent");
    $stmt->bindParam(":levent",$_GET['lastevent']);
    $stmt->execute();
    $row['EffectifActuel']++;
    $user->redirect("contenu.php?lastevent=" . $_GET['lastevent']);
}
catch(PDOException $e){
    $erreur="Inscription impossible";
}
}

if(isset($_GET['desinscription']) && $user->isLoggedin()){ //Si l'utilisateur se desinscrit a l'evenement
try{
        //Suppression de l'utilisateur de la table S_inscrit 
    $stmt=$dbh->prepare("DELETE FROM S_inscrit WHERE ID_Event=:levent AND  login=:ulogin");
    $stmt->bindParam(":levent",$_GET['lastevent']);
    $stmt->bindParam(":ulogin",$_SESSION['user_session']);
    $stmt->execute();

        //Mise à jour de EffectifActuel dans l'Evenement
    $stmt=$dbh->prepare("UPDATE Evenement SET EffectifActuel=EffectifActuel-1 WHERE ID_Event=:levent");
    $stmt->bindParam(":levent",$_GET['lastevent']);
    $stmt->execute();
    $row['EffectifActuel']--;
    $user->redirect("contenu.php?lastevent=" . $_GET['lastevent']);
}
catch(PDOException $e){
    $erreur="Erreur (c'est pas normal...)";
}
}

if(isset($_POST['comm'])){
    try{
        unset($_POST['comm']);
        $_POST['comm']=[];
        $stmt=$dbh->prepare("INSERT INTO Commentaire(ID_Event,login,commentaire) VALUES(:idevent,:login,:comm)");
        $stmt->bindParam(":idevent",$idevent);
        $stmt->bindParam(":login",$_SESSION['user_session']);
        $stmt->bindParam(":comm",$_POST['message']);
        $stmt->execute();
    }
    catch(PDOException $e){
        $erreur="Impossible de poster votre commentaire";
    }
}

//Date de l'evenement et date actuelle
$eventDate=strtotime($event['Date']);
$now=strtotime(date("Y-m-d"));
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/css/ol.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>
    <title><?php echo $row['Titre'] ?> - <?php echo $row['login'] ?></title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>

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
    <div id ="up">
        <a href="#Menu"><img id="arrow" src="img/up.png"/></a>
    </div>

    <div id="MainContainer">
        <div class="pacc">
            <p>
                <h1><b><?php echo $row['Titre']; ?></b>
                </h1>
            </p>
        </div>
        <img id="markerProto" class="marker" src="./img/marker.png" style='width: 50px; height: 50px; display: none;' />

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
                    center: ol.proj.fromLonLat([<?php echo $row['Longitude'] . ", " . $row['Latitude']; ?>]),
                    zoom: 17
                })
            });

            let id = <?php echo $row['ID_Event'] ?>;
            let image = $("#markerProto").clone();
            image.attr("id", "marker" + id).attr('style', 'display:block').attr('height', '70px').attr('width', '50px');
            $("body").append(image);
            var marker = document.getElementById('marker' + id);
            map.addOverlay(new ol.Overlay({
                position: ol.proj.fromLonLat([<?php echo $row['Longitude'] . ", " . $row['Latitude']; ?>]),
                positioning: 'bottom-right',
                element: marker
            }));
        </script>
        <div>
            <p>Date: <?php echo $event['Date'];?></p>
        </div>
        <div id="desc">
            <p><b>Description:</b><br> <?php echo $row['Descriptif']; ?></p>
        </div>
        <div>
            <?php
            try {
                $stmt = $dbh->prepare("SELECT * FROM S_inscrit WHERE login=:ulogin AND ID_Event=:levent");
                $stmt->bindParam(":ulogin", $_SESSION['user_session']);
                $stmt->bindParam(":levent", $_GET['lastevent']);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if($stmt->rowCount()){
                    $userReg=true;
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            if ($user->isLoggedin() && $eventDate>$now) {

                if (!$stmt->rowCount()) {
                    echo "<button><a href='contenu.php?lastevent=" . $_GET['lastevent'] . "&inscription=true'>S'inscrire</a></button>";
                } else {
                    echo "<b>Vous êtes inscrit à cet évènement.</b><br>";
                    echo "<button><a href='contenu.php?lastevent=" . $_GET['lastevent'] . "&desinscription=true'>Se désinscrire</a></button>";
                }
                
            } else if($eventDate<$now){
                echo "L'évènement est passé.<br>";
            }else{
                echo '<button onclick=' . '"' . "alert('Vous devez être connecté(e) pour pouvoir vous inscrire')" . '"' . ">S'inscrire</button>";
            }
            ?>
        </div>
        <div>
            <div id="comzone">
                <?php
                try{
                        //Affichage des commentaires
                    $stmt=$dbh->prepare("SELECT * FROM Commentaire WHERE ID_Event=:idevent");
                    $stmt->bindParam(":idevent",$idevent);
                    $stmt->execute();

                    foreach($stmt as $row){
                        echo"<p><h4><b>{$row['login']}</b></h4>";
                        echo $row['commentaire'];
                        echo "</p>";
                    }
                }
                catch(PDOException $e){
                    echo $e->getMessage();
                }
                ?>
            </div>
            <?php
            if($eventDate<=$now && isset($userReg)){
                //TODO: Ajouter le CSS pour faire des étoiles
                echo "<form method='post' id='chat_form'>
                <textarea name='message' id='message' rows='5' cols='50' maxlength=300 placeholder='Dites quelque chose...'></textarea><br />
                <input type='submit' name='comm' id='send_message' value='Envoyer'/>
                <div class='rating'>
                <span><input type='radio' name='rating' id='str5' value='5'></span>
                <span><input type='radio' name='rating' id='str4' value='4'></span>
                <span><input type='radio' name='rating' id='str3' value='3'></span>
                <span><input type='radio' name='rating' id='str2' value='2'></span>
                <span><input type='radio' name='rating' id='str1' value='1'></span>
                </div>
                </form>";
            }
            ?>
        </div>
    </div>
</body>
</html>