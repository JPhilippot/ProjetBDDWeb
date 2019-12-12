<?php
/**
 * User est une classe qui permet de gérer les utilisateurs.
 *
 * @author Aurelien Besnier <https://github.com/AurelienBesnier>
 * @version 1.00
 * @see  https://github.com/JPhilippot/ProjetBDDWeb
 */
class User{
    private $dbh;

    function __construct($dbh){
        $this->dbh=$dbh;
    }
    
    /**
     * Permet a un utilisateur de s'enregistrer dans la bd
     *
     * @param $email string representant un email
     * @param $login string representant le nom d'utilisateur
     * @param $pass string representant le mot de passe utilisateur
     *
     * @throws PDOException
     *
     * @return PDOStatment
     * @access public
     */
    public function register($email, $login, $pass){ //TODO: Faire en sorte que les admin puissent se co
        try{
            $hash=password_hash($pass,PASSWORD_DEFAULT);
            echo strlen($hash) . "<br>";
            $stmt=$this->dbh->prepare("INSERT INTO Visiteur(email,login,password) VALUES( :uemail, :ulogin, :upass);");

            $stmt->bindParam(":uemail",$email);
            $stmt->bindParam(":ulogin",$login);
            $stmt->bindParam(":upass",$hash);
            $stmt->execute();
            $_SESSION['user_session']=$login;
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Permet a un utilisateur de se connecter a la bd
     *
     * @param $email string representant un email
     * @param $login string representant le nom d'utilisateur
     * @param $pass string representant le mot de passe utilisateur
     *
     * @throws PDOException
     *
     * @return true si l'utilisateur existe dans la bd, false sinon
     * @access public
     */
    public function login($login,$pass): bool{
        try{
            //Verification compte Visiteur
            $stmt=$this->dbh->prepare("SELECT * FROM Visiteur WHERE login=:ulogin OR email=:ulogin LIMIT 1");
            $stmt->execute(array(":ulogin"=>$login));
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            
            if($stmt->rowCount()){
                if(password_verify($pass,$row['password'])){
                    $_SESSION['user_session']=$login;

                    return true;
                } 
            }

            //Verification compte Administrateur
            $stmt=$this->dbh->prepare("SELECT * FROM Administrateur WHERE login=:ulogin OR email=:ulogin LIMIT 1");
            $stmt->bindParam(":ulogin",$login);
            $stmt->execute();
            $row=$stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount()){
                if(password_verify($pass,$row['password'])){
                    $_SESSION['user_session']=$login;

                    return true;
                }
            }
            else{
                return false;
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Determine si l'utilisateur est un contributeur
     *
     * @throws PDOException
     * @return bool true si l'utilisateur est contributeur false sinon
     * @access public
     */
    public function isContributor():bool{
        try{
            $stmt=$this->dbh->prepare("SELECT * FROM Contributeur WHERE login=:ulogin LIMIT 1");
            $stmt->bindParam(":ulogin",$_SESSION['user_session']);
            $stmt->execute();
            if($stmt->rowCount()){
                return true;
            }else{
                return false;
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Determine si l'utilisateur est un administrateur
     *
     * @rework ne marche pas
     * @throws PDOException
     * @return bool true si l'utilisateur est administrateur false sinon
     */
    public function isAdministrateur():bool{
        try{
            $stmt=$this->dbh->prepare("SELECT * FROM Administrateur WHERE login=:ulogin LIMIT 1");
            $stmt->bindParam(":ulogin",$_SESSION['user_session']);
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount()){
                return true;
            }else{
                return false;
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }


    /**
     * Enregistre l'utilisateur dans la table des contributeurs
     *
     * @throws PDOException
     * @return true si tout c'est bien passé
     * @access public
     */
    public function setContributeur():bool{
        try{

            $stmt=$this->dbh->prepare("SELECT * FROM Visiteur WHERE login=:ulogin LIMIT 1");
            $stmt->bindParam(":ulogin", $_SESSION['user_session']);
            $stmt->execute();
            $row=$stmt->fetch(PDO::FETCH_ASSOC);

            $stmt=$this->dbh->prepare("INSERT INTO Contributeur(login,email,password) VALUES(:ulogin,:uemail,:upass)");
            $stmt->execute(array(":ulogin"=>$_SESSION['user_session'],":uemail"=>$row['email'],":upass"=>$row['password']));
            return true;
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Determine si l'utilisateur est logge(e)
     *
     * @return bool true si l'utilisateur est connecté false sinon
     * @access public
     */
    public function isLoggedin(): bool{
        return isset($_SESSION['user_session']);
    }

    /**
     * Redirige l'utilisateur sur la page $url
     *
     * @param string representant l'url d'un page du site
     * @access public
     */
    public function redirect($url){
        header("Location: $url");
    }

    /**
     * affiche la liste des evenements auquels l'utilisateur est inscrit
     *
     * @throws PDOException
     * @access public
     */
    public function listevent(){
        try{
            $stmt=$this->dbh->prepare("SELECT Evenement.ID_Event, Titre, Date , Adresse FROM Evenement, Localisation, S_inscrit WHERE Localisation.ID_Loc=Evenement.ID_Loc AND Evenement.ID_Event=S_inscrit.ID_Event AND S_inscrit.login=:ulogin");   
            $stmt->bindParam(":ulogin",$_SESSION['user_session']);
            $stmt->execute();

            $tab=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount()){
                echo "<ul>";
                for ($i=0; $i<$stmt->rowCount();$i++){
                    echo "<li><a href='./contenu.php?lastevent=" . $tab['ID_Event'] . "'>" . $tab['Titre'] . "</a> le " . $tab['Date'] . " à " . $tab['Adresse'] . " </li>";
                    $tab=$stmt->fetch(PDO::FETCH_ASSOC);
                }
                echo "</ul><br>";
            } else {
                echo "Vous êtes inscrit a aucun événement.<br>";
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Affiche la liste des événements auquels le contributeur
     *
     * @throws PDOException
     * @access public
     */
    public function listeventCree(){
        try{
            $stmt=$this->dbh->prepare("SELECT Evenement.ID_Event, Titre, Date , Adresse FROM Evenement, Localisation WHERE Localisation.ID_Loc=Evenement.ID_Loc AND login=:ulogin");
            $stmt->bindParam(":ulogin",$_SESSION['user_session']);
            $stmt->execute();

            $tab=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount()){
                echo "<ul>";
                for ($i=0; $i<$stmt->rowCount();$i++){
                    echo "<li><a href='./contenu.php?lastevent=" . $tab['ID_Event'] . "'>" . $tab['Titre'] . "</a> le " . $tab['Date'] . " à " . $tab['Adresse'] . "<a href='./profile.php?delete=true&event={$tab['ID_Event']}'> <button class='btn btn-warning'>Supprimer</button></a></li>";
                    //Mettre un bouton pour le supprimer
                    $tab=$stmt->fetch(PDO::FETCH_ASSOC);
                }
                echo "</ul><br>";
            } else {
                echo "Vous n'avez crée aucun événement.<br>";
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Crée un evenement avec les informations fourni dans creation.php
     *
     * @param $title string le titre de l'événement
     * @param $theme string le theme de l'événement
     * @param $date date la date de l'événement
     * @param $adress string l'adresse de l'événement
     * @param $eff int l'effectif maximum
     * @param $desc string une description de l'événement
     *
     * @throws PDOException
     * @return bool true si tout se passe bien
     * @access public
     */
    public function createEvent($title, $theme, $date, $adress, $eff, $desc): bool{
        try{
            $stmt=$this->dbh->prepare("SELECT ID_Loc FROM Localisation WHERE Adresse=:adress");
            $stmt->bindParam(":adress",$adress);
            $stmt->execute();
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount()){
                //Create Local ?
                //$stmt=$this->dbh->prepare("INSERT INTO ")
            }

            $stmt=$this->dbh->prepare("INSERT INTO Evenement(Titre,Date,EffectifMax,Descriptif,EffectifActuel,login,ID_Loc,Nom) VALUES(:title,:date,:eff,:desc,0,:login,:loc,:nom)");
            $stmt->bindParam(":title",$title);
            $stmt->bindParam(":date",$date);
            $stmt->bindParam(":eff",$eff);
            $stmt->bindParam(":desc",$desc);
            $stmt->bindParam(":login",$_SESSION['user_session']);
            $stmt->bindParam(":loc",$row['ID_Loc']);
            $stmt->bindParam(":nom",$theme);
            $stmt->execute();
            return true;
        }
        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Détruit la session de l'utilisateur
     *
     * @return true si tout se passe bien
     * @access public
     */
    public function logout(): bool{
        //session_destroy();            //->pas utiliser sur la fac
        unset($_SESSION['user_session']);
        $_SESSION=[];
        return true;
    }
}
?>
