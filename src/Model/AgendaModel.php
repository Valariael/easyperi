<?php
/**
 * Created by PhpStorm.
 * User: Meedah
 * Date: 05/12/2017
 * Time: 15:15
 */

namespace App\Model;
use MongoDB\Driver\Query;
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use \Datetime ;


class AgendaModel
{
    private $db ;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }


    public function getMaxId(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('count(agenda.idAgenda)')
            ->from('agenda');
        $res = $queryBuilder->execute()->fetch(0);
        return $res ;
    }

    public function getAgenda($id){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->select('ag.idAgenda', 'act.nomActivite', 'ag.dateActivite', 'ag.jour', 'h.heureDebut', 'h.heureFin' )
            ->from('agenda', 'ag')
            ->innerJoin('ag', 'activite', 'act', 'ag.idActivite = act.idActivite')
            ->innerJoin('ag', 'horaire', 'h', 'ag.idHoraire = h.idHoraire')
            ->where('ag.idAgenda = :id')
            ->setParameter(':id', $id) ;
        return $queryBuilder->execute()->fetchAll() ;
    }

//récuperer juste le jour, là on return jour:heure
    public function getdateActivite($id){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->select('dateActivite')
            ->from('agenda')
            ->where('agenda.idAgenda = :id')
            ->setParameter(':id', intval($id));
         $res = $queryBuilder->execute()->fetch(0) ;
         return $res ;
    }





    public function getHeureFin($id){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->select( 'h.heureFin')
            ->from('horaire', 'h')
            ->innerJoin('h', 'agenda', 'ag', 'ag.idHoraire = h.idHoraire')
            ->where('ag.idHoraire = :id')
            ->setParameter(':id', intval($id))
        ;

        return  intval($queryBuilder->execute()->fetchColumn(0));
    }

    public function getHeureDebut($id){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->select('h.heureDebut')
            ->from('horaire', 'h')
            ->innerJoin('h', 'agenda', 'ag', 'ag.idHoraire = h.idHoraire')
            ->where('ag.idHoraire = :id')
            ->setParameter(':id', intval($id))
        ;
        return intval($queryBuilder->execute()->fetchColumn(0));
    }

    public function getAgendaClean(){
        $heureDebut=$this->getHeureDebut(1);
        $heureFin=$this->getHeureFin(1);

        $dateActivite= $this->getdateActivite(1);
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder -> select('ag.idAgenda','ac.nomActivite','h.heureDebut','h.heureFin','ag.dateActivite')
            ->from('agenda', 'ag')
            ->innerJoin('ag' ,'activite','ac', 'ag.idActivite = ac.idActivite')
            ->innerJoin('ag','horaire','h', 'ag.idHoraire = h.idHoraire')
            ->where('ag.dateActivite = :dateActivite')
            ->andWhere('h.heureDebut =:heureDebut')
            ->andWhere('h.heureFin= :heureFin')
            ->setParameter(':dateActivite',$dateActivite)
            ->setParameter(':heureDebut',$heureDebut)
            ->setParameter(':heureFin',$heureFin)
            ->addOrderBy('ag.dateActivite')
        ;


        return $queryBuilder->execute()->fetchAll(); }

 /*   public function getUnMois(){
        $heureDebut=$this->getHeureDebut();
        $heureFin=$this->getHeureFin();

        $dateActivite= $this->getdateActivite();
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder -> select('agenda.idAgenda','ac.nomActivite','h.heureDebut','h.heureFin','agenda.dateActivite')
            ->from('agenda')
            ->from('activite','ac')
            ->from('horaire','h')
            ->where('agenda.idActivite = ac.idActivite')
            ->andWhere('agenda.dateActivite = :dateActivite')
            ->andWhere('h.heureDebut =:heureDebut')
            ->andWhere('h.heureFin= :heureFin')
            ->setParameter(':dateActivite',$dateActivite)
            ->setParameter(':heureDebut',$heureDebut)
            ->setParameter(':heureFin',$heureFin)
            ->addOrderBy('agenda.dateActivite')
        ;


        return $queryBuilder->execute()->fetchAll();
    } */
    public function addUnMois()
    {
        $queryBuilder = new QueryBuilder($this->db);
        for ($i = 0; $i < 21; $i++) {
            $dateActivite = $this->getdateActivite();
            $queryBuilder->insert('agenda')
                ->values([
                    'idAgenda' => 'NULL',
                    'idActivite' => '1',
                    'idHoraire' => '1',
                    'dateActivite' => '?',
                    'jour'=> 1
                ])
                ->setParameter(0, $dateActivite);

        }
        return $queryBuilder->execute();
    }

     public function addInscription($idEnfant, $idAgenda){
        $dateTime = new DateTime('now') ;
        $dateTime = $dateTime->format("Y-m-d") ;
        $queryBuilder = new QueryBuilder($this->db) ;
            $queryBuilder->insert('inscription')
                ->values([
                    'idEnfant' => '?',
                    'idAgenda' => '?',
                    'dateInscription' => '?' ,
                ])
                ->setParameter(0, intval($idEnfant))
                ->setParameter(1, intval($idAgenda))
                ->setParameter(2, $dateTime)
            ;
        return $queryBuilder->execute() ;
    }

    /*public function getAllAgendas(){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->select('a.idAgenda', 'act.idActivite', 'a.dateActivite', 'a.dateActivite', 'v.idVacance')
            ->from('agenda', 'a')
            ->innerJoin('a', 'activite', 'act', 'a.idActivite = act.idActivite')
            ->innerJoin('a', 'horaire', 'v', 'a.idHoraire = v.idVacance');

        //->innerJoin('a', 'vacance', 'v', 'a.idVacance = v.idVacance');
        return $queryBuilder->execute()->fetchAll() ;
    }
    public function createAgenda($donnees){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder->insert('agenda')
            ->values([
                'idActivite' => '?',
                'dateActivite' => '?',
                'dateActivite' => '?'
         "   ])
            ->setParameter(0, $donnees['idActivite'])
            ->setParameter(1, $donnees['dateActtivite'])
            ->setParameter(2, $donnees['dateActivite']) ;
        return $queryBuilder->execute() ;
    }
    public function updateAgenda($donnees){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->update('agenda')
            ->set('dateActivite', '?')
            ->set('dateActivite', '?')
            ->where('idAgenda = ?')
            ->setParameter(0, $donnees['dateActivite'])
            ->setParameter(1, $donnees['dateActivite'])
            ->setParameter(2, $donnees['idAgenda']) ;
        return $queryBuilder->execute() ;
    }
    public function deleteAgenda($id){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder
            ->delete('agenda')
            ->where('idAgenda = :id')
            ->setParameter(':id', $id) ;
        return $queryBuilder->execute();
    }
    public function updateDateActivite($id, $donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('agenda')
            ->set('date', ':date')
            ->where('idAgenda = :id')
            ->setParameter(':date', $donnees['date'])
            ->setParameter(':id', $id);
        return $queryBuilder->execute() ;
    }

   /* public function updateHorairesActivite($donnees, $idActivite){
        $queryBuilder = new QueryBuilder($this->db) ;
        $queryBuilder ->update('')
            ->set('h.heureDebut', ':heureDebut')
            ->set('h.heureFin', ':heureFin')
            ->where('')
    } */







}