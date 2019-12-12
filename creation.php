<?php
include_once('config.php');
if(!$user->isLoggedin() || !$user->isContributor()){
    $user->redirect("index.php");
}

if(isset($_GET['deco'])){
    $user->logout();
    $user->redirect('index.php');
}

if(isset($_POST['create'])){
    $user->createEvent($_POST['title'],$_POST['theme'],$_POST['date'],$_POST['adress'],$_POST['effect'],$_POST['desc']);
    $user->redirect("profile.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Seek My Spot</title>
    
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./style.css">
    
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div id="Menu">
        <table>
            <th>
                <div class="dropdown">
                    <a href="./profile.php"><button class="dropbtn">Mon profil</button></a>
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
    <div id ="up">
        <a href="#Menu"><img id="arrow" src="img/up.png"/></a>
    </div>
    <div id="container">
        <div id="content">
            <h1>Création d'événements:</h1>
            <form method='post'>
                <div class='form-group'>
                    <label for="title">Titre:</label><br />
                    <input class='form-control' type="text" name="title" id="title" placeholder="Titre" required><br />
                    <label for="theme">Thème:</label><br />
                    <select class='form-control' type="select" name="theme" id="theme" >
                        <option value="" selected>Choisir</option>
                        <?php
                        $stmt=$dbh->prepare('SELECT Nom FROM Theme');
                        $stmt->execute();
                        foreach($stmt as $row){
                            echo "<option value='$row[0]'>$row[0]</option>";
                        }
                        ?>
                    </select><br />
                    <label for="date">Date:</label><br />
                    <input class='form-control' type="date" name="date" value="date" required/><br />
                    <label for="adress">Adresse: </label><br />
                    <input class='form-control' type="text" name="adress" placeholder="Adresse" required/><br />
                    <label for="effect">Effectif maximum:</label><br />
                    <input class='form-control' type="number" name="effect" value=0 required/><br />
                    <label for="desc">Description: </label><br />
                    <textarea class='form-control' name="desc" id="desc" rows="5" cols="50" maxlength=300 placeholder="Description(300 caractères max)"></textarea><br />

                    <input class='btn btn-primary' type="submit" value="Créer" name="create"/>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
