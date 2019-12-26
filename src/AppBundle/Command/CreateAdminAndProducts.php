<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\User;
use Symfony\Component\VarDumper\VarDumper;

class CreateAdminAndProducts extends ContainerAwareCommand
{
    const ADMIN_EMAIL = 'admin@mail.com';
    const ADMIN_PASSWORD = 'admin';

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:create-all')
            ->setDescription('Create Admin User and Products')
            ->setHelp('Try to create admin dashboard user and products...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $products = $this->em->getRepository(Product::class)->findAll();

        if ($products) {
            die("PRODUCTS EXISTS");
        }

        $product = (new Product())->setName('Месяц')->setText('Ежедневные видео и задания Доступ в закрутую группу')->setPrice(2990)->setDays(28);
        $this->em->persist($product);

        $product = (new Product)->setName('3 Месяца')->setText('Ежедневные видео и задания Доступ в закрутую группу')->setPrice(6990)->setDays(90);
        $this->em->persist($product);

        $product = (new Product)->setName('Год')->setText('Ежедневные видео и задания Доступ в закрутую группу')->setPrice(14990)->setDays(365);
        $this->em->persist($product);


        $user = new User();
        $user->setEmail(self::ADMIN_EMAIL)->setRoles([User::ROLE_SUPER_ADMIN])
            ->setPassword(password_hash(self::ADMIN_PASSWORD, PASSWORD_BCRYPT))
            ->setName('Admin');
        $this->em->persist($user);

        $this->em->flush();

        echo 'ADMIN and Products was created', PHP_EOL;
        die;
    }

}