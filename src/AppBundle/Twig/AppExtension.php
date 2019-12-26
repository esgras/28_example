<?php

namespace AppBundle\Twig;

use AppBundle\Entity\ActivationRequest;
use AppBundle\Entity\Common\Meta;
use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\MessageFromSearch;
use AppBundle\Entity\Payment\RefundRequest;
use AppBundle\Entity\UserReview;
use AppBundle\Helper\ConstantHelper;
use AppBundle\Helper\MetaHelper;
use AppBundle\Service\Common\MetaTag\MetaTagHelper;
use AppBundle\Service\StringHelper;
use AppBundle\Service\ViewWidgets\OfferHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\VarDumper\VarDumper;
use AppBundle\Entity\User;

class  AppExtension extends \Twig_Extension
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }


    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_granted_for_collection', [$this, 'isGrantedForCollection']),
            new \Twig_SimpleFunction('confUrl', [$this, 'confUrl']),
            new \Twig_SimpleFunction('isLocalVideo', [$this, 'isLocalVideo'])
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('plural', [$this, 'plural'])
        ];
    }



    public function isGrantedForCollection($attributes, array $collection)
    {
        foreach($collection as $subject) {
            if ($this->authorizationChecker->isGranted($attributes, $subject)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Detect & return the ending for the plural word
     *
     * @param  integer $endings  nouns or endings words for (1, 4, 5)
     * @param  array   $number   number rows to ending determine
     *
     * @return string
     *
     * @example:
     * {{ ['Остался %d час', 'Осталось %d часа', 'Осталось %d часов']|plural(11) }}
     * {{ count }} стат{{ ['ья','ьи','ей']|plural(count)
     */
    public function plural($endings, $number)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        $n = $number;
        return sprintf($endings[ ($n%100>4 && $n%100<20) ? 2 : $cases[min($n%10, 5)] ], $n);
    }

    public function isLocalVideo($videoPath)
    {
        return strpos($videoPath, '/upload') !== false ;
    }

}