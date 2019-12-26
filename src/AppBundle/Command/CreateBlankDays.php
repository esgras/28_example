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

class CreateBlankDays extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:create-blank-days');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $days = $this->em->getRepository(Day::class)->findAll();
        if ($days) {
            die("Days exist");
        }

        $len = 28;
        /** @var Product $product */
        $products = $this->em->getRepository(Product::class)->findAll();

        $imageHelper = $this->getContainer()->get('app.image_helper');
        $videoHelper = $this->getContainer()->get('app.video_helper');
        $dir = $this->getContainer()->getParameter('kernel.project_dir') . '/var/storage/';
        $video = $dir . '3d.mp4';
        $image = $dir . 'blog-article01.png';

        $text = 'Сегодняшний день - один из самых важных дней в вашей жизни. Пожалуйста не пропускайте первое упражнение. Найдите укромный тихий уголок. Хорошенько подумайте и запишите на бумаге или на электронном носителе (но обязательно именно запишите, а не просто придумайте) причины, по которым вы решили отказаться от алкоголя. Не торопитесь, копните глубже, будьте с собой честны.';
        $title = 'К взлету готов';

        foreach ($products as $product) {
            $days = [];
            echo "For Product #{$product->getId()}", PHP_EOL;
            for ($i = 1; $i <= $product->getDays(); $i++) {
                $day = new Day;
                $day->setTitle($title . ' день #' . $i)->setText($text)->setNumber($i)
                    ->setVideoFile(basename($video))->setImageFile(basename($image));
                $this->em->persist($day);
                $days[] = $day;
                $product->addProductDay($day);
                echo "\tDay {$i} before creating", PHP_EOL;
            }

            $this->em->flush();

            foreach ($days as $key => $day) {
                $imageHelper->makeImageForFile($day, $image);
                $videoHelper->makeVideoForFile($day, $video);
                echo "\tDay {$day->getNumber()} image and video creating", PHP_EOL;
            }
        }


        die("DAYS WAS CREATED" . PHP_EOL);

    }


}