<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    public function gitCitiesByName($name)
    {
        $dql = 'SELECT c.name FROM AppBundle\Entity\City c
                WHERE c.name LIKE :city';

        return $this->_em->createQuery($dql)->setParameters([
            'city' => '%' . $name . '%'
        ])->setMaxResults(50)->getResult('COLUMN_HYDRATOR');
    }

    public function getCitiesList()
    {
        $dql = 'SELECT c.name FROM AppBundle\Entity\City c
                ORDER BY c.name ASC';

        return $this->_em->createQuery($dql)->getResult('COLUMN_HYDRATOR');
    }


}