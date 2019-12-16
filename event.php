<?php
include_once('config.php');

//Connexions utilisateurs
if (isset($_POST['log'])) {
    $login = trim($_POST['login']);
    $pass = trim($_POST['pass']);

    if ($user->login($login, $pass)) {
        $user->redirect('profile.php');
    } else {
        $error = "Informations incorrectes";
    }
} else if (isset($_POST['reg'])) {
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $pass = trim($_POST['pass']);


    if ($user->register($email, $login, $pass)) {
        $user->redirect('profile.php');
    } else {
        echo "ERROR<br>";
    }
}
if (isset($_GET['deco'])) {
    $user->logout();
    $user->redirect('index.php');
}

//Ordre
if (!isset($_GET['order'])) {
    $_GET['order'] = "Titre";
    $order = "Titre";
    $crois = 'DESC';
}
if (isset($_GET['crois'])) {
    $crois = $_GET['crois'];
}
if (isset($_GET['filt'])) {
    $order = $_GET['order'];
    $crois = $_GET['crois'];
}

if (isset($_GET['search'])) {
	$search_word = $_GET['search_word'];
} else {
	$search_word = "";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seek My Spot - Evénements</title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">


    <link rel="stylesheet" type="text/css" href="./style.css">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="form.js"></script>
    <script type="text/javascript" src="filter.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


</head>

<body>

    <div id="Menu">
    <table>
        <div class="dropdown" id="globe-btn">
            <a href="./index.php"><img src="./img/globe.png" /></a>
        </div>
        <table style="margin:0;">
            <th>
                <?php
                if ($user->isLoggedin()) {
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
            if ($user->isLoggedin()) {
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
    <div class="container">
		<div id="content">
			<form class='form-inline' method="get">
				<label for="search_word">Recherche: </label>
				<input class='form-control' type="text" name="search_word">
				<input class='form-control' type="submit" name="search" value='Rechercher'>
			</form>
			<table class="table table-hover">
				<button class="dropbtn" id="filterButton" onclick=filter()>Filtrer</button>
				<thead>
                    <tr>
                        <th><a href="./event.php?pagenb=1&order=Titre&crois=DESC">Titre</a></th>
                        <th><a href="./event.php?pagenb=1&order=Adresse&crois=DESC">Localisation</a></th>
                        <th><a href="./event.php?pagenb=1&order=Date&crois=DESC">Date</a></th>
                        <th><a href="./event.php?pagenb=1&order=Nom&crois=DESC">Thème</a></th>
                        <th><a href="./event.php?pagenb=1&order=EffectifActuel&crois=DESC">EffectifActuel</a></th>
                    </tr>
                    </thead>
				<tbody>
                                        <?php


                    if (isset($_GET['pagenb'])) { //Recupere la page courante
                        $pagenb = $_GET['pagenb'];
                    } else {
                        $pagenb = 1;
                    }

                    $nbrecpage = 20;    //Possiblement changeable
                    $offset = ($pagenb - 1) * $nbrecpage;

                    try {
                        $stmt = $dbh->prepare("SELECT COUNT(*) FROM Evenement");
                        $stmt->execute();
                        $order = $_GET['order'];

                        $nbrow = $stmt->fetch(PDO::FETCH_ASSOC);
                        $nbtotpages = ceil((intval($nbrow['COUNT(*)']) / $nbrecpage));

                        $stmt = $dbh->prepare("SELECT * FROM Evenement, Localisation WHERE Localisation.ID_Loc=Evenement.ID_Loc AND Evenement.Titre LIKE '%" . $search_word . "%' ORDER BY " . $order . " " . $crois . " LIMIT :offset, :nbrec");
                        $stmt->bindParam(":nbrec", $nbrecpage, PDO::PARAM_INT);
                        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
                        $stmt->execute();

                        foreach ($stmt as $row) {
                            echo "<tr>";
                            echo "<td><a href='contenu.php?lastevent={$row['ID_Event']}'>{$row['Titre']}</a></td><td>{$row['Adresse']}</td><td>{$row['Date']}</td>";
                            echo "<td>{$row['Nom']}</td><td>{$row['EffectifActuel']}/{$row['EffectifMax']}</td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "Erreur durant le chargement de la page de l'événement.";
                        die();
                    }
                    ?></tbody>
                </table>
                
            <div class='pagination'>
                <?php
                //Pagination
                $tmp = (int) $pagenb;
                ?>
                <?php if ($tmp != 1) {
               						echo "<a href='event.php?pagenb=" . ($tmp - 1) . "&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>Préc</a>";
                                } ?>
                <?php
                if ($nbtotpages <= 10) {
                    for ($i = 1; $i <= $nbtotpages; $i++) {
                        if ($i == $tmp) {
                            echo "<a class='active'>$i</a>";
                        } else {
                            echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$i</a>";
                        }
                    }
                } else {
                    if ($pagenb <= 4) {
                        for ($i = 1; $countei < 8; $i++) {
                            if ($i == $pagenb) {
                                echo "<a class='active'>$i</a>";
                            } else {
                                echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$i</a>";
						    }
                        }
                        echo "<a>...</a>";
                        $second = $nbtotpages - 1;
                        echo "<a href='event.php?pagenb=$second&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$second</a>";
                        echo "<a href='event.php?pagenb=$nbtotpages&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$nbtotpages</a>";
                    } else if ($pagenb > 4 && $pagenb < $total_no_of_pages - 4) {
                        echo "<a href='event.php?pagenb=1&order" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>1</a>";
                        echo "<a href='event.php?pagenb=2&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>2</a>";
                        echo "<a>...</a>";

                        for ($i = $pagenb - $adjacents; $i <= $pagenb + $adjacents; $i++) {
                            if ($i == $pagenb) {
                                echo "<a class='active'>$i</a>";
                            } else {
                                echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$i</a>";
                            }
                        }
                        echo "<a>...</a>";
                        $second = $nbtotpages - 1;
                        echo "<a href='event.php?pagenb=$second&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$second</a>";
                        echo "<a href='event.php?pagenb=$nbtotpages&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$nbtotpages</a>";
                    } else {
                        echo "<a href='event.php?pagenb=1&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>1</a>";
                        echo "<a href='event.php?pagenb=2&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>2</a>";
                        echo "<a>...</a>";
                        for ($i = $nbtotpages - 6; $i <= $nbtotpages; $i++) {
                            if ($i == $pagenb) {
                                echo "<a class='active'>$i</a>";
                            } else {
                                echo "<a href='event.php?pagenb=$i&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>$i</a>";
                            }
                        }
                    }
                }
                if ($tmp != $nbtotpages) {
                    echo "<a href='event.php?pagenb=" . ($tmp + 1) . "&order=" . $_GET['order'] . "&crois=" . $crois . "&search_word=" . $search_word . "'>Suiv</a>";
                }
                //Fin pagination
            ?>
            </div>

        </div>
    </div>
</body>

</html>