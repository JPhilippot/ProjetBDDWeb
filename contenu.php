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

if(isset($_POST['log'])){           //Si l'utilisateur s'enregistre ou se connecte sur cette page
    $login= $_POST['login'];
    $email= $_POST['email'];
    $pass= $_POST['pass'];


    if($user->login($email,$login,$pass)){
        if(isset($_POST['remember'])){
            $cookie_name="user";
            $cookie_value=$_SESSION['user_session'];
            setcookie($cookie_name,$cookie_value, time() + (86400 * 30));
        }
        $user->redirect('profile.php');
    }
    else{
        echo "Invalid credentials<br>";
    }
} else if(isset($_POST['reg'])){
    $login= $_POST['login'];
    $email= $_POST['email'];
    $pass= $_POST['pass'];


    if($user->register($email,$login,$pass)){
        $user->redirect('profile.php');
    }
    else{
        echo "ERROR<br>";
    }
}

if(isset($_GET['inscription']) && $user->isLoggedin()){
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

if(isset($_GET['desinscription']) && $user->isLoggedin()){
    //delete dans la table
    try{
        $stmt=$dbh->prepare("DELETE FROM S_inscrit WHERE ID_Event=:levent AND  login=:ulogin");
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
<script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>
<title>Nom du site</title>

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
            //afficher le bouton que si l'utilisateur n'est pas incrit -> requete
            try{
                $stmt=$dbh->prepare("SELECT * FROM S_inscrit WHERE login=:ulogin AND ID_Event=:levent");
                $stmt->bindParam(":ulogin",$_SESSION['user_session']);
                $stmt->bindParam(":levent",$_GET['lastevent']);
                $stmt->execute();
                $row=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$stmt->rowCount()){
                    echo "<button><a href='contenu.php?lastevent=" . $_GET['lastevent'] . "&inscription=true'>S'inscrire</a></button>";        
                } else {
                    echo "<b>Vous êtes inscrit à cet évènement.</b><br>";
                    echo "<button><a href='contenu.php?lastevent=" . $_GET['lastevent'] . "&desinscription=true'>Se désinscrire</a></button>";
                }
            }
            catch(PDOException $e){
                echo $e->getMessage();
            }
             
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
                    <h4><b>Jean-Martin de Garonne :</b></h4>
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