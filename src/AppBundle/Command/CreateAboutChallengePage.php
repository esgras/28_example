<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\CompanyPage;
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

class CreateAboutChallengePage extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:create-about-challenge');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $slug = ConstantHelper::ABOUT_PAGE_SLUG;

        $pages = $this->em->getRepository(CompanyPage::class)->findOneBy(['slug' => $slug]);

        if ($pages) {
            die("About Challenge page exists");
        }

        $companyPage = new CompanyPage();
        $companyPage->setText('')->setTitle('Узнать подробнее о челлендже')
            ->setSlug($slug);
            $this->em->persist($companyPage);

        $this->em->flush();


        echo 'About Challenge page exists', PHP_EOL;
        die;
    }


}