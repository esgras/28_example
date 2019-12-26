<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function getProductsWithDays()
    {
        $dql = 'SELECT p, pds FROM AppBundle\Entity\Product p
                LEFT JOIN p.productDays pds';

        return $this->_em->createQuery($dql)->getArrayResult();
    }

    public function getProductWithDays($productId)
    {
        $dql = 'SELECT p, pds FROM AppBundle\Entity\Product p
                LEFT JOIN p.productDays pds
                WHERE p = :product';

        return $this->_em->createQuery($dql)
            ->setParameters(['product' => $productId])->getResult();
    }

}