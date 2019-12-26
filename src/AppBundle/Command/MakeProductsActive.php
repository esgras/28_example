<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\User;
use Symfony\Component\VarDumper\VarDumper;

class MakeProductsActive extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:make-products-active');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $products = $this->em->getRepository(Product::class)->findBy(['id' => [1, 2]]);
        foreach ($products as $product) {
            $product->setStatus(Product::STATUS_ACTIVE);
        }

        $this->em->flush();


        die("Products is active now" . PHP_EOL);

    }


}