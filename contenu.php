<?php
include_once('config.php');

if(isset($_GET['deco'])){
    $user->logout();
    $user->redirect('index.php');
}

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
        echo "ERROR<br>";
    }
}

if(isset($_GET['inscription']) && $user->isLoggedin()){
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
        echo $e->getMessage();
        die();
    }
}

if(isset($_GET['desinscription']) && $user->isLoggedin()){
    //delete dans la table
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
        echo $e->getMessage();
        die();
    }
}
?>
<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/css/ol.css" type="text/css">
        <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="form.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.0/build/ol.js"></script>

        <title>Seek My Spot</title>

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
                echo "Effectif: {$row['EffectifActuel']}/{$row['EffectifMax']}<br>";
                $eff=$row['EffectifActuel']; $max=$row['EffectifMax'];
                if($user->isLoggedin()){
                    try{
                        $stmt=$dbh->prepare("SELECT * FROM S_inscrit WHERE login=:ulogin AND ID_Event=:levent");
                        $stmt->bindParam(":ulogin",$_SESSION['user_session']);
                        $stmt->bindParam(":levent",$_GET['lastevent']);
                        $stmt->execute();
                        $row=$stmt->fetch(PDO::FETCH_ASSOC);

                        if($eff<$max){
                            if(!$stmt->rowCount()){
                                echo "<button><a href='contenu.php?lastevent={$_GET['lastevent']}&inscription=true'>S'inscrire</a></button>";
                            } else {
                                echo "<b>Vous êtes inscrit à cet évènement.</b><br>";
                                echo "<button><a href='contenu.php?lastevent={$_GET['lastevent']}&desinscription=true'>Se désinscrire</a></button>";
                            }
                        }
                    }
                    catch(PDOException $e){
                        echo $e->getMessage();
                        die();
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
