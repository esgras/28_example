<?php

namespace AppBundle\Command\Settings;

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

class CreateDiscountMailSettings extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:create-discount-mail-settings');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $configs = $this->em->getRepository(Config::class)->findBy(['type' => Config::TYPE_DISCOUNT_MAIL]);

        if ($configs) {
            die("Discount Configs exist");
        }


        $names = [
            ConstantHelper::DISCOUNT_STATUS_KEY => 'Статус',
            ConstantHelper::DISCOUNT_VALUE_KEY => 'Скидка в %',
            ConstantHelper::DISCOUNT_MAX_COUNT_KEY => 'Количество использований скидки',
            ConstantHelper::DISCOUNT_EXPIRED_KEY => 'Длительность в днях',
            ConstantHelper::DISCOUNT_USED_KEY => 'Скидка использована',
        ];

        foreach ($names as $name => $label) {
            $setting = new Config();
            $setting->setType(Config::TYPE_DISCOUNT_MAIL)
                ->setName($name)
                ->setLabel($label)
                ->setValue(NULL);
            $this->em->persist($setting);
        }

        $this->em->flush();


        echo 'Discount configs created', PHP_EOL;
        die;
    }


}