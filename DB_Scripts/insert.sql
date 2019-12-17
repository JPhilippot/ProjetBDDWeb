Insert into Administrateur VALUES(
    "BéberBG",
    "bbbg@caramail.fr",
    "$2y$10$.3VlcocV0/X5HULKtLu6xeXo0kZvPE.LWrCKlsivK5yLx333z2noW"
);
Insert into Administrateur VALUES(
    "MatthieuBG",
    "mttbg@caramail.fr",
    "jemaimebcp2"
);

Insert into Contributeur VALUES(
   "JM-Castor",
   "kiliandu07@hotmail.com",
   "ouisititi34",
   "BéberBG" 
);

INSERT into localisation(Adresse,Latitude, Longitude) VALUES(
    "Espace St charles, 300 Rue Auguste Broussonnet, 34090 Montpellier",
    "43.61",
    "3.8706"
);
Insert into theme Values(
    "Célébration",
    "BéberBG",
    "MatthieuBG"
);

insert into evenement(Titre,Date,EffectifMax,Descriptif,EffectifActuel,login,ID_Loc,Nom) values(
    "Remise des dîplomes",
    "2019-12-14",
    50,
    "Viendez rescuperer vostre displome durement obtenu apres cestte annee de labeur. Petit four et soda au rendez-vous ! Venez nombreux ! (En cas d'absence vôtre année sera annulée).",
    5,
    "JM-Castor",
    "1",
    "Célébration"
);

Insert into Contributeur VALUES(
   "Mahmoud",
   "darkiller2@hotmail.com",
   "test",
   "BéberBG" 
);

INSERT into localisation(Adresse,Latitude, Longitude) VALUES(
    "3 rue de la boustifaille",
    "45",
    "4"
);
Insert into theme Values(
    "Repas",
    "BéberBG",
    "MatthieuBG"
);

insert into evenement(Titre,Date,EffectifMax,Descriptif,EffectifActuel,login,ID_Loc,Nom) values(
    "Tartiflette géante",
    "2019-09-11",
    50,
    "Maintenant que j'ai créée cet event, j'ai envie de tartiflette.",
    5,
    "Mahmoud",
    "2",
    "Repas"
);