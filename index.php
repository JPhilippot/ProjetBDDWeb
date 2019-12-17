<?php
include_once('config.php');

//Connexions utilisateur

//Connexions utilisateur
if($user->isLoggedin()){
    $user->redirect('profile.php');
}
else if(isset($_POST['log'])){
    $login= trim($_POST['login']);
    $pass= trim($_POST['pass']);


    if($user->login($login,$pass)){
        if(isset($_POST['remember'])){
            $cookie_name="user";
            $cookie_value=$_SESSION['user_session'];
            setcookie($cookie_name,$cookie_value, time() + (86400 * 30));
        }
        if($user->isAdministrateur()){
            $user->redirect('admin.php');
        }
        else{
            $user->redirect('profile.php');
        }
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
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Seek My Spot</title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="./style.css">
    <?php if (isset($error)) {
        echo "<script>alert('Erreur: " . $error . "')</script>";
    } //Affiche un message d'erreur 
    ?>

</head>

<body>
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
    <div id="MainContainer">

        <div class="pacc">
            <p>
                <h1><b>Bienvenue sur Seek My Spot !</b>
                </h1>
                <b>[Description du site]</b>
                {
                Vestibulum a placerat leo. Nunc hendrerit tempor tincidunt. Duis tincidunt mattis neque nec vestibulum.
                In imperdiet massa est, sit amet fermentum augue dignissim vitae.
                Maecenas gravida semper nunc, sit amet ultricies diam finibus vitae.
                Proin fringilla nisl sit amet nunc laoreet fermentum. Donec pellentesque finibus ante sed vehicula.
                Nullam porta id elit et cursus. Morbi semper laoreet laoreet. Curabitur pharetra ultrices eleifend.
                }
            </p>
        </div>
        <p>

        </p>

        <div class="pacc">
            <p>
                <img class="right" src="img/screenshot1.png" />
                In ac lorem sed quam porta iaculis nec mollis turpis. Aliquam mattis at neque sed bibendum.
                In porttitor interdum accumsan. Vestibulum quis dolor nec purus finibus laoreet elementum id mauris.
                Integer tincidunt elementum lorem, ac dapibus erat luctus eget. Duis congue sem ante, id blandit nisl
                varius cursus. In hac habitasse platea dictumst. Phasellus nec metus blandit, convallis mi eu, fringilla
                massa. Donec fermentum nisl felis, vel mattis urna efficitur sit amet. Nulla tincidunt id diam vitae
                suscipit. Pellentesque metus dui, condimentum id nisi ut, consectetur consectetur erat.
                \n
                <b>[Bouton en bootstrap qui mène vers la fonctionalité décrite]</b>
            </p>
        </div>
        <div class="pacc">
            <p>
                Donec eget ligula ac tortor suscipit ultricies. Quisque elementum lacus at ex maximus, a auctor
                ligula sollicitudin. Maecenas imperdiet ex ac enim finibus, vel accumsan justo ultrices.
                Etiam pharetra orci ut est vehicula faucibus. Duis semper mauris felis, egestas hendrerit elit
                malesuada at. Nunc id justo congue, semper urna et, condimentum tellus. Quisque sollicitudin luctus
                turpis id congue. In lacinia vulputate risus. Sed at ultricies purus. Praesent ut imperdiet erat.
                Mauris quam dolor, condimentum a tincidunt id, pellentesque ac massa. Ut vestibulum facilisis feugiat.
                Suspendisse accumsan risus ut massa vestibulum, vitae luctus ligula cursus. Nullam non accumsan ante,
                vitae ullamcorper leo. \n
                <b>[Bouton en bootstrap qui mène vers la fonctionalité décrite]</b>

            </p>
        </div>
        <div class="pacc">
            <p>
                <img class="left" src="img/screenshot1.png" />
                Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;
                Ut tincidunt pulvinar dolor id elementum. In ut tincidunt augue. Nunc mattis at libero id lacinia.
                Phasellus a rutrum ex. Vivamus eget euismod nulla, et mattis ante. Etiam luctus urna vel tellus
                scelerisque sollicitudin sit amet non augue. Nullam nec orci sit amet orci maximus vestibulum.
                Praesent lacinia, sapien sit amet suscipit tempus, odio tortor faucibus metus, quis mollis diam
                diam nec nisi. In pharetra suscipit sem eget vulputate. Suspendisse quis justo id felis gravida
                luctus quis vel nibh. Integer cursus id nunc quis suscipit. Vestibulum venenatis sit amet nisi vitae
                laoreet. Vestibulum cursus felis dolor, nec convallis justo pharetra vel.
                \n <b>[Bouton en bootstrap qui mène vers la fonctionalité décrite]</b>
            </p>
        </div>
    </div>
    <footer>
        <div class="footer-copyright text-center py-3" style="color: white; font-size:smaller">The icons used in our website were designed by
            <a href="https://www.elegantthemes.com">ElegantThemes.com</a>. They can be found
            <a href="https://www.elegantthemes.com/blog/freebie-of-the-week/beautiful-flat-icons-for-free">there </a> for free !
        </div>

    </footer>
</body>

</html>