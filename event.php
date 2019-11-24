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

//Ordre
if(!isset($_GET['order'])){
    $_GET['order']="Titre";
    $order="Titre";
    $crois='DESC';
}
if(isset($_GET['crois'])){
    $crois=$_GET['crois'];
}
if(isset($_GET['filt'])){
    $order=$_GET['order'];
    $crois=$_GET['crois'];
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
    <script type="text/javascript" src="filter.js"></script>

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
            <table id="listevent">
                <button class="dropbtn" id="filterButton" onclick=filter()>Filtrer</button>
                <tr>
                    <th><a href="./event.php?pagenb=1&order=Titre&crois=DESC">Titre</a></th><th><a href="./event.php?pagenb=1&order=Adresse&crois=DESC">Localisation</a></th><th><a href="./event.php?pagenb=1&order=Date&crois=DESC">Date</a></th>
                    <th><a href="./event.php?pagenb=1&order=Nom&crois=DESC">Thème</a></th><th><a href="./event.php?pagenb=1&order=EffectifActuel&crois=DESC">EffectifActuel</a></th>
                </tr>
                <?php
                

                if (isset($_GET['pagenb'])) { //Recupere la page courante
                    $pagenb = $_GET['pagenb'];
                } else {
                    $pagenb = 1;
                }

                $nbrecpage = 10;    //Possiblement changeable
                $offset = ($pagenb-1) * $nbrecpage;

                try{
                    $stmt=$dbh->prepare("SELECT COUNT(*) FROM Evenement");
                    $stmt->execute();
                    $order=$_GET['order'];

                    $nbrow=$stmt->fetch(PDO::FETCH_ASSOC);
                    $nbtotpages=ceil((intval($nbrow['COUNT(*)'])/ $nbrecpage));

                    $stmt=$dbh->prepare("SELECT * FROM Evenement, Localisation WHERE Localisation.ID_Loc=Evenement.ID_Loc ORDER BY " . $order . " " . $crois . " LIMIT :offset, :nbrec");
                    $stmt->bindParam(":nbrec",$nbrecpage,PDO::PARAM_INT);
                    $stmt->bindParam(":offset",$offset,PDO::PARAM_INT);
                    $stmt->execute();

                    foreach ($stmt as $row) {
                        echo "<tr>";
                        echo "<td><a href='contenu.php?lastevent={$row['ID_Event']}'>{$row['Titre']}</a></td><td>{$row['Adresse']}</td><td>{$row['Date']}</td>";
                        echo "<td>{$row['Nom']}</td><td>{$row['EffectifActuel']}/{$row['EffectifMax']}</td>";
                        echo "</tr>";
                    }
                }
                catch(PDOException $e){
                    echo "Erreur durant le chargement de la page";
                    die();
                }
                ?>
            </table>
        </p>
        <div class='pagination'>
            <?php 
            //Pagination
            $tmp=(int) $pagenb;
            ?>
            <?php if($tmp!=1){echo "<a href='event.php?pagenb=" . ($tmp-1) . "&order=" . $_GET['order'] . "&crois=" . $crois . " '>Préc</a>";}?>
            <?php   
            if ($nbtotpages <= 10){   
                for ($i = 1; $i <= $nbtotpages; $i++){
                    if ($i == $tmp) {
                        echo "<a class='active'>$i</a>"; 
                    }else{
                        echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "'>$i</a>";
                    }
                }
            }else{
                if($pagenb <= 4) { 
                    for ($i = 1; $countei < 8; $i++){ 
                        if ($i == $pagenb) {
                            echo "<a class='active'>$i</a>"; 
                        }else{
                            echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "'>$i</a>";
                        }
                    }
                    echo "<a>...</a>";
                    $second=$nbtotpages-1;
                    echo "<a href='event.php?pagenb=$second&order=" . $_GET['order'] . "&crois=" . $crois . "'>$second</a>";
                    echo "<a href='event.php?pagenb=$nbtotpages&order=" . $_GET['order'] . "&crois=" . $crois . "'>$nbtotpages</a>";
                }


                else if($pagenb > 4 && $pagenb < $total_no_of_pages - 4) { 
                    echo "<a href='event.php?pagenb=1&order" . $_GET['order'] . "&crois=" . $crois . "'>1</a>";
                    echo "<a href='event.php?pagenb=2&order=" . $_GET['order'] . "&crois=" . $crois . "'>2</a>";
                    echo "<a>...</a>";

                    for ($i = $pagenb - $adjacents; $i <= $pagenb + $adjacents; $i++) { 
                        if ($i == $pagenb) {
                            echo "<a class='active'>$i</a>"; 
                        }else{
                            echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "'>$i</a>";
                        }                  
                    }
                    echo "<a>...</a>";
                    $second=$nbtotpages-1;
                    echo "<a href='event.php?pagenb=$second&order=" . $_GET['order'] . "&crois=" . $crois . "'>$second</a>";
                    echo "<a href='event.php?pagenb=$nbtotpages&order=" . $_GET['order'] . "&crois=" . $crois . "'>$nbtotpages</a>";
                }
                else {
                    echo "<a href='event.php?pagenb=1&order=" . $_GET['order'] . "&crois=" . $crois . "'>1</a>";
                    echo "<a href='event.php?pagenb=2&order=" . $_GET['order'] . "&crois=" . $crois . "'>2</a>";
                    echo "<a>...</a>";
                    for ($i = $nbtotpages - 6; $i <= $nbtotpages; $i++) {
                        if ($i == $pagenb) {
                            echo "<a class='active'>$i</a>"; 
                        }else{
                            echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "'>$i</a>";
                        }                   
                    }
                }
            }   
            if($tmp!=$nbtotpages){
                echo "<a href='event.php?pagenb=" . ($tmp+1) . "&order=" . $_GET['order'] . "&crois=" . $crois . "'>Suiv</a>";
            }
            //Fin pagination
        ?>
        </div>

    </div>
</div>
</body>

</html>
