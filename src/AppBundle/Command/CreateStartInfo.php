<?php

namespace AppBundle\Command;

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

class CreateStartInfo extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:create-start')
            ->setDescription('Create Start Information')
            ->setHelp('Try to create start information...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $cqs = $this->em->getRepository(CommonQuestion::class)->findAll();

        if ($cqs) {
            die("Start info EXISTS");
        }

        $arr = [
            ['title' => 'Почему 90 дней? Неужели мне так долго нужно идти?',
            'text' => 'Алкоголь - один из самых известных депрессантов, подавляющий в том числе тревогу… Подвох заключается в том, что на утро его действие проходит.'],
            ['title' => 'Что произойдет со мной за 28 дней?',
                'text' => 'Мы на основе своего опыта пришли к выводу, что как раз после 28 дня начинает происходить настоящая магия: вы получаете свободу
                                выбора.'],
            ['title' => 'Вы будете выглядеть лучше, чем когда-либо',
                'text' => 'Очевидно, что действие токсинов, в первую очередь, отражается на состоянии вашей кожи. При отказе от алкоголя состояние кожи, как правило, улучшается настолько заметно, что многие из вашего окружения сразу обратят на это внимание'],
            ['title' => 'Больше денег на вашем счете',
                'text' => 'Самом собой разумеется, что деньги которые вы потратили бы на выпивку останутся при вас. Сэкономленных денег с лихвой хватит на то, чтобы каждый год всей семьей ездить в отпуск. Или же отложить эти деньги на черный день и перестать о беспокоиться о том, что он может наступить.'],
        ];

        for ($i = 0; $i < 2; $i++) {
            foreach ($arr as $item) {
                $cq = new CommonQuestion();
                $cq->setTitle($item['title'])->setText($item['text']);
                $this->em->persist($cq);
            }
        }

        $arr = [
            [
                'authorName' => 'Николай',
                'text' => 'Всегда иду до конца',
                'minutes' => 0,
                'seconds' => 34,
                'link' => 'https://www.youtube.com/watch?v=lir3dzYIhz0',
                'linkPreview' => '/assets/img/reviews-img01.png'
            ],
            [
                'authorName' => 'Алексей',
                'text' => 'Научился держать себя в руках',
                'minutes' => 1,
                'seconds' => 46,
                'link' => 'https://www.youtube.com/watch?v=FMvppW6kfIE',
                'linkPreview' => '/assets/img/reviews-img02.png'
            ],
            [
                'authorName' => 'Сергей',
                'text' => 'Мысли стали яснее, цели понятнее',
                'minutes' => 0,
                'seconds' => 36,
                'link' => 'https://www.youtube.com/watch?v=FMvppW6kfIE',
                'linkPreview' => '/assets/img/reviews-img03.png'
            ],
            [
                'authorName' => 'Александр',
                'text' => 'Жить',
                'minutes' => 0,
                'seconds' => 44,
                'link' => 'https://www.youtube.com/watch?v=BE_0m5OkyN4',
                'linkPreview' => '/assets/img/reviews-img04.png'
            ],
        ];

        for ($i = 0; $i < 2; $i++) {
            foreach ($arr as $item) {
                $cq = new Feedback();
                $cq->setAuthorName($item['authorName'])
                    ->setText($item['text'])->setLink($item['link'])
                    ->setSeconds($item['seconds'])->setMinutes($item['minutes'])
                    ->setLinkPreview($item['linkPreview'])->setHardLinkPreview(true);
                $this->em->persist($cq);
            }
        }

        $posts = [
            [
                'title' => 'Фильмы которые изменят тебя навсегда',
                'text' => '',
                'image' => '434c095c5bfd566234eddd58a666e3c8.png'
            ],
            [
                'title' => 'Как перестать думать об алкоголе',
                'text' => '',
                'image' => '52221ad7257bad1c222bdd0ddf10d49d.png'
            ],
            [
                'title' => '12 эффективных упражнений для пресса',
                'text' => '',
                'image' => '1e30a1538c7473fa1f95601931e4f3f9.png'
            ],
            [
                'title' => 'Подборка лучших книг для души',
                'text' => '',
                'image' => 'f94760e9c73d9b9aa16d433afaee4595.png'
            ]

        ];

        for ($i = 0; $i < 2; $i++) {
            foreach ($posts as $post) {
                $newPost = new Post();
                $newPost->setTitle($post['title'])->setText($post['text'])
                    ->setImageFile($post['image']);
                $this->em->persist($newPost);
            }
        }


        $this->em->flush();

        echo 'Start information was created', PHP_EOL;
        die;
    }

}