<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\Config;
use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use AppBundle\Helper\ConstantHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\User;
use Symfony\Component\VarDumper\VarDumper;

class AddSettings extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:add-settings');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $settings = [
            'info_email' => ['value' => 'info@28dney.ru', 'label' => 'E-mail для уведомлений о покупках']
        ];

        $setts = $this->em->getRepository(Config::class)->findBy(['name' => array_keys($settings)]);

        if ($setts) {
            die("Settings Exist");
        }

        foreach ($settings as $key => $setting) {
            $sett = new Config();
            $sett->setName($key)->setType(Config::TYPE_MAIN);
            if ($setting['value']) {
                $sett->setValue($setting['value']);
            }
            if ($setting['label']) {
                $sett->setLabel($setting['label']);
            }

            $this->em->persist($sett);
        }


        $this->em->flush();


        echo 'Settings added', PHP_EOL;
        die;
    }


}