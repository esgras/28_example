<?php

namespace AppBundle\Repository;

use AppBundle\Helper\ConstantHelper;
use Doctrine\ORM\EntityRepository;

class ConfigRepository extends EntityRepository
{
    public function getDiscountConfigs()
    {
        $dql = 'SELECT c FROM AppBundle\Entity\Config c
                WHERE c.type=:type AND c.name IN (:keys)';

        return $this->_em->createQuery($dql)->setParameters([
            'type' => \AppBundle\Entity\Config::TYPE_DISCOUNT_MAIL,
            'keys' => [ConstantHelper::DISCOUNT_STATUS_KEY, ConstantHelper::DISCOUNT_VALUE_KEY, ConstantHelper::DISCOUNT_MAX_COUNT_KEY, ConstantHelper::DISCOUNT_EXPIRED_KEY]
        ])->getResult();
    }


}