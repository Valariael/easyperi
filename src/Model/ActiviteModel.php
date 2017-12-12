<?php

namespace App\Model;
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class ActiviteModel
{
    private $db;/*
`idActivite` int(11) NOT NULL AUTO_INCREMENT,
`nomActivite` varchar(200) DEFAULT NULL,
`descriptionActivite` text,
`idTheme` int(11) NOT NULL,
PRIMARY KEY (`idActivite`,`idTheme`),
KEY `fk_Activite_Theme1_idx` (`idTheme`)
*/
    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllActivites(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('a.idActivite', 'a.nomActivite', 'a.descriptionActivite','a.idTheme')
            ->from('activite', 'a')
            ->addOrderBy('a.idActivite', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }

    public function getActivite($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('a.idActivite', 'a.nomActivite',"a.descriptionActivite",'a.nomTheme')
            ->from('Activite', 'a')
            ->where('a.idActivite = :idActivite')
            ->setParameter(':idActivite', intval($id));

        $res = $queryBuilder->execute()->fetch();
        if (!$res){
            return $res;
        }
        return [
            'idActivite' => $res['idActivite'],
            'nomActivite' => $res['nomActivite'],
            'prenom' => $res['descriptionActivite'],
            'idTheme' => $res['idTheme'],
        ];
    }
    public function getActiviteIdByNomDesc($nomActivite, $descriptionActivite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('a.idActivite')
            ->from('Activite', 'a')
            ->where('a.nomActivite = :nomActivite')
            ->andWhere('a.descriptionActivite = :descriptionActivite')
            ->setParameter(':nomActivite', $nomActivite)
            ->setParameter(':descriptionActivite', $descriptionActivite);


        $res = intval($queryBuilder->execute()->fetchColumn(0));

        return $res;
    }

   /* public function getActiviteIdBySession($username){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('a.idActivite')
            ->from('Activite', 'a')
            ->where('a.idActivite = :username')
            ->setParameter(':username', $username);

        $res = intval($queryBuilder->execute()->fetchColumn(0));

        return $res;
    }
*/


    public function addActivite($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('Activite')
            ->values([
                'nomActivite' => '?',
                'descriptionActivite' => '?',
                'idTheme' => '?'
            ])
            ->setParameter(0, $donnees['nomActivite'])
            ->setParameter(1, $donnees['descriptionActivite'])
            ->setParameter(2,$donnees['idTheme'])
        ;
        $queryBuilder->execute();
        return $this->getActiviteIdByNomDesc($donnees['nomActivite'],$donnees['descriptionActivite']);

    }

/*
    public function updateActivite($id, $donnees){
        $queryBuilder = new QueryBuilder($this->db);

        $idVille=$this->addVille($donnees);
        $queryBuilder->insert('Activite')

            ->set('nom', ':nom')
            ->set('prenom', ':prenom')
            ->set('idVille', ':idVille')
            ->set('telephone', ':telephone')
            ->set('rang', ':rang')
            ->set('adresseMail', ':adresseMail')
            ->where('idActivite = :id')
            ->setParameter(':idActivite', $id)
            ->setParameter(':nom', $donnees['nom'])
            ->setParameter(':prenom', $donnees['prenom'])
            ->setParameter(':idVille', $idVille)
            ->setParameter(':telephone', $donnees['telephone'])
            ->setParameter(':rang', $donnees['rang'])
            ->setParameter(':adresseMail', $donnees['adresseMail'])
        ;
        $this->deleteActivite($id);
        return $queryBuilder->execute();
    }
*/
    public function deleteActivite($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->delete('Activite')
            ->where('idActivite = :id')
            ->setParameter(':id', intval($id))
        ;
        return $queryBuilder->execute();
    }


}