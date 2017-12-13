<?php
define("hostname","localhost"); // ou serveurmysql
define("database","projettut2");
define("username","root");
define("password","");



/*
Tout se fera dans AgendaController, AgendaModel ou agenda/showAgenda.html.twig le but sera de  :
	1) appeler AgendaController.showAgenda depuis showEnfants, en passant $idEnfant(avec getEnfantBySession sinon)
	2)	créer un array['idAgenda'] en cliquant sur les checkbox de showAgenda
	3)	ajouter une inscription avec chaque 'idAgenda' dans l'array + idEnfant
	OPTIONNEL : 4) s'assurer que les checkbox sont bien cochées quand on revient sur showAgenda
	OPTIONNEL : 5) Utiliser agenda.dateActivite en affichant que la date sans l'heure
	 */
	
/* TODO, lier enfants.show a agenda.showAgenda
	
TODO, Récupérer $idAgenda dans les checkbox de showAgenda.html.twig 

TODO,Dans agendaModel créer addInscription($idEnfant,$idAgenda,dateActivite)
	Faut faire un for($i) pour récupérer les $idEnfant[$i] avec $i<$idEnfant.lenght

TODO,appeler (new $this->AddInscription())dans AgendaController à chaque clic ,

TODO, créer et appeler validAddInscription pour valider les inscriptions cochées

FONCTION D'ENREGISTREMENT ET D'AFFICHAGE DU MENU DE LA SEMAINE ? DU MOIS ?
*/



