<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Entity\CommonQuestion;
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

class CityList extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:city-list');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $cities = $this->em->getRepository(City::class)->findAll();
        if ($cities) {
            die("CITIES EXIST");
        }

        $cities = file_get_contents(__DIR__ . '/../../../var/file_cities.txt');
        $cities = preg_split("#',\s'#", $cities);
        $cities = array_map('trim', $cities);
        $cities = array_unique($cities);
        $cities = $this->array_iunique($cities);

        foreach ($cities as $key => $city) {
            if (strpos($city, 'ё') !== false) {
                unset($cities[$key]);
            }
        }

        $count = 30;
        $i = 0;


        foreach ($cities as $city) {
            $c = new City();
            $c->setName(trim($city, "'"));
            $this->em->persist($c);
            $i++;

            if ($i == $count) {
                $i = 0;
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();

        die("CITIES WAS CREATED");

    }

    private function array_iunique( $array ) {
        return array_intersect_key(
            $array,
            array_unique( array_map( "strtolower", $array ) )
        );
    }


    public function forceSendEmail()
    {
        $mailer = $this->getContainer()->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->getContainer()->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }


}