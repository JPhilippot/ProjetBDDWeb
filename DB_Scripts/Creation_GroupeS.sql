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
ID_Admin Int  Auto_increment  NOT NULL ,
email    Text NOT NULL ,
login    Varchar (50) NOT NULL ,
password Varchar (80) NOT NULL
,CONSTRAINT Administrateur_PK PRIMARY KEY (ID_Admin)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Visiteur
#------------------------------------------------------------

CREATE TABLE Visiteur(
ID_Vis   Int  Auto_increment  NOT NULL ,
email    Text NOT NULL ,
login    Varchar (20) NOT NULL ,
password Varchar (80) NOT NULL
,CONSTRAINT Visiteur_PK PRIMARY KEY (ID_Vis)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Contributeur
#------------------------------------------------------------

CREATE TABLE Contributeur(
ID_Contrib Int  Auto_increment  NOT NULL ,
email      Text NOT NULL ,
login      Varchar (20) NOT NULL ,
password   Varchar (80) NOT NULL ,
ID_Admin   Int
,CONSTRAINT Contributeur_PK PRIMARY KEY (ID_Contrib)

,CONSTRAINT Contributeur_Administrateur_FK FOREIGN KEY (ID_Admin) REFERENCES Administrateur(ID_Admin)
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
Nom                     Varchar (20) NOT NULL ,
ID_Admin                Int ,
ID_Admin_Administrateur Int NOT NULL
,CONSTRAINT Theme_PK PRIMARY KEY (Nom)

,CONSTRAINT Theme_Administrateur_FK FOREIGN KEY (ID_Admin) REFERENCES Administrateur(ID_Admin)
,CONSTRAINT Theme_Administrateur0_FK FOREIGN KEY (ID_Admin_Administrateur) REFERENCES Administrateur(ID_Admin)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Evenement
#------------------------------------------------------------

CREATE TABLE Evenement(
ID_Event       Int  Auto_increment  NOT NULL ,
Date           Date NOT NULL ,
EffectifMax    Int NOT NULL ,
Descriptif     Text NOT NULL ,
EffectifActuel Int NOT NULL ,
ID_Contrib     Int NOT NULL ,
ID_Loc         Int NOT NULL ,
Nom            Varchar (20) NOT NULL
,CONSTRAINT Evenement_PK PRIMARY KEY (ID_Event)

,CONSTRAINT Evenement_Contributeur_FK FOREIGN KEY (ID_Contrib) REFERENCES Contributeur(ID_Contrib)
,CONSTRAINT Evenement_Localisation0_FK FOREIGN KEY (ID_Loc) REFERENCES Localisation(ID_Loc)
,CONSTRAINT Evenement_Theme1_FK FOREIGN KEY (Nom) REFERENCES Theme(Nom)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Supprime_Event
#------------------------------------------------------------

CREATE TABLE Supprime_Event(
ID_Admin   Int NOT NULL ,
ID_Event   Int NOT NULL ,
ID_Contrib Int NOT NULL
,CONSTRAINT Supprime_Event_PK PRIMARY KEY (ID_Admin,ID_Event,ID_Contrib)

,CONSTRAINT Supprime_Event_Administrateur_FK FOREIGN KEY (ID_Admin) REFERENCES Administrateur(ID_Admin)
,CONSTRAINT Supprime_Event_Evenement0_FK FOREIGN KEY (ID_Event) REFERENCES Evenement(ID_Event)
,CONSTRAINT Supprime_Event_Contributeur1_FK FOREIGN KEY (ID_Contrib) REFERENCES Contributeur(ID_Contrib)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: S_inscrit
#------------------------------------------------------------

CREATE TABLE S_inscrit(
ID_Event Int NOT NULL ,
ID_Vis   Int NOT NULL
,CONSTRAINT S_inscrit_PK PRIMARY KEY (ID_Event,ID_Vis)

,CONSTRAINT S_inscrit_Evenement_FK FOREIGN KEY (ID_Event) REFERENCES Evenement(ID_Event)
,CONSTRAINT S_inscrit_Visiteur0_FK FOREIGN KEY (ID_Vis) REFERENCES Visiteur(ID_Vis)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Commentaire
#------------------------------------------------------------

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
# Insertions
#------------------------------------------------------------


INSERT INTO Administrateur SELECT 1,'admin@email.com','root',SHA2('root',256);

INSERT INTO Localisation VALUES(1,'MONTPELLIER',43.6119, 3.8772);

INSERT INTO Theme VALUES('Concert',1,1);
