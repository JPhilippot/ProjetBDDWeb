/*
Fichier : Creation_GroupeS.sql
Auteurs:
Aurélien Besnier 21709012
Josua Philippot 21703792
Nom du groupe : S
*/

#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: Administrateur
#------------------------------------------------------------

CREATE TABLE Administrateur(
        login    Varchar (50) NOT NULL ,
        email    Text NOT NULL ,
        password Varchar (80) NOT NULL
	,CONSTRAINT Administrateur_PK PRIMARY KEY (login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Visiteur
#------------------------------------------------------------

CREATE TABLE Visiteur(
        login    Varchar (20) NOT NULL ,
        email    Text NOT NULL ,
        password Varchar (80) NOT NULL
	,CONSTRAINT Visiteur_PK PRIMARY KEY (login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Contributeur
#------------------------------------------------------------

CREATE TABLE Contributeur(
        login                Varchar (20) NOT NULL ,
        email                Text NOT NULL ,
        password             Varchar (80) NOT NULL ,
        login_Administrateur Varchar (50),
        Attente 			 Boolean NOT NULL
	,CONSTRAINT Contributeur_PK PRIMARY KEY (login)

	,CONSTRAINT Contributeur_Administrateur_FK FOREIGN KEY (login_Administrateur) REFERENCES Administrateur(login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Localisation
#------------------------------------------------------------

CREATE TABLE Localisation(
        ID_Loc    Int  Auto_increment  NOT NULL ,
        Adresse   Text NOT NULL ,
        Latitude  Double NOT NULL ,
        Longitude Double NOT NULL
	,CONSTRAINT Localisation_PK PRIMARY KEY (ID_Loc)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Thème
#------------------------------------------------------------

CREATE TABLE Theme(
        Nom                  Varchar (20) NOT NULL ,
        login                Varchar (50) ,
        login_Administrateur Varchar (50) NOT NULL
	,CONSTRAINT Theme_PK PRIMARY KEY (Nom)

	,CONSTRAINT Theme_Administrateur_FK FOREIGN KEY (login) REFERENCES Administrateur(login)
	,CONSTRAINT Theme_Administrateur0_FK FOREIGN KEY (login_Administrateur) REFERENCES Administrateur(login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Evenement
#------------------------------------------------------------

CREATE TABLE Evenement(
        ID_Event       Int  Auto_increment  NOT NULL ,
        Titre          Varchar (50) NOT NULL ,
        Date           Date NOT NULL ,
        EffectifMax    Int NOT NULL ,
        Descriptif     Text NOT NULL ,
        EffectifActuel Int NOT NULL ,
        login          Varchar (20) NOT NULL ,
        ID_Loc         Int NOT NULL ,
        Nom            Varchar (20) NOT NULL,
        Note           Float NOT NULL
	,CONSTRAINT Evenement_PK PRIMARY KEY (ID_Event)

	,CONSTRAINT Evenement_Contributeur_FK FOREIGN KEY (login) REFERENCES Contributeur(login)
	,CONSTRAINT Evenement_Localisation0_FK FOREIGN KEY (ID_Loc) REFERENCES Localisation(ID_Loc)
	,CONSTRAINT Evenement_Theme1_FK FOREIGN KEY (Nom) REFERENCES Theme(Nom)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: S_inscrit
#------------------------------------------------------------

CREATE TABLE S_inscrit(
        ID_Event Int NOT NULL ,
        login    Varchar (20) NOT NULL
	,CONSTRAINT S_inscrit_PK PRIMARY KEY (ID_Event,login)

	,CONSTRAINT S_inscrit_Evenement_FK FOREIGN KEY (ID_Event) REFERENCES Evenement(ID_Event)
	,CONSTRAINT S_inscrit_Visiteur0_FK FOREIGN KEY (login) REFERENCES Visiteur(login)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Commentaire
#------------------------------------------------------------

CREATE TABLE Commentaire(
ID INT(11) NOT NULL AUTO_INCREMENT ,
ID_Event INT(11) NOT NULL ,
login VARCHAR(20) NOT NULL ,
commentaire TEXT NOT NULL ,
Note Int NOT NULL
,CONSTRAINT Commentaire_PK PRIMARY KEY (ID, ID_Event)

,CONSTRAINT Commentaire_Evenement_FK FOREIGN KEY (ID_Event) REFERENCES Evenement(ID_Event)
,CONSTRAINT Commentaire_Visiteur_FK FOREIGN KEY (login) REFERENCES Visiteur(login)
) ENGINE = InnoDB;


ALTER TABLE Visiteur AUTO_INCREMENT=1;
ALTER TABLE Evenement AUTO_INCREMENT=1;
ALTER TABLE Contributeur AUTO_INCREMENT=1;
ALTER TABLE Localisation AUTO_INCREMENT=1;

#------------------------------------------------------------
# Trigger: SupprEvent
#------------------------------------------------------------
DROP PROCEDURE IF EXISTS proc_suppr_event;

DELIMITER $$
CREATE PROCEDURE proc_suppr_event(IN id integer)
    BEGIN
        DELETE FROM S_inscrit WHERE id=S_inscrit.ID_Event;
        DELETE FROM Commentaire WHERE id=Commentaire.ID_Event;
    END
    $$
DELIMITER ;


#------------------------------------------------------------
# Trigger: SupprEvent
#------------------------------------------------------------
DROP TRIGGER IF EXISTS suppr_event;

DELIMITER $$
CREATE TRIGGER suppr_event BEFORE DELETE ON Evenement
FOR EACH ROW
    BEGIN
        CALL proc_suppr_event(OLD.ID_Event);
    END;
    $$
DELIMITER ;

#------------------------------------------------------------
# Trigger: SupprTheme
#------------------------------------------------------------
DROP TRIGGER IF EXISTS suppr_theme;

DELIMITER $$
CREATE TRIGGER suppr_theme BEFORE DELETE ON Theme
FOR EACH ROW
    BEGIN
        DELETE FROM Evenement WHERE Evenement.Nom=OLD.Nom;
    END;
    $$
DELIMITER ;

#------------------------------------------------------------
# Trigger: SupprContributeur
#------------------------------------------------------------
DROP TRIGGER IF EXISTS suppr_contributeur;

DELIMITER $$
CREATE TRIGGER suppr_contributeur BEFORE DELETE ON Contributeur
FOR EACH ROW
    BEGIN
        DELETE FROM Evenement WHERE Evenement.login=OLD.login;
    END;
    $$
DELIMITER ;

#------------------------------------------------------------
# Trigger: note_event
#------------------------------------------------------------
DROP TRIGGER IF EXISTS note_event;

DELIMITER $$
CREATE TRIGGER  note_event AFTER INSERT ON Commentaire
FOR EACH ROW
    BEGIN
        DECLARE newnote float;

        SET @newnote :=(SELECT AVG(Note) FROM Commentaire WHERE ID_Event=NEW.ID_Event);

        UPDATE Evenement SET Note=@newnote WHERE ID_Event=NEW.ID_Event;
    END;
    $$
DELIMITER ;
