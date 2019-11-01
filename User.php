<?php

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
     */
    public function register($email, $login, $pass){  //ou sign up, bref pour s'enregister pour la premiere fois
    try{
        $hash=password_hash($pass,PASSWORD_DEFAULT);
        echo strlen($hash) . "<br>";
        $stmt=$this->dbh->prepare("INSERT INTO Visiteur(email,login,password) VALUES( :uemail, :ulogin, :upass);");

        $stmt->bindParam(":uemail",$email);
        $stmt->bindParam(":ulogin",$login);
        $stmt->bindParam(":upass",$hash);
        $stmt->execute();
        $_SESSION['user_session']=$login;

        return $stmt;
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
     */
    public function login($email,$login,$pass){
        try{
            $stmt=$this->dbh->prepare("SELECT * FROM Visiteur WHERE login=:ulogin OR email=:uemail LIMIT 1");
            $stmt->execute(array(":ulogin"=>$login,":uemail"=>$email));
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount()){
                if(password_verify($pass,$row['password'])){
                    $_SESSION['user_session']=$login;

                    return true;
                } else {
                    return false;
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * @return booleen true si l'utilisateur est connecté false sinon
     */
    public function isLoggedin(){
        return isset($_SESSION['user_session']);
    }

    /**
     * @param string representant l'url d'un page du site
     * Redirige l'utilisateur sur la page $url
     */
    public function redirect($url){
        header("Location: $url");
    }

    /**
     * affiche la liste des evenements auquels l'utilisateur est inscrit
     * @throws PDOException
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
                    echo "<li><a href='./contenu.php?lastevent=" . $tab['ID_Event'] . "'>" . $tab['Titre'] . "</a> le " . $tab['Date'] . " à " . $tab['Adresse'] . "</li>";
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
     * Détruit la session de l'utilisateur
     * @return true si tout se passe bien
     */
    public function logout(){
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }
}
?>
