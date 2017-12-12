#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP DATABASE IF EXISTS projettut ;
CREATE DATABASE projettut;
USE projettut;



DROP TABLE IF EXISTS autorisemodif ;
DROP TABLE IF EXISTS tarif ;
DROP TABLE IF EXISTS enfant ;
DROP TABLE IF EXISTS agenda ;
DROP TABLE IF EXISTS activite ;
DROP TABLE IF EXISTS tarif ;
DROP TABLE IF EXISTS adulte ;
DROP TABLE IF EXISTS ville;
DROP TABLE IF EXISTS niveau ;
DROP TABLE IF EXISTS vacance;
DROP TABLE IF EXISTS horaire;

#------------------------------------------------------------
# Table: tarif
#------------------------------------------------------------
CREATE TABLE tarif(
        idTarif    int (11) Auto_increment  NOT NULL ,
        prix       Int NOT NULL ,
        PRIMARY KEY (idTarif )
)ENGINE=InnoDB;
INSERT INTO tarif VALUES (1,4);
insert into tarif (idTarif, prix) values (2, 3);
insert into tarif (idTarif, prix) values (3, 4);


#------------------------------------------------------------
# Table: ville
#------------------------------------------------------------

CREATE TABLE ville(
        idVille    int (11) Auto_increment  NOT NULL ,
        adresse    Varchar (45) ,
        nomVille   Varchar (45) ,
        codePostal Int ,
        PRIMARY KEY (idVille )
)ENGINE=InnoDB;
INSERT INTO ville VALUES (NULL, 'la bas', 'loin', '66666');
insert into ville (idVille, adresse, nomVille, codePostal) values (NULL, '9 Ridge Oak Court', 'Jessore', '94130');
insert into ville (idVille, adresse, nomVille, codePostal) values (2, '676 Aberg Road', 'Faraulep', '75000');



#------------------------------------------------------------
# Table: niveau
#------------------------------------------------------------

CREATE TABLE niveau(
        idNiveau  int (11) Auto_increment  NOT NULL ,
        nomNiveau Varchar (45) ,
        PRIMARY KEY (idNiveau )
)ENGINE=InnoDB;
INSERT INTO niveau VALUES (NULL, "ce1");
INSERT INTO niveau VALUES (NULL, "ce2");

#------------------------------------------------------------
# Table: vacance
#------------------------------------------------------------

CREATE TABLE vacance(
        idVacance    int (11) Auto_increment  NOT NULL ,
        dateDebutVac Datetime ,
        dateFinVac   Datetime ,
        PRIMARY KEY (idVacance )
)ENGINE=InnoDB;
insert into vacance (idVacance, dateDebutVac, dateFinVac) values (NULL, '2017-12-22 00:00:00', '2018-01-08 00:00:00');
insert into vacance (idVacance, dateDebutVac, dateFinVac) values (NULL, '2018-02-18 00:00:00', '2018-03-05 00:00:00');


#------------------------------------------------------------
# Table: Horaire
#------------------------------------------------------------

CREATE TABLE horaire(
        idHoraire  int (11) NOT NULL ,
        heureDebut int ,
        heureFin   int ,
        PRIMARY KEY (idHoraire )
)ENGINE=InnoDB;
INSERT INTO horaire VALUES(1,8,10);
INSERT INTO horaire VALUES(2,10,12);
INSERT INTO horaire VALUES(3,14,16);
INSERT INTO horaire VALUES(4,16,18);
INSERT INTO horaire VALUES(5,18,20);


#------------------------------------------------------------
# Table: classe
#------------------------------------------------------------
CREATE TABLE classe(
        idClasse       int (11) Auto_increment  NOT NULL ,
        nomClasse      Varchar (45) ,
        professeur     Varchar (45) ,
        profRespClasse TinyINT ,
        PRIMARY KEY (idClasse )
)ENGINE=InnoDB;
insert into classe (idClasse, nomClasse, professeur, profRespClasse) values (NULL, 'abrack0', 'Alyse Brack', 1);
insert into classe (idClasse, nomClasse, professeur, profRespClasse) values (NULL, 'skilleen1', 'Stephannie Killeen', 2);


#------------------------------------------------------------
# Table: adulte
#------------------------------------------------------------
CREATE TABLE adulte(
        idAdulte    int (11) Auto_increment  NOT NULL ,
        nom         Varchar (50) ,
        prenom      Varchar (50) ,
        idVille     int(11) NOT NULL ,
        username    Varchar (45) ,
        password    Varchar (45) ,
        adresseMail Varchar (50) ,
        rang        Int ,
        telephone   Int ,
        PRIMARY KEY (idAdulte ),
		FOREIGN KEY (idVille) REFERENCES ville(idVille) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;
SET FOREIGN_KEY_CHECKS = 0;
INSERT INTO adulte VALUES('','0','0',1,'0','0','0',0,0);
SET FOREIGN_KEY_CHECKS=1;



#------------------------------------------------------------
# Table: activite
#------------------------------------------------------------
CREATE TABLE activite(
        idActivite          int (11) Auto_increment  NOT NULL ,
		idTarif				int(11) NOT NULL,
        nomActivite         Varchar (200) ,
        descriptionActivite Text ,
        PRIMARY KEY (idActivite ),
		FOREIGN KEY (idTarif) REFERENCES tarif(idTarif) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE=InnoDB;
INSERT INTO activite VALUES (1,1,'Cantine','Venez manger !');

#------------------------------------------------------------
# Table: agenda
#------------------------------------------------------------
CREATE TABLE agenda(
        idAgenda     int (11) Auto_increment  NOT NULL ,
        idActivite   int(11) NOT NULL ,
		    idHoraire	 int(11) NOT NULL,
        dateActivite Datetime ,
        jour         Int ,
        PRIMARY KEY (idAgenda, idActivite, idHoraire ),
		FOREIGN KEY (idHoraire) REFERENCES horaire(idHoraire) ON DELETE CASCADE ON UPDATE CASCADE,
		FOREIGN KEY (idActivite) REFERENCES activite(idActivite) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: enfant
#------------------------------------------------------------
CREATE TABLE enfant(
        idEnfant        int (11) Auto_increment  NOT NULL ,
        nomEnfant       Varchar (50) ,
        prenomEnfant    Varchar (50) ,
        dateDeNaissance Date ,
        idClasse        int(11) NOT NULL ,
		idNiveau		int(11) NOT NULL,
        PRIMARY KEY (idEnfant ),
		FOREIGN KEY (idClasse) REFERENCES classe(idClasse) ON DELETE CASCADE ON UPDATE CASCADE,
		FOREIGN KEY (idNiveau) REFERENCES niveau(idNiveau) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;



#------------------------------------------------------------
# Table: inscription
#------------------------------------------------------------
CREATE TABLE inscription (
        idEnfant int(11) NOT NULL,
        idAgenda int(11) NOT NULL, 
        dateInscription date,
		FOREIGN KEY (idEnfant) REFERENCES enfant(idEnfant) ON DELETE CASCADE ON UPDATE CASCADE,
		FOREIGN KEY (idAgenda) REFERENCES agenda(idAgenda) ON DELETE CASCADE ON UPDATE CASCADE 
)ENGINE=InnoDB; 

#------------------------------------------------------------
# Table: autorisemodif
#------------------------------------------------------------
CREATE TABLE autorisemodif(
        idAdulte int(11) NOT NULL ,
        idEnfant int(11) NOT NULL ,
		PRIMARY KEY(idAdulte,idEnfant),
		FOREIGN KEY (idAdulte) REFERENCES adulte(idAdulte) ON DELETE CASCADE ON UPDATE CASCADE,
		FOREIGN KEY (idEnfant) REFERENCES enfant(idEnfant) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


/*
MEILLEURE FACON DE FAIRE... NE MARCHE PAS

--
-- Contraintes pour la table autorisemodif
--
ALTER TABLE autorisemodif
  ADD CONSTRAINT fk_Enfant_has_AdulteResponsable_Adulte FOREIGN KEY (idAdulte) REFERENCES adulte (idAdulte) ON DELETE CASCADE ON UPDATE CASCADE;
  ADD CONSTRAINT fk_Enfant_has_AdulteResponsable_Enfant FOREIGN KEY (idEnfant) REFERENCES enfant (idEnfant) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table activite
--
ALTER TABLE activite
  ADD CONSTRAINT fk_Activite_tarif FOREIGN KEY (idTarif) REFERENCES tarif (idTarif) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Contraintes pour la table inscription
--
ALTER TABLE inscription
  ADD CONSTRAINT fk_Enfant_has_Agenda_Enfant FOREIGN KEY (idEnfant) REFERENCES enfant (idEnfant) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_Inscription_Agenda FOREIGN KEY (idAgenda) REFERENCES agenda (idAgenda) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraites pour la table classe 
--

ALTER TABLE classe 
  ADD CONSTRAINT fk_classe_niveau FOREIGN KEY (idNiveau) REFERENCES niveau (idNiveau) ON DELETE CASCADE ON UPDATE CASCADE ; 

--
-- Contraintes pour la table enfant
--
ALTER TABLE enfant
  ADD CONSTRAINT fk_enfant_classe FOREIGN KEY (idClasse) REFERENCES classe (idClasse) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table agenda
--
ALTER TABLE agenda
  ADD CONSTRAINT fk_Agenda_Activite FOREIGN KEY (idActivite) REFERENCES activite (idActivite) ON DELETE CASCADE ON UPDATE CASCADE;
  ADD CONSTRAINT fk_Agenda_Horaire FOREIGN KEY (idHoraire) REFERENCES horaire (idHoraire) ON DELETE CASCADE ON UPDATE CASCADE;

  --
-- Contraintes pour la table enfant
--
ALTER TABLE enfant
  ADD CONSTRAINT fk_enfant_niveau FOREIGN KEY (idNiveau) REFERENCES niveau (idNiveau) ON DELETE CASCADE ON UPDATE CASCADE;
  ADD CONSTRAINT fk_enfant_classe FOREIGN KEY (idClasse) REFERENCES classe (idClasse) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Contraintes pour la table adulte
--
ALTER TABLE adulte
  ADD CONSTRAINT fk_AdulteResponsable_Ville FOREIGN KEY (idVille) REFERENCES ville (idVille) ON DELETE CASCADE ON UPDATE CASCADE;


*/

