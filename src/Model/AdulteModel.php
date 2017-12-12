<?php

namespace App\Model;
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class AdulteModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllAdultes(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idAdulte', 'e.nom', 'e.prenom', "e.rang", "v.nomVille","e.username","e.password", "e.telephone", "e.adresseMail")
            ->from('adulte', 'e')
            ->from('ville', 'v')
            ->where('e.idVille=v.idVille')
            ->addOrderBy('e.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }



    public function getAdulte($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idAdulte', 'e.nom',"e.prenom", "e.idVille","e.username","e.password", "e.telephone", "e.adresseMail", "e.rang")
            ->from('adulte', 'e')
            ->where('e.idAdulte = :idAdulte')
            ->setParameter(':idAdulte', intval($id));

        $res = $queryBuilder->execute()->fetch();
        if (!$res){
            return $res;
        }
        return [
            'idAdulte' => $res['idAdulte'],
            'nom' => $res['nom'],
            'prenom' => $res['prenom'],
            'idVille' => $res['idVille'],
            'username' => $res['username'],
            'password' => $res['password'],
            'telephone' => $res['telephone'],
            'adresseMail' => $res['adresseMail'],
            'rang' => $res['rang']
        ];
    }
    public function getAdulteIdByNomPrenom($nom,$prenom){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idAdulte')
            ->from('adulte', 'e')
            ->where('e.nom = :nom')
            ->andWhere('e.prenom = :prenom')
            ->setParameter(':nom', $nom)
            ->setParameter(':prenom', $prenom);


        $res = intval($queryBuilder->execute()->fetchColumn(0));

        return $res;
    }

    public function getAdulteIdBySession($username){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idAdulte')
            ->from('adulte', 'e')
            ->where('e.username = :username')
            ->setParameter(':username', $username);

        $res = intval($queryBuilder->execute()->fetchColumn(0));

        return $res;
    }


    public function addVille($donnees){

        $newVille = $_POST["nomVille"];
        $quotedNewVille = "'" . str_replace(",", "'", $newVille) . "'";
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('idVille')
            ->from('ville')
            ->where($queryBuilder->expr()->eq('nomVille',$quotedNewVille));
        $idVille=$queryBuilder->execute()->fetchColumn(0);
        if(!$idVille) {
            $queryBuilder->insert('ville')
                ->values([
                    'idVille' => 'NULL',
                    'nomVille' => $quotedNewVille,
                    'codePostal' => $donnees['codePostal']
                ]);
            $queryBuilder->execute();
            $queryBuilder->select('idVille')
                ->from('ville')
                ->where($queryBuilder->expr()->eq('nomVille',$quotedNewVille));
            $idVille=$queryBuilder->execute()->fetchColumn(0);
        }


            return $idVille;

    }


    public function addAdulte($donnees){
        $idVille=$this->addVille($donnees);
        $nomPrenom = $donnees['nom'].$donnees['prenom'];
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('adulte')
            ->values([
                'nom' => '?',
                'prenom' => '?',
                'idVille' => '?',
                'telephone' => '?',
                'adresseMail' => '?',
                'username' => '?',
                'password' => '?',
                'rang' => 1
            ])
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['prenom'])
            ->setParameter(2, $idVille)
            ->setParameter(3, $donnees['telephone'])
            ->setParameter(4, $donnees['adresseMail'])
            ->setParameter(5, $nomPrenom)
            ->setParameter(6, $donnees['password'])
        ;
        $queryBuilder->execute();
        return $this->getAdulteIdByNomPrenom($donnees['nom'],$donnees['prenom']);

    }

    public function getVille($id){
        return $this->addVille($id);
    }
    public function updateAdulte($id, $donnees){
        $queryBuilder = new QueryBuilder($this->db);

        $idVille=$this->addVille($donnees);
        $queryBuilder->insert('adulte')

            ->set('nom', ':nom')
            ->set('prenom', ':prenom')
            ->set('idVille', ':idVille')
            ->set('telephone', ':telephone')
            ->set('rang', ':rang')
            ->set('adresseMail', ':adresseMail')
            ->where('idAdulte = :id')
            ->setParameter(':idAdulte', $id)
            ->setParameter(':nom', $donnees['nom'])
            ->setParameter(':prenom', $donnees['prenom'])
            ->setParameter(':idVille', $idVille)
            ->setParameter(':telephone', $donnees['telephone'])
            ->setParameter(':rang', $donnees['rang'])
            ->setParameter(':adresseMail', $donnees['adresseMail'])
        ;
        $this->deleteAdulte($id);
        return $queryBuilder->execute();
    }
    public function deleteAdulte($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->delete('adulte')
            ->where('idAdulte = :id')
            ->setParameter(':id', intval($id))
        ;
        return $queryBuilder->execute();
    }

    public function addAdulteResp($idEnfant, $idParent){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('autorisemodif')
            ->values(['idEnfant' => '?',
                'idAdulte' => '?'])
            ->setParameter(0, $idEnfant)
            ->setParameter(1,$idParent);
        return $queryBuilder->execute();
    }

    public function loginCheckAdulte($login,$mdp){
        $sql = "SELECT username, password FROM adulte WHERE username = ? AND password = ?";
        $res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
        if($res->rowCount()==1)
            return $res->fetch();
        else
            return NULL;
    }
}