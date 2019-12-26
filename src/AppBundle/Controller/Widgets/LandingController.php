<?php

namespace AppBundle\Controller\Widgets;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Landing\Block;
use AppBundle\Entity\Landing\LandingPage;
use AppBundle\Entity\Landing\Template;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LandingController extends BaseController
{

    protected static $position = 1;

    
    public function landingAction()
    {

        $page = [
            'title' => 'Test Title'
        ];

        return $this->render('widgets/landing/index.html.twig', [
            'page' => $page,
            'main' => true
        ]);
    }

    public function showLandingAction($slug)
    {
        $landingPage = $this->em->getRepository(LandingPage::class)->findOneBy(['slug' => $slug, 'status' => LandingPage::STATUS_VISIBLE]);

        if (!$landingPage || $landingPage->getId() != 1) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('widgets/landing/view.html.twig', [
            'page' => $landingPage,
            'blocks' => $landingPage->getBlocks(),
            'products' => $this->em->getRepository(Product::class)->findAll(),
        ]);
    }

    protected function createBlock1(LandingPage $landingPage)
    {
        $data = [
            'title' => '25% OFF ALL OYNB CHALLENGES THIS FEBRUARY',
            'text' => 'DISCOUNT APPLIED UPON CHECKOUT WHEN YOU USE THE PROMO CODE "DRYFEB".'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock2(LandingPage $landingPage)
    {
        $data = [
            'img' => 'https://images.clickfunnels.com/f5/d0f3c0755211e8a3cd79b79af6e943/OYNB---Dark---Landscape---RGB---High-Res.png',
            'first' => 'ABOUT THE PROGRAM',
            'second' => 'WHAT\'S INCLUDED?',
            'button' => 'JOIN THE CHALLANGE!'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock3(LandingPage $landingPage)
    {
        $data = [
            'title' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL AND WATCH YOUR WHOLE WORLD CHANGE',
            'yellow' => 'Discover Why One Year No Beer Is The Leading Habit Changing Programme With 96% Of Members Transforming Their Relationship With Alcohol',
            'third' => 'Become the most productive, present and healthiest version of yourself just by making one change',
            'button' => 'YES! I WANT TO TAKE THE CHALLENGE!',
            'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.',
            'img' => 'https://images.clickfunnels.com/64/19217015a011e9993c3b6211d60693/OYNB---January-Sales-Page-Header-min.jpg'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock4(LandingPage $landingPage)
    {
        $data = [
            'title' => 'AS FEATURED IN',
            'img' => 'https://images.clickfunnels.com/d1/f8715021fe11e889771f04f4372fd5/Screen-Shot-2018-03-07-at-11.57.22-min.png'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock5(LandingPage $landingPage)
    {
        $data = [
            'title' => 'WHAT HAPPENS WHEN YOU TAKE A BREAK FROM BOOZE?'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock6(LandingPage $landingPage)
    {
        $data = [
            'img' => 'https://images.clickfunnels.com/c9/8e7640252311e889b2956bb734ee9d/oynb---stats-min.png',
            'first' => 'In 2015, Professor Kevin Moore of the Royal Free Hospital, London, co-authored one of the largest ever studies into the effects of a four-week break from alcohol.  ',
            'second' => 'The participants were average drinkers and the results were staggering.',
            'third' => 'By the end of the four weeks, the participants of the studies had each lost on average, ',
            'fourth' => 'Moore was so impressed with the findings he suggested that if there were a pill that produced similar results, everyone would want it!',
            'fifth' => 'THIS PROGRAMME IS THAT PILL',

        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock7(LandingPage $landingPage)
    {
        $data = [
            'video' => 'https://oneyearnobeer.wistia.com/medias/ph0dmnwjqa'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock8(LandingPage $landingPage)
    {
        $data = [
            'title' => 'WHY DON\'T YOU JUST STOP DRINKING?'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock9(LandingPage $landingPage)
    {
        $data = [
            'first' => 'Society has conditioned you that you need alcohol to be successful, to be cool, to be sexy, to have fun, to relax, the list goes on and on. How will you fare going up against years of social conditioning, second-hand peer pressure and self-doubt, all on your own?',
            'second' => 'The key difference between our challenges and just "stopping drinking" is that we teach you to have a mindset shift. We help break down the habits associated with drinking, the same habits that can help change multiple areas of your life.
',
            'third' => 'Of course, lots of people stop drinking on their own, like people find their six pack all by themselves. For the rest of us, we go and get a plan, a strategy, from someone who has done it before. Which takes us LEAPS and BOUNDS ahead of where we would be if we just tried to go it alone. ',
            'fourth' => 'This journey is no different.',
            'button' => 'YES! I WANT TO TAKE THE CHALLENGE!',
            'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock10(LandingPage $landingPage)
    {
        $data = [
            'title' => 'WHAT DOES THE PROGRAMME INVOLVE?'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock11(LandingPage $landingPage)
    {
        $data = [
            'first' => 'We show you how to finally take total control over your alcohol habits. Alcohol does not help you sleep, relax or have a good time. People who think this merely associate alcohol with that. Not only do we help you break your habits and associations with alcohol, we\'ll also help you understand alcohol. You do not have to give up alcohol forever. Just master pure self control.',
            'img' => 'https://images.clickfunnels.com/a3/a9d1803d7a11e88c8aeb45ce2f2fdc/oynb---mockup-_2_.png',
            'button' => 'YES! I WANT TO TAKE THE CHALLENGE!',
            'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.',
            '1_title' => 'DAILY SUPPORT & ACCOUNTABILITY',
            '1_text' => 'Our daily accountability posts bring the latest science and habit change, Every day we smash those old negative habits and build healthy new ones, with lots of support along the way!',
            '4_title' => 'SUPPORTIVE COMMUNITY',
            '4_text' => 'Science shows us that if you want to really break a habit, you need to join a tribe, already living how you want to live, in order to inspire you... So, we\'ve built just that! Over 26,000 members in 120 countries. -Everyone is welcome at OYNB.',
            '2_title' => 'UNIQUE STEP BY STEP SYSTEM
',
            '2_text' => 'Our unique step by step process will walk you through daily habit changes. This isn\'t about just giving up alcohol. The process is around breaking and building habits that arn\'t just a quick fix but last forever.
',
            '5_title' => 'DOWNLOADS AT THE TIPS OF YOUR FINGERS',
            '5_text' => 'Discover everything from mindfulness to nutrition, overcoming addictions to getting the best sleep of your life, even tips to stay alcohol-free on nights out without anybody knowing (stealth drinking).',
            '3_title' => 'GUEST TRAININGS AND Q&A\'s',
            '3_text' => 'The best experts in fitness, diet & exercise delivering regular training and webinars. Going alcohol-free is just the beginning of an adventure that will lead you to the best version of you.',
            '6_title' => 'SOCIAL CONDITIONING',
            '6_text' => 'Why is alcohol the only drug that when you stop using it, people berate and abuse you? Crazy eh? OYNB creates the PERFECT excuse for you to get past it. "Sorry mate, not tonight, I\'m doing a 28, 90 or 365-day alcohol-free challenge!"',
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock12(LandingPage $landingPage)
    {
        $data = [
            'img' => 'https://images.clickfunnels.com/d8/435380222711e89a8cdf434e955e7c/Screen-Shot-2017-06-05-at-22.32.17-min.png',
            'title' => 'I joined OYNB to lose weight, I realised I was a binge drinking and did the math, the 4000 extra calories I consumed every weekend was not helping any attempt to lose weight. The Facebook (community) page was wonderful and supportive and I quickly got into it learning the tricks or the trade from OYNB and I quickly got healthier, but then the magic happened and EVERY aspect of my life got better, I became happier, in turn my wife became happier. I found discipline and clarity. It was and is.. life changing and wonderful!
',
            'footer' => 'Michael - One Year No Beer Challenger'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock13(LandingPage $landingPage)
    {
        $data = [
            'video' => 'https://www.youtube.com/embed/hHNczP_QO6E',
            'title' => '"But Alcohol Really Helps Me..." Really? Lets talk about that.',
            'text' => "Firstly we need to identify that alcohol is a depressant, so its actually causing a large part of your anxiety and even depression.\nThere so many misconceptions:\nAlcohol helps you to sleep? Nope... Trust us, it really doesn't. Google it. Alcohol disturbs your sleep, stops you from going into REM (that's the really deep rejuvenating sleep, mmm) so you feel tired again.\nSo wait. I'm drinking alcohol to feel better but it's making me feel sad in the first place? YES."
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock14(LandingPage $landingPage)
    {
        $data = [
            'title' => 'Why Us?',
            'text' => "The problem with any other alcohol free problem is they're just a quick fix. They identify drink is a problem and focus on the negatives instead of the positive. People give up alcohol but the niggling thoughts, the cravings and for most, they end up eventually giving in and going back to drink.\nAt One Year No Beer we focus on the habit changing process. So rather than just deleting alcohol from your life. We'll help you break down and rebuild new habits and a new mindset that gives you complete control, that will last way past finishing your challenge.",
            'second' => 'HEAR FROM MORE OF OUR MEMBERS',
            'third' => "Almost no-one went into this challenge thinking they were giving up forever. Most went in thinking 28, 90 or even 356 days. But 87% of our members choose to carry on alcohol-free after 90 days.\nThat's incredible... and that's because they've had a life-changing mindset shift.",
            'fourth' => 'Why?...because they\'ve had a life-changing mindset shift.',
            'click' => 'CLICK ON ANY OF THE IMAGES BELOW TO SEE HOW THEIR LIVES HAVE CHANGED',
            'links' => [
                ['img' => 'https://images.clickfunnels.com/39/858400252111e8942d09883496b79a/Screen-Shot-2017-12-09-at-01.41.24-min.png', 'url' => 'https://www.youtube.com/watch?v=ItRN0NdMd-I'],
                ['img' => 'https://images.clickfunnels.com/4f/89cea0252111e8942d09883496b79a/Screen-Shot-2017-12-09-at-02.00.58-min.png', 'url' => 'https://www.youtube.com/watch?v=ZYy1_zs-i_Y'],
                ['img' => 'https://images.clickfunnels.com/7b/176780252111e8ae74e9f66eb47ac1/Screen-Shot-2017-12-09-at-02.02.18-min.png', 'url' => 'https://www.youtube.com/watch?v=3WSYseQKPVE'],
                ['img' => 'https://images.clickfunnels.com/7b/176780252111e8ae74e9f66eb47ac1/Screen-Shot-2017-12-09-at-02.02.18-min.png', 'url' => 'https://www.youtube.com/watch?v=3WSYseQKPVE'],
            ]
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock15(LandingPage $landingPage)
    {
        $data = [
            'first' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL.',
            'second' => 'GET 25% OFF THIS FEBRUARY.',
            'third' => 'Use Promo Code "DRYFEB" To Apply The Discount'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }


    protected function createBlock16(LandingPage $landingPage)
    {
        $data = [
            'first' => '* MOST POPULAR. *',
            'button' => 'JOIN THE CHALLENGE!',
            'images' => [
                ['link' => 'https://images.clickfunnels.com/0b/6e599015a111e9a05b05f6edac57e3/OYNB-Pricing-Graphic---January-min.png'],
                ['link' => 'https://images.clickfunnels.com/4e/90ef3015a111e98e8b1f91285d953b/OYNB-Pricing-Graphic-90---January-min.png', 'most' => true],
                ['link' => 'https://images.clickfunnels.com/88/bce6f015a111e98e8b1f91285d953b/OYNB-Pricing-Graphic-365---January-min.png'],
            ],
            'footer' => 'Use Discount Code "DRYFEB" At Checkout!'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock17(LandingPage $landingPage)
    {
        $data = [
            'title' => 'SEE HOW OYNB CHANGED THESE PEOPLES LIVES',
            'second' => 'BECOME THE BEST VERSION OF YOURSELF',
            'text' => 'Imagine the thought of looking in the mirror and feeling inflated, being able to not have to use drink as the crutch to get through stressful days. Or having to use drink to feel more confident at social events. Even dropping the extra pounds and feeling more confident in your own skin. When your re-wire the relationship you have with drink it becomes natural rather than a challenge.',
            'videos' => [
                'https://www.youtube.com/embed/TONrqMDljy0',
                'https://www.youtube.com/embed/TwyyTB8P5vw',
            ],
            'list' => [
                ['title' => 'Improved Sleep', 'text' => 'Proper, restful sleep that sees you spring out of bed in the morning, ready to tackle whatever the day has to throw at you like Rocky on Speed. Imagine that!'],
                ['title' => 'Reduced Anxiety', 'text' => 'Alcohol is a proven depressant and only covers up the effects of anxiety… a crutch that’s gone in the morning. The result, as your body dedicates its resources to processing the toxins you’ve poured into it is – you guessed it – even more anxiety. Break the cycle and thrive with real confidence.'],
                ['title' => 'Look Better Than EVER!', 'text' => 'Your skin is one of the most obvious places the effects of toxins show. In many cases your complexion improves, dry skin becomes much more manageable and people will have no choice but to compliment your new shimmer and shine.'],
                ['title' => 'Boosted Productivity And Motivation', 'text' => 'When you aren’t lying around with a hangover or feeling hazy from booze, you have the energy to get more done. Start that business, write that book. Smash those goals you’ve left on the back-burner.'],
                ['title' => 'More Money In The Bank! ', 'text' => 'Naturally, the money you’d otherwise be spending on booze ends up staying in your bank account. This could be enough to pay for a holiday for the entire family every year, or put the worries of a rainy day firmly to bed!']
            ]
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock18(LandingPage $landingPage)
    {
        $data = [
            'title' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL THIS FEBRUARY',
            'second' => 'TAKE THE FIRST STEPS NOW',
            'third' => 'Become the most productive, present and healthiest version of yourself just by making one change',
            'button' => 'YES! I WANT TO TAKE THE CHALLENGE!',
            'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock19(LandingPage $landingPage)
    {
        $data = [
            'first' => '25% OFFER IS AVAILABLE ALL OF FEBRUARY',
            'second' => 'JUST USE THE CODE \'DRYFEB\' AT CHECKOUT!'
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createBlock20(LandingPage $landingPage)
    {
        $data = [
            'first' => 'COPYRIGHT © 2019 | ONE YEAR NO BEER | ALL RIGHTS RESERVED.',
            'links' => [
                ['text' => 'PRIVACY POLICY', 'url' => 'http://thesuccessvaults.com/PRIVACY-POLICY/'],
                ['text' => 'TERMS AND CONDITIONS', 'url' => 'http://thesuccessvaults.com/termsandconditions/'],
            ]
        ];

        $block = new Block();
        $block->setPosition(self::$position++)
            ->setData($data)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

//
//    /**
//     * @Route("/create-landing")
//     */
//    public function createLandingAction()
//    {
////        $templates = $this->em->getRepository(Template::class)->findAll();
////
////        $data = [
////              [
////                  'title' => '25% OFF ALL OYNB CHALLENGES THIS FEBRUARY',
////                  'text' => 'DISCOUNT APPLIED UPON CHECKOUT WHEN YOU USE THE PROMO CODE "DRYFEB".'
////              ],
////            [
////                'title' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL.',
////                'subtitle' => 'GET 25% OFF THIS FEBRUARY.',
////                'text' => 'Use Promo Code "DRYFEB" To Apply The Discount'
////            ],
////            [
////                'title' => '25% OFFER IS AVAILABLE ALL OF FEBRUARY',
////                'text' => 'JUST USE THE CODE \'DRYFEB\' AT CHECKOUT!'
////            ]
////        ];
////
////        $data = [
////            [
////                'text' => 'WHY DON\'T YOU JUST STOP DRINKING?'
////            ],
////            [
////                'text' => 'WHAT DOES THE PROGRAMME INVOLVE?'
////            ]
////        ];
//
//        $landingPage = $this->createEmptyLandingPage();
//        $this->createRedLines($landingPage);
//        $this->createYellowLines($landingPage);
//        $this->createListBlock($landingPage);
//        $this->createChangeBlock($landingPage);
//        $this->createJourneyBlock($landingPage);
//        $this->createVideoBlock($landingPage);
//        $this->createMenuLine($landingPage);
//        $this->createBanner($landingPage);
//        $this->createFeaturedLine($landingPage);
//        $this->createPillLine($landingPage);
//        $this->createPhoneLine($landingPage);
////        $template = $templates[0];
//
//
//        $this->em->flush();
//
//
//        dump($landingPage);
//        die;
//    }

    protected function createRedLines(LandingPage $landingPage)
    {
        $data = [
            [
                'title' => '25% OFF ALL OYNB CHALLENGES THIS FEBRUARY',
                'text' => 'DISCOUNT APPLIED UPON CHECKOUT WHEN YOU USE THE PROMO CODE "DRYFEB".'
            ],
            [
                'title' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL.',
                'subtitle' => 'GET 25% OFF THIS FEBRUARY.',
                'text' => 'Use Promo Code "DRYFEB" To Apply The Discount'
            ],
            [
                'title' => '25% OFFER IS AVAILABLE ALL OF FEBRUARY',
                'text' => 'JUST USE THE CODE \'DRYFEB\' AT CHECKOUT!'
            ]
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'red_line']);
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }

    protected function createYellowLines(LandingPage $landingPage)
    {
        $data = [
            [
                'text' => 'WHY DON\'T YOU JUST STOP DRINKING?'
            ],
            [
                'text' => 'WHAT DOES THE PROGRAMME INVOLVE?'
            ]
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'yellow_line']);
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }

    protected function createListBlock(LandingPage $landingPage)
    {
        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'list_block']);
        $data = [
            [
                'title' => 'BECOME THE BEST VERSION OF YOURSELF',
                'text' => 'Imagine the thought of looking in the mirror and feeling inflated, being able to not have to use drink as the crutch to get through stressful days. Or having to use drink to feel more confident at social events. Even dropping the extra pounds and feeling more confident in your own skin. When your re-wire the relationship you have with drink it becomes natural rather than a challenge.',
                'header' => 'SEE HOW OYNB CHANGED THESE PEOPLES LIVES',
                'list' => [
                    [
                        'list_title' => 'Improved Sleep',
                        'list_text' => 'Proper, restful sleep that sees you spring out of bed in the morning, ready to tackle whatever the day has to throw at you like Rocky on Speed. Imagine that!'
                    ],
                    [
                        'list_title' => 'Reduced Anxiety',
                        'list_text' => 'Alcohol is a proven depressant and only covers up the effects of anxiety… a crutch that’s gone in the morning. The result, as your body dedicates its resources to processing the toxins you’ve poured into it is – you guessed it – even more anxiety. Break the cycle and thrive with real confidence.'
                    ],
                    [
                        'list_title' => 'Look Better Than EVER!',
                        'list_text' => 'Your skin is one of the most obvious places the effects of toxins show. In many cases your complexion improves, dry skin becomes much more manageable and people will have no choice but to compliment your new shimmer and shine.',
                    ],
                    [
                        'list_title' => 'Boosted Productivity And Motivation',
                        'list_text' => 'When you aren’t lying around with a hangover or feeling hazy from booze, you have the energy to get more done. Start that business, write that book. Smash those goals you’ve left on the back-burner.',
                    ],
                    [
                        'list_title' => 'More Money In The Bank!',
                        'list_text' => 'Naturally, the money you’d otherwise be spending on booze ends up staying in your bank account. This could be enough to pay for a holiday for the entire family every year, or put the worries of a rainy day firmly to bed!',
                    ]
                ],
                'youtube' => [
                    [
                        'youtube_link' => 'https://youtube.com/embed/TONrqMDljy0',
                    ],
                    [
                        'youtube_link' => 'https://youtube.com/embed/TwyyTB8P5vw',
                    ]
                ]
            ]
        ];
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }

    protected function createChangeBlock(LandingPage $landingPage)
    {
        $data = [
            [
                'title' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL THIS FEBRUARY',
                'subtitle' => 'TAKE THE FIRST STEPS NOW',
                'text' => 'Become the most productive, present and healthiest version of yourself just by making one change',
                'button' => 'YES! I WANT TO TAKE THE CHALLENGE!',
                'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.'
            ]
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'change_block']);
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }

    protected function createVideoBlock(LandingPage $landingPage)
    {
        $data = [
            [
                'link' => 'https://fast.wistia.net/embed/iframe/ph0dmnwjqa'
            ]
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'video_block']);
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }



    protected function createMenuLine(LandingPage $landingPage)
    {
        $data = [
            [
                'logo' => 'https://images.clickfunnels.com/f5/d0f3c0755211e8a3cd79b79af6e943/OYNB---Dark---Landscape---RGB---High-Res.png',
                'button' => 'JOIN THE CHALLANGE!',
                'links' => [
                    'first' => 'ABOUT THE PROGRAM',
                    'second' => 'WHAT\'S INCLUDED?'
                ]
            ]
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'menu_line']);
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }

    protected function createBanner(LandingPage $landingPage)
    {
        $data = [
            'img' => 'https://images.clickfunnels.com/64/19217015a011e9993c3b6211d60693/OYNB---January-Sales-Page-Header-min.jpg',
            'title' => 'CHANGE YOUR RELATIONSHIP WITH ALCOHOL AND WATCH YOUR WHOLE WORLD CHANGE',
            'yellow' => 'Discover Why One Year No Beer Is The Leading Habit Changing Programme With 96% Of Members Transforming Their Relationship With Alcohol',
            'text' => 'Become the most productive, present and healthiest version of yourself just by making one change',
            'button' => 'YES! I WANT TO TAKE THE CHALLENGE!',
            'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.'
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'banner']);
        $block = new Block();
        $block->setTemplate($template->getName())
            ->setPosition(0)
            ->setData($data)
            ->setTemplate($template)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createFeaturedLine(LandingPage $landingPage)
    {
        $data = [
            'img' => 'https://images.clickfunnels.com/d1/f8715021fe11e889771f04f4372fd5/Screen-Shot-2018-03-07-at-11.57.22-min.png',
            'text' => 'AS FEATURED IN'
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'featured_line']);
        $block = new Block();
        $block->setTemplate($template->getName())
            ->setPosition(0)
            ->setData($data)
            ->setTemplate($template)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createPillLine(LandingPage $landingPage)
    {
        $data = [


                    'img' => 'https://images.clickfunnels.com/c9/8e7640252311e889b2956bb734ee9d/oynb---stats-min.png',
                    'first' => 'In 2015, Professor Kevin Moore of the Royal Free Hospital, London, co-authored one of the largest ever studies into the effects of a four-week break from alcohol.',
                    'bold' => 'The participants were average drinkers and the results were staggering.',
                    'second' => 'By the end of the four weeks, the participants of the studies had each lost on average, 40% of their liver fat and 3kg in weight. They had also reduced their cholesterol, lowered glucose levels as well as many other health improvements.
',
                    'third' => 'Moore was so impressed with the findings he suggested that if there were a pill that produced similar results, everyone would want it!
',
                    'footer' => 'THIS PROGRAMME IS THAT PILL'


        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'pill_line']);
        $block = new Block();
        $block->setTemplate($template->getName())
            ->setPosition(0)
            ->setData($data)
            ->setTemplate($template)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }

    protected function createJourneyBlock(LandingPage $landingPage)
    {
        $data = [
            [
                'first' => 'Society has conditioned you that you need alcohol to be successful, to be cool, to be sexy, to have fun, to relax, the list goes on and on. How will you fare going up against years of social conditioning, second-hand peer pressure and self-doubt, all on your own?',
                'yellow' => 'The key difference between our challenges and just "stopping drinking" is that we teach you to have a mindset shift. We help break down the habits associated with drinking, the same habits that can help change multiple areas of your life.',
                'second' => 'Of course, lots of people stop drinking on their own, like people find their six pack all by themselves. For the rest of us, we go and get a plan, a strategy, from someone who has done it before. Which takes us LEAPS and BOUNDS ahead of where we would be if we just tried to go it alone.',
                'journey' => 'This journey is no different.',
                'button' => 'YES! I WANT TO TAKE THE CHALLANGE!',
                'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.'
            ]
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'journey_block']);
        $position = 0;
        foreach ($data as $item) {
            $block = new Block();
            $block->setTemplate($template->getName())
                ->setPosition($position++)
                ->setData($item)
                ->setTemplate($template)
                ->setStatus(Block::STATUS_VISIBLE);
            $this->em->persist($block);
            $landingPage->addBlock($block);
        }
    }

    protected function createPhoneLine(LandingPage $landingPage)
    {
        $data = [
            'text' => 'We show you how to finally take total control over your alcohol habits. Alcohol does not help you sleep, relax or have a good time. People who think this merely associate alcohol with that. Not only do we help you break your habits and associations with alcohol, we\'ll also help you understand alcohol. You do not have to give up alcohol forever. Just master pure self control.',
            'first_title' => 'DAILY SUPPORT & ACCOUNTABILITY',
            'first_text' => 'Our daily accountability posts bring the latest science and habit change, Every day we smash those old negative habits and build healthy new ones, with lots of support along the way!',
            'second_title' => 'SUPPORTIVE COMMUNITY',
            'second_text' => 'Science shows us that if you want to really break a habit, you need to join a tribe, already living how you want to live, in order to inspire you... So, we\'ve built just that! Over 26,000 members in 120 countries. -Everyone is welcome at OYNB.',
            'third_title' => 'UNIQUE STEP BY STEP SYSTEM',
                'third_text' => 'Our unique step by step process will walk you through daily habit changes. This isn\'t about just giving up alcohol. The process is around breaking and building habits that arn\'t just a quick fix but last forever.',
           'fourth_title' => 'DOWNLOADS AT THE TIPS OF YOUR FINGERS',
           'fourth_text' => 'Discover everything from mindfulness to nutrition, overcoming addictions to getting the best sleep of your life, even tips to stay alcohol-free on nights out without anybody knowing (stealth drinking).',
            'fifth_title' => 'GUEST TRAININGS AND Q&A\'s',
            'fifth_text' => 'The best experts in fitness, diet & exercise delivering regular training and webinars. Going alcohol-free is just the beginning of an adventure that will lead you to the best version of you.',
'title' => 'SOCIAL CONDITIONING',
            'sixth_title' => 'SOCIAL CONDITIONING',
            'sixth_text' => 'Why is alcohol the only drug that when you stop using it, people berate and abuse you? Crazy eh? OYNB creates the PERFECT excuse for you to get past it. "Sorry mate, not tonight, I\'m doing a 28, 90 or 365-day alcohol-free challenge!"',
            'image' => 'https://images.clickfunnels.com/a3/a9d1803d7a11e88c8aeb45ce2f2fdc/oynb---mockup-_2_.png',
            'footer' => 'Use Discount Code "DRYFEB" Anytime This February To Get 25% Off Any OYNB Challenge.',
            'button' => 'YES! I WANT TO TAKE THE CHALLENGE!'
        ];

        $template = $this->em->getRepository(Template::class)->findOneBy(['name' => 'phone_line']);
        $block = new Block();
        $block->setTemplate($template->getName())
            ->setPosition(0)
            ->setData($data)
            ->setTemplate($template)
            ->setStatus(Block::STATUS_VISIBLE);
        $this->em->persist($block);
        $landingPage->addBlock($block);
    }


    protected function createEmptyLandingPage()
    {
        $landingPage = new LandingPage();
        $landingPage->setTitle('Test Title')->setSlug('')
            ->setStatus(LandingPage::STATUS_DRAFT);
        $this->em->persist($landingPage);
        return $landingPage;
    }
}