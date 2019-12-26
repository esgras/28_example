<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Config;
use AppBundle\Entity\User;
use AppBundle\Helper\ConstantHelper;
use AppBundle\Hydrators\ColumnHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BaseController extends Controller
{
    /** @var  $em EntityManagerInterface */
    protected $em;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $this->getDoctrine()->getManager();
        $this->em->getConfiguration()->addCustomHydrationMode('COLUMN_HYDRATOR', ColumnHydrator::class);

    }

    protected function loginUser(User $user, $password=null)
    {

        $token = new UsernamePasswordToken(
            $user,
            $password,
            'main', #firewall
            $user->getRolesArray()
        );
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
    }

    protected function getInfoEmail()
    {
        $conf = $this->em->getRepository(Config::class)->findOneBy(['name' => ConstantHelper::INFO_EMAIL]);

        return $conf && $conf->getValue() ? $conf->getValue() : $this->getParameter('info_email');
    }
}