<?php

namespace AppBundle\Command;

use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Landing\Template;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\User;
use Symfony\Component\VarDumper\VarDumper;

class CreateLandingTemplates extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:create-landing-templates')
            ->setDescription('Create Templates for Landing Page');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $cqs = $this->em->getRepository(Template::class)->findAll();

        if ($cqs) {
            die("Templates exists");
        }

        foreach ($this->templateConfigs() as $key => $templateConfig) {
            $template = new Template();
            $template->setName($key);
            $template->setData($templateConfig['data']);
            $this->em->persist($template);
        }


        $this->em->flush();

        echo 'Landing templates was created', PHP_EOL;
        die;
    }


    protected function templateConfigs()
    {
        return [
            'red_line' => [
                'data' => [
                    'title' => Template::DATA_TYPE_TEXT,
                    'subtitle' => Template::DATA_TYPE_TEXT,
                    'text' => Template::DATA_TYPE_TEXT,
                ]
            ],
            'yellow_line' => [
                'data' => [
                    'text' => Template::DATA_TYPE_TEXT,
                ]
            ],
            'list_block' => [
                'data' => [
                    'header' => Template::DATA_TYPE_TEXT,
                    'title' => Template::DATA_TYPE_TEXT,
                    'text' => Template::DATA_TYPE_TEXT,
                    'list' => [
                        'list_title' => Template::DATA_TYPE_TEXT,
                        'list_text' => Template::DATA_TYPE_TEXT,
                    ],
                    'youtube' => [
                        'youtube_link' => Template::DATA_TYPE_TEXT
                    ]
                ]
            ],
            'change_block' => [
                'data' => [
                    'title' => Template::DATA_TYPE_TEXT,
                    'subtitle' => Template::DATA_TYPE_TEXT,
                    'text' => Template::DATA_TYPE_TEXT,
                    'button' => Template::DATA_TYPE_TEXT,
                    'footer' => Template::DATA_TYPE_TEXT,
                ]
            ],
            'journey_block' => [
                'data' => [
                    'first' => Template::DATA_TYPE_TEXT,
                    'yellow' => Template::DATA_TYPE_TEXT,
                    'second' => Template::DATA_TYPE_TEXT,
                    'journey' => Template::DATA_TYPE_TEXT,
                    'button' => Template::DATA_TYPE_TEXT,
                    'footer' => Template::DATA_TYPE_TEXT,
                ]
            ],
            'video_block' => [
                'data' => [
                    'link' => Template::DATA_TYPE_TEXT
                ]
            ],
            'menu_line' => [
                'data' => [
                    'logo' => Template::DATA_TYPE_TEXT,
                    'links' => [
                        'first' => Template::DATA_TYPE_TEXT,
                        'second' => Template::DATA_TYPE_TEXT,
                    ],
                    'button' => Template::DATA_TYPE_TEXT
                ]
            ],
            'banner' => [
                'data' => [
                    'img' => Template::DATA_TYPE_TEXT,
                    'title' => Template::DATA_TYPE_TEXT,
                    'yellow' => Template::DATA_TYPE_TEXT,
                    'text' => Template::DATA_TYPE_TEXT,
                    'button' => Template::DATA_TYPE_TEXT,
                    'footer' => Template::DATA_TYPE_TEXT
                ]
            ],
            'featured_line' => [
                'data' => [
                    'img' => Template::DATA_TYPE_TEXT,
                    'text' => Template::DATA_TYPE_TEXT
                ]
            ],
            'pill_line' => [
                'data' => [
                    'img' => Template::DATA_TYPE_TEXT,
                    'first' => Template::DATA_TYPE_TEXT,
                    'bold' => Template::DATA_TYPE_TEXT,
                    'second' => Template::DATA_TYPE_TEXT,
                    'third' => Template::DATA_TYPE_TEXT,
                    'footer' => Template::DATA_TYPE_TEXT
                ]
            ],
            'phone_line' => [
                'data' => [
                    'text' => Template::DATA_TYPE_TEXT,
                    'button' => Template::DATA_TYPE_TEXT,
                    'footer' => Template::DATA_TYPE_TEXT,
                    'first_title' => Template::DATA_TYPE_TEXT,
                    'first_text' => Template::DATA_TYPE_TEXT,
                    'second_title' => Template::DATA_TYPE_TEXT,
                    'second_text' => Template::DATA_TYPE_TEXT,
                    'third_title' => Template::DATA_TYPE_TEXT,
                    'third_text' => Template::DATA_TYPE_TEXT,
                    'fourth_title' => Template::DATA_TYPE_TEXT,
                    'fourth_text' => Template::DATA_TYPE_TEXT,
                    'fifth_title' => Template::DATA_TYPE_TEXT,
                    'fifth_text' => Template::DATA_TYPE_TEXT,
                    'sixth_title' => Template::DATA_TYPE_TEXT,
                    'sixth_text' => Template::DATA_TYPE_TEXT,
                    'image' => Template::DATA_TYPE_TEXT,
                ]
            ]
        ];
    }

}