#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP DATABASE IF EXISTS projettut2 ;
CREATE DATABASE projettut2;
USE projettut2;



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


#------------------------------------------------------------
# Table: niveau
#------------------------------------------------------------

CREATE TABLE niveau(
        idNiveau  int (11) Auto_increment  NOT NULL ,
        nomNiveau Varchar (45) ,
        PRIMARY KEY (idNiveau )
)ENGINE=InnoDB, DEFAULT CHARSET=utf8;
INSERT INTO niveau VALUES(1, NULL);

#------------------------------------------------------------
# Table: vacance
#------------------------------------------------------------

CREATE TABLE vacance(
        idVacance    int (11) Auto_increment  NOT NULL ,
        dateDebutVac Datetime ,
        dateFinVac   Datetime ,
        PRIMARY KEY (idVacance )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Horaire
#------------------------------------------------------------

CREATE TABLE horaire(
        idHoraire  int (11) NOT NULL ,
        heureDebut int ,
        heureFin   int ,
        PRIMARY KEY (idHoraire )
)ENGINE=InnoDB;
INSERT INTO horaire VALUES(1,10,12);
INSERT INTO horaire VALUES(2,8,10);
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
Insert into classe VALUES (1, 'ce1', 'prof', 1);


#------------------------------------------------------------
# Table: adulte
#------------------------------------------------------------
CREATE TABLE adulte(
        idAdulte    int (11) Auto_increment  NOT NULL ,
        nom         Varchar (50) ,
        prenom      Varchar (50) ,
        adresse     Varchar (50) ,
        ville       Varchar (50) ,
        code_postal int (5) ,
        username    Varchar (45) ,
        password    Varchar (45) ,
        adresseMail Varchar (50) ,
        role        Varchar (20) ,
        telephone   Varchar (20) ,
        PRIMARY KEY (idAdulte )
)ENGINE=InnoDB;
SET FOREIGN_KEY_CHECKS = 0;
INSERT INTO adulte VALUES(1,'admin','admin','admin_adress','admin_town',90000,'admin','5ed92051c648232133b6ddd54a9e8951', 'admin@admin.com', 'ROLE_ADMIN', '0600000000');
INSERT INTO adulte VALUES(2,'parent_nom','parent_prenom','parent_adress','parent_town',90000,'parent','b79ecf14eea786745e4293d0264de731', 'parent@parent.com', 'ROLE_PARENT', '0610000000');
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
    	CONSTRAINT fk_activiteTarif FOREIGN KEY(idTarif)
		REFERENCES tarif(idTarif) ON DELETE CASCADE ON UPDATE CASCADE

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
        jour         varchar(3) ,
        PRIMARY KEY (idAgenda),
		    CONSTRAINT fk_agendaHoraire FOREIGN KEY (idHoraire) REFERENCES horaire(idHoraire) ON DELETE CASCADE ON UPDATE CASCADE,
		    CONSTRAINT fk_agendaActivite FOREIGN KEY (idActivite) REFERENCES activite(idActivite) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE=InnoDB;
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (1, 1, 1, '2018-01-01 14:01:14', 'LUN');
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (2, 1, 2, '2018-01-02 18:10:27', 'MAR');
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (3, 1, 3, '2018-01-03 18:08:54', 'MER');
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (4, 1, 4, '2018-01-04 02:44:53', 'JEU');
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (5, 1, 5, '2018-01-05 01:38:02', 'VEN');
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (6, 1, 1, '2018-01-08 13:39:59', 'LUN');
insert into agenda (idAgenda, idActivite, idHoraire, dateActivite, jour) values (7, 1, 2, '2018-01-09 06:51:35', 'MAR');



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
		CONSTRAINT fk_enfantClasse FOREIGN KEY (idClasse) REFERENCES classe(idClasse) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT fk_enfantNiveau FOREIGN KEY (idNiveau) REFERENCES niveau(idNiveau) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: inscription
#------------------------------------------------------------
CREATE TABLE inscription (
        idEnfant int(11) NOT NULL,
        idAgenda int(11) NOT NULL, 
        dateInscription Datetime,
		CONSTRAINT fk_inscriptionEnfant FOREIGN KEY (idEnfant) REFERENCES enfant(idEnfant) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT fk_inscriptionAgenda FOREIGN KEY (idAgenda) REFERENCES agenda(idAgenda) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB; 

#------------------------------------------------------------
# Table: autorisemodif
#------------------------------------------------------------
CREATE TABLE autorisemodif(
        idAdulte int(11) NOT NULL ,
        idEnfant int(11) NOT NULL ,
		CONSTRAINT fk_autorisemodifAdulte FOREIGN KEY (idAdulte) REFERENCES adulte(idAdulte) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT fk_autorisemodifEnfant FOREIGN KEY (idEnfant) REFERENCES enfant(idEnfant) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


