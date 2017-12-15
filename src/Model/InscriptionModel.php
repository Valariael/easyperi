<?php
/**
 * Created by PhpStorm.
 * User: tlacaill
 * Date: 15/12/17
 * Time: 10:45
 */

namespace App\Model;


namespace App\Model;
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class InscriptionModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }


    public function getEnfantsByIdAgenda($idAgenda){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idEnfant','e.nomEnfant','e.prenomEnfant','e.dateDeNaissance','c.nomClasse','n.nomNiveau','i.idAgenda','a.dateActivite')
            ->from('inscription', 'i')
            ->from('enfant', 'e')
            ->from('classe', 'c')
            ->from('niveau', 'n')
            ->from('agenda', 'a')
            ->Where('c.idClasse = e.idClasse ')
            ->andWhere('n.idNiveau = e.idNiveau')
            ->andWhere('i.idEnfant = e.idEnfant')
            ->andWhere('i.idAgenda = a.idAgenda')
            ->andWhere('a.idAgenda = :idAgenda')
            ->setParameter(':idAgenda', $idAgenda);

        $res = $queryBuilder->execute()->fetchAll();

        return $res;
    }



}