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
            ->select('e.idAdulte', 'e.nom', 'e.prenom', "e.role", "e.adresse", "e.ville", "e.code_postal", "e.username", "e.telephone", "e.adresseMail")
            ->from('adulte', 'e')
            ->addOrderBy('e.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }

    public function getAdulte($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.idAdulte', 'e.nom',"e.prenom", "e.adresse", "e.ville", "e.code_postal", "e.username", "e.telephone", "e.adresseMail", "e.role")
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
            'adresse' => $res['adresse'],
            'code_postal' => $res['code_postal'],
            'ville' => $res['ville'],
            'telephone' => $res['telephone'],
            'username' => $res['username'],
            'adresseMail' => $res['adresseMail'],
            'role' => $res['role']
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

    public function addAdulte($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('adulte')
            ->values([
                'nom' => ':nom',
                'prenom' => ':prenom',
                'adresse' => ':adresse',
                'ville' => ':ville',
                'code_postal' => ':code_postal',
                'telephone' => ':telephone',
                'adresseMail' => ':adresseMail',
                'username' => ':username',
                'password' => ':password',
                'role' => ':role'
            ])
            ->setParameter(':nom', $donnees['nom'])
            ->setParameter(':prenom', $donnees['prenom'])
            ->setParameter(':adresse', $donnees['adresse'])
            ->setParameter(':code_postal', $donnees['code_postal'])
            ->setParameter(':ville', $donnees['ville'])
            ->setParameter(':telephone', $donnees['telephone'])
            ->setParameter(':adresseMail', $donnees['adresseMail'])
            ->setParameter(':username', $donnees['nom'].".".$donnees['prenom'])
            ->setParameter(':password', md5('projet_tutore2017'.$donnees['password']))
            ->setParameter(':role', 'ROLE_PARENT')
        ;
        $queryBuilder->execute();
        return $this->getAdulteIdByNomPrenom($donnees['nom'], $donnees['prenom']);

    }

    public function updateAdulte($id, $donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('adulte')
            ->set('nom', ':nom')
            ->set('prenom', ':prenom')
            ->set('adresse', ':adresse')
            ->set('ville', ':ville')
            ->set('code_postal', ':code_postal')
            ->set('telephone', ':telephone')
            ->set('adresseMail', ':adresseMail')
            ->where('idAdulte = :id')
            ->setParameter(':id', intval($id))
            ->setParameter(':nom', $donnees['nom'])
            ->setParameter(':prenom', $donnees['prenom'])
            ->setParameter(':ville', $donnees['ville'])
            ->setParameter(':code_postal', $donnees['code_postal'])
            ->setParameter(':adresse', $donnees['adresse'])
            ->setParameter(':telephone', $donnees['telephone'])
            ->setParameter(':adresseMail', $donnees['adresseMail']);
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
            ->setParameter(0, intval($idEnfant))
            ->setParameter(1, intval($idParent));
        return $queryBuilder->execute();
    }

    public function isUserNameAvailable($username) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('adulte')
            ->where('username = :username')
            ->setParameter('username', $username);
        return ($queryBuilder->execute()->fetch() == NULL);
    }

    public function loginCheckAdulte($login, $mdp){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('idAdulte, username, role')
            ->from('adulte')
            ->where('username = :username and password = :mdp_crypte')
            ->setParameter(':username', $login)
            ->setParameter(':mdp_crypte', md5('projet_tutore2017'.$mdp));
        return $queryBuilder->execute()->fetch();
    }
}