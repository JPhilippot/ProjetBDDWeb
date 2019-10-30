/*
Fichier : Creation_GroupeS.sql
Auteurs:
Aurélien Besnier 21709012
Josua Philippot 21703792
Nom du groupe : S
*/
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
        login_Administrateur Varchar (50)
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
        Nom            Varchar (20) NOT NULL
	,CONSTRAINT Evenement_PK PRIMARY KEY (ID_Event)

	,CONSTRAINT Evenement_Contributeur_FK FOREIGN KEY (login) REFERENCES Contributeur(login)
	,CONSTRAINT Evenement_Localisation0_FK FOREIGN KEY (ID_Loc) REFERENCES Localisation(ID_Loc)
	,CONSTRAINT Evenement_Theme1_FK FOREIGN KEY (Nom) REFERENCES Theme(Nom)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Supprime_Event
#------------------------------------------------------------

CREATE TABLE Supprime_Event(
        login              Varchar (50) NOT NULL ,
        ID_Event           Int NOT NULL ,
        login_Contributeur Varchar (20) NOT NULL
	,CONSTRAINT Supprime_Event_PK PRIMARY KEY (login,ID_Event,login_Contributeur)

	,CONSTRAINT Supprime_Event_Administrateur_FK FOREIGN KEY (login) REFERENCES Administrateur(login)
	,CONSTRAINT Supprime_Event_Evenement0_FK FOREIGN KEY (ID_Event) REFERENCES Evenement(ID_Event)
	,CONSTRAINT Supprime_Event_Contributeur1_FK FOREIGN KEY (login_Contributeur) REFERENCES Contributeur(login)
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


ALTER TABLE Evenement AUTO_INCREMENT=1;
ALTER TABLE Localisation AUTO_INCREMENT=1;


#------------------------------------------------------------
# Insertions
#------------------------------------------------------------



