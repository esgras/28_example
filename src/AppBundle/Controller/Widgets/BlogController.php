<?php

namespace AppBundle\Controller\Widgets;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Post;

class BlogController extends BaseController
{
    public function indexAction()
    {
        return $this->render('widgets/blog/index.html.twig', [
            'posts' => $this->em->getRepository(Post::class)->findAll()
        ]);
    }
}