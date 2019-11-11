<?php
include_once('config.php');

if(isset($_POST['log'])){
    $login= $_POST['login'];
    $email= $_POST['email'];
    $pass= $_POST['pass'];


    if($user->login($login,$pass)){
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
                <tr>
                    <th>Titre</th><th>Date</th><th>Thème</th><th>Effectif Actuel</th>
                </tr>
                <?php
                

                if (isset($_GET['pagenb'])) {
                    $pagenb = $_GET['pagenb'];
                } else {
                    $pagenb = 1;
                }

                $nbrecpage = 10;
                $offset = ($pagenb-1) * $nbrecpage;

                try{
                    $stmt=$dbh->prepare("SELECT COUNT(*) FROM Evenement");
                    $stmt->execute();


                    $nbrow=$stmt->fetch(PDO::FETCH_ASSOC);
                    $nbtotpages=ceil((intval($nbrow['COUNT(*)'])/ $nbrecpage));

                    $stmt=$dbh->prepare("SELECT * FROM Evenement LIMIT :offset, :nbrec");

                    $stmt->bindParam(":nbrec",$nbrecpage,PDO::PARAM_INT);
                    $stmt->bindParam(":offset",$offset,PDO::PARAM_INT);
                    $stmt->execute();

                    foreach ($stmt as $row) {
                        echo "<tr>";
                        echo "<td><a href='contenu.php?lastevent={$row['ID_Event']}'>{$row['Titre']}</a></td><td>{$row['Date']}</td>";
                        echo "<td>{$row['Nom']}</td><td>{$row['EffectifActuel']}/{$row['EffectifMax']}</td>";
                        echo "</tr>";
                    }
                }
                catch(PDOException $e){
                    echo $e->getMessage();
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
            <?php if($tmp!=1){echo "<a href='event.php?pagenb=" . ($tmp-1) . "'>Préc</a>";}?>
            <?php   
            if ($nbtotpages <= 10){   
                for ($i = 1; $i <= $nbtotpages; $i++){
                    if ($i == $tmp) {
                        echo "<a class='active'>$i</a>"; 
                    }else{
                        echo "<a href='event.php?pagenb=$i'>$i</a>";
                    }
                }
            }else{
                if($pagenb <= 4) { 
                    for ($i = 1; $countei < 8; $i++){ 
                        if ($i == $pagenb) {
                            echo "<a class='active'>$i</a>"; 
                        }else{
                            echo "<a href='event.php?pagenb=$i'>$i</a>";
                        }
                    }
                    echo "<a>...</a>";
                    $second=$nbtotpages-1;
                    echo "<a href='event.php?pagenb=$second'>$second</a>";
                    echo "<a href='event.php?pagenb=$nbtotpages'>$nbtotpages</a>";
                }


                else if($pagenb > 4 && $pagenb < $total_no_of_pages - 4) { 
                    echo "<a href='event.php?pagenb=1'>1</a>";
                    echo "<a href='event.php?pagenb=2'>2</a>";
                    echo "<a>...</a>";

                    for ($i = $pagenb - $adjacents; $i <= $pagenb + $adjacents; $i++) { 
                        if ($i == $pagenb) {
                            echo "<a class='active'>$i</a>"; 
                        }else{
                            echo "<a href='event.php?pagenb=$i'>$i</a>";
                        }                  
                    }
                    echo "<a>...</a>";
                    $second=$nbtotpages-1;
                    echo "<a href='event.php?pagenb=$second'>$second</a>";
                    echo "<a href='event.php?pagenb=$nbtotpages'>$nbtotpages</a>";
                }
                else {
                    echo "<a href='event.php?pagenb=1'>1</a>";
                    echo "<a href='event.php?pagenb=2'>2</a>";
                    echo "<a>...</a>";
                    for ($i = $nbtotpages - 6; $i <= $nbtotpages; $i++) {
                        if ($i == $pagenb) {
                            echo "<a class='active'>$i</a>"; 
                        }else{
                            echo "<a href='event.php?pagenb=$i'>$i</a>";
                        }                   
                    }
                }
            }   
            ?>

            <?php if($tmp!=$nbtotpages){echo "<a href='event.php?pagenb=" . ($tmp+1) . "'>Suiv</a>";}?>
        </div>

    </div>
</div>
</body>

</html>
