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

#DROP TABLE IF EXISTS Administrateur CASCADE; 
CREATE TABLE Administrateur(
        login    Varchar (50) NOT NULL ,
        email    Text NOT NULL ,
        password Varchar (80) NOT NULL
	,CONSTRAINT Administrateur_PK PRIMARY KEY (login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Visiteur
#------------------------------------------------------------

#DROP TABLE IF EXISTS Visiteur CASCADE;
CREATE TABLE Visiteur(
        login    Varchar (20) NOT NULL ,
        email    Text NOT NULL ,
        password Varchar (80) NOT NULL
	,CONSTRAINT Visiteur_PK PRIMARY KEY (login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Contributeur
#------------------------------------------------------------

#DROP TABLE IF EXISTS Contributeur CASCADE;
CREATE TABLE Contributeur(
        login                Varchar (20) NOT NULL ,
        email                Text NOT NULL ,
        password             Varchar (80) NOT NULL ,
        login_Administrateur Varchar (50)
	,CONSTRAINT Contributeur_PK PRIMARY KEY (login)

	,CONSTRAINT Contributeur_Administrateur_FK FOREIGN KEY (login_Administrateur) REFERENCES Administrateur(login)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Localisation
#------------------------------------------------------------

#DROP TABLE IF EXISTS Localisation CASCADE;
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

#DROP TABLE IF EXISTS Theme CASCADE;
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

#DROP TABLE IF EXISTS Evenement CASCADE;
CREATE TABLE Evenement(
        ID_Event       Int  Auto_increment  NOT NULL ,
        Titre          Varchar (50) NOT NULL ,
        Date           Date NOT NULL ,
        EffectifMax    Int NOT NULL ,
        Descriptif     Text NOT NULL ,
        EffectifActuel Int NOT NULL ,
        login          Varchar (20) NOT NULL ,
        ID_Loc         Int NOT NULL ,
        Nom            Varchar (20) NOT NULL
	,CONSTRAINT Evenement_PK PRIMARY KEY (ID_Event)

	,CONSTRAINT Evenement_Contributeur_FK FOREIGN KEY (login) REFERENCES Contributeur(login)
	,CONSTRAINT Evenement_Localisation0_FK FOREIGN KEY (ID_Loc) REFERENCES Localisation(ID_Loc)
	,CONSTRAINT Evenement_Theme1_FK FOREIGN KEY (Nom) REFERENCES Theme(Nom)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: S_inscrit
#------------------------------------------------------------

#DROP TABLE IF EXISTS S_inscrit CASCADE;
CREATE TABLE S_inscrit(
        ID_Event Int NOT NULL ,
        login    Varchar (20) NOT NULL
	,CONSTRAINT S_inscrit_PK PRIMARY KEY (ID_Event,login)

	,CONSTRAINT S_inscrit_Evenement_FK FOREIGN KEY (ID_Event) REFERENCES Evenement(ID_Event)
	,CONSTRAINT S_inscrit_Visiteur0_FK FOREIGN

#------------------------------------------------------------
# Table: Commentaire
#------------------------------------------------------------

#DROP TABLE IF EXISTS Commentaire CASCADE;
CREATE TABLE Commentaire(
ID INT(11) NOT NULL AUTO_INCREMENT ,
ID_Event INT(11) NOT NULL ,
login VARCHAR(20) NOT NULL ,
commentaire TEXT NOT NULL
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
DROP TRIGGER IF EXISTS suppr_event;

DELIMITER $$
CREATE TRIGGER suppr_event BEFORE DELETE ON Evenement
FOR EACH ROW
    BEGIN
        DELETE FROM S_inscrit WHERE OLD.ID_Event=S_inscrit.ID_Event;
        DELETE FROM Commentaire WHERE OLD.ID_Event=Commentaire.ID_Event;
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
        DECLARE newnote integer;
        DECLARE nbnotes integer;

        SET @nbnotes :=(SELECT COUNT(*) FROM Commentaire WHERE ID_Event=NEW.ID_Event);
        SET @newnote :=(SELECT CAST(SUM(Note)/nbnotes AS INTEGER) FROM Commentaire WHERE ID_Event=NEW.ID_Event);

        UPDATE Evenement SET Note=newnote WHERE ID_Event=NEW.ID_Event;
    END;
    $$
DELIMITER ;