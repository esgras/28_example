<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Helper\ConstantHelper;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getUsersWithProducts()
    {
        $dql = 'SELECT u, p FROM AppBundle\Entity\User u
                INNER JOIN u.product p
                WHERE u.dayNumber IS NOT NULL';

        return $this->_em->createQuery($dql)->getResult();
    }

    public function getUsersWithProductsAndDays()
    {
        $dql = 'SELECT u, p, pds FROM AppBundle\Entity\User u
                INNER JOIN u.product p
                LEFT JOIN p.productDays pds
                WHERE u.dayNumber IS NOT NULL';

        return $this->_em->createQuery($dql)->getResult();
    }

    public function getWaitingForStart()
    {
        $dql = 'SELECT u, p, pds FROM AppBundle\Entity\User u
                INNER JOIN u.product p
                LEFT JOIN p.productDays pds
                WHERE u.dayNumber IS NULL AND p.id != :mainProductId AND u.isWaitingForStart = :isWaitingStatus';

        return $this->_em->createQuery($dql)->setParameters([
            'mainProductId' => ConstantHelper::PRODUCT_MAIN_ID,
            'isWaitingStatus' => User::STATUS_WAITING_FOR_STATUS
        ])->getResult();
    }
}