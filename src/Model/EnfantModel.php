<?php
/**
 * Created by PhpStorm.
 * User: Théo
 * Date: 15/10/2017
 * Time: 12:01
 */
namespace App\Model;
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class EnfantModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllEnfants(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idEnfant', 'e.nomEnfant', 'e.prenomEnfant', 'e.dateDeNaissance', 'c.nomClasse','n.nomNiveau' )
            ->from('enfant', 'e')
            ->from('classe','c')
            ->from('niveau','n')
            ->where('e.idClasse=c.idClasse')
            ->andWhere('e.idNiveau=n.idNiveau')
            ->addGroupBy('e.idClasse');
        return $queryBuilder->execute()->fetchAll();
    }

    public function getEnfant($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idEnfant','e.nomEnfant', 'e.prenomEnfant', 'e.dateDeNaissance', 'c.nomClasse','n.nomNiveau' )
            ->from('enfant', 'e')
            ->from('classe','c')
            ->from('niveau','n')
            ->where('e.idEnfant = :id')
            ->andWhere('e.idClasse = c.idClasse')
            ->andWhere('e.idNiveau = n.idNiveau')
            ->setParameter(':id',$id);
        return $res = $queryBuilder->execute()->fetch();
    }

    public function addNiveauByNom(){
        $nomNiveau = $_POST['nomNiveau'];
        $quotedNomNiveau ="'" . str_replace(",", "'", $nomNiveau) . "'";
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('idNiveau')
            ->from('niveau','n')
            ->where($queryBuilder->expr()->eq('n.nomNiveau',$quotedNomNiveau))
            ->addOrderBy('n.idNiveau');
        $idNiveau=intval($queryBuilder->execute()->fetchColumn(0));

        if(!$idNiveau){
            $queryBuilder->insert('niveau')
                ->values([
                    'idNiveau'=> 'NULL',
                    'nomNiveau'=> $quotedNomNiveau,
                ]);
            $queryBuilder->execute();
            $queryBuilder->select('idNiveau')
                ->from('niveau','n')
                ->where($queryBuilder->expr()->eq('n.nomNiveau',$quotedNomNiveau))
                ->addOrderBy('n.idNiveau');
            $idNiveau=intval($queryBuilder->execute()->fetchColumn(0));
        }


        return $idNiveau;
    }

    public function addClasseByNom(){
        //la même que adulte.addVille : prend nomClasse, retourne idClasse
        $nomClasse = $_POST["nomClasse"];
        $quotedNomClasse = "'" . str_replace(",", "'", $nomClasse) . "'";

        $queryBuilder = new QueryBuilder($this->db);
        //Sale select, avec nom :<
        $queryBuilder->select('idClasse')
            ->from('classe','c')
            ->where($queryBuilder->expr()->eq('c.nomClasse',$quotedNomClasse))
            ->addOrderBy('c.idClasse');
        $idClasse=intval($queryBuilder->execute()->fetchColumn(0));

        if(!$idClasse){
            $queryBuilder->insert('classe')
                ->values([
                    'idClasse'=> 'NULL',
                    'nomClasse'=> $quotedNomClasse,
                    'professeur'=>'null',
                    'profRespClasse' => 0
                ]);
            $queryBuilder->execute();
            $queryBuilder->select('idClasse')
                ->from('classe','c')
                ->where($queryBuilder->expr()->eq('c.nomClasse',$quotedNomClasse))
                ->addOrderBy('c.idClasse');
            $idClasse=intval($queryBuilder->execute()->fetchColumn(0));
        }

        return $idClasse;
    }

    public function getEnfantIdByNomPrenom($nomEnfant, $prenomEnfant){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idEnfant')
            ->from('enfant', 'e')
            ->where('e.nomEnfant = :nomEnfant')
            ->andWhere('e.prenomEnfant = :prenomEnfant')
            ->setParameter(':nomEnfant', $nomEnfant)
            ->setParameter(':prenomEnfant',$prenomEnfant);


        $res = intval($queryBuilder->execute()->fetchColumn(0));

        return $res;
    }

    public function getEnfantOfParent($idAdulte){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idEnfant', 'e.nomEnfant', 'e.prenomEnfant', 'e.dateDeNaissance', 'e.idClasse', 'e.idNiveau')
            ->from('enfant', 'e')
            ->inerJoin('e', 'autorisemodif', 'a', '')
            ->Where('e.idEnfant = a.idEnfant')
            ->Where('a.idAdulte = :idAdulte')
            ->groupBy('idEnfant')
            ->setParameter(':idAdulte', htmlentities($idAdulte));
        return $queryBuilder->execute()->fetchAll();
    }


    public function getEnfantBySession($username){
        $username=htmlentities($username);
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idEnfant','e.nomEnfant','e.prenomEnfant','e.dateDeNaissance','c.nomClasse','n.nomNiveau')
            ->from('autorisemodif')
            ->from('adulte')
            ->from('enfant', 'e')
            ->from('classe', 'c')
            ->from('niveau', 'n')
            ->Where('adulte.username = :username')
            ->andWhere('autorisemodif.idAdulte = adulte.idAdulte')
            ->andWhere('autorisemodif.idEnfant = e.idEnfant')
            ->andWhere('c.idClasse = e.idClasse ')
            ->andWhere('n.idNiveau = e.idNiveau')
            ->groupBy('idEnfant','autorisemodif.idAdulte')
            ->setParameter(':username', $username);

        $res = $queryBuilder->execute()->fetchAll();

        return $res;
    }


    public function addEnfant($donnees, $idParent){

        $idClasse=$this->addClasseByNom();
        $idNiveau = $this->addNiveauByNom();
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('enfant')
            ->values([
                'nomEnfant' => '?',
                'prenomEnfant' => '?',
                'dateDeNaissance' => '?',
                'idClasse' => '?',
                'idNiveau' => '?'
            ])
            ->setParameter(0, $donnees['nomEnfant'])
            ->setParameter(1, $donnees['prenomEnfant'])
            ->setParameter(2, date('Y-m-d', strtotime($donnees['dateDeNaissance']))) //attention format date : pour requetes format américain
            ->setParameter(3, $idClasse)
            ->setParameter(4, $idNiveau);

        ;
        $queryBuilder->execute();
        $queryBuilder = new QueryBuilder($this->db);
        $idEnfant = $this->getEnfantIdByNomPrenom($donnees['nomEnfant'],$donnees['prenomEnfant']);
        $queryBuilder->insert('autorisemodif')
            ->values(['idEnfant' => '?',
                            'idAdulte' => '?'])
            ->setParameter(0, $idEnfant)
            ->setParameter(1,$idParent);
        return $queryBuilder->execute();
    }
/*
    public function getNiveauByNom($nomNiveau){
        $queryBuilder= new QueryBuilder($this->db);
        $queryBuilder->select('idNiveau')
            ->from('niveau' )
            ->where('nomNiveau = :nomNiveau')
            ->setParameter(':nomNiveau',$nomNiveau)
        ;
        return $queryBuilder->execute();
    }

    public function getNiveauByIDEnfant($id){
        $queryBuilder= new QueryBuilder($this->db);
        $queryBuilder->select('n.idNiveau')
            ->from('niveau','n')
            ->where('enfant.idNiveau = n.idNiveau')
            ->andWhere('enfant.idNiveau = :id')
            ->setParameter(':id',$id)
            ;
        return $queryBuilder->execute();
    }
*/

    public function updateEnfant($id, $donnees){
        $idClasse=$this->addClasseByNom();
        $idNiveau = $this->addNiveauByNom();
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('enfant')
            ->set('nomEnfant', ':nomEnfant')
            ->set('prenomEnfant', ':prenomEnfant')
            ->set('idClasse', ':idClasse')
            ->set('dateDeNaissance', ':dateDeNaissance')
            ->set('idNiveau',':idNiveau')
            ->where('idEnfant =:id')
            ->andWhere('niveau.idNiveau = enfant.idNiveau')
            ->andWhere('classe.idClasse = enfant.idClasse')
            ->setParameter(':nomEnfant', $donnees['nomEnfant'])
            ->setParameter(':prenomEnfant', $donnees['prenomEnfant'])
            ->setParameter(':idClasse', $idClasse)
            ->setParameter(':dateDeNaissance', date('Y-m-d', strtotime($donnees['dateDeNaissance'])))
            ->setParameter(':id', intval($id))
            ->setParameter(':idNiveau',$idNiveau)

        ;
        return $queryBuilder->execute();
    }



    public function deleteEnfant($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->delete('enfant')
            ->where('idEnfant = :id')
            ->setParameter(':id', intval($id))
        ;
        return $queryBuilder->execute();
    }
}