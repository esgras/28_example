<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class PostController extends BaseController
{
    public function indexAction($page, Request $request)
    {
        $posts = $this->em->getRepository(Post::class)->findBy([], ['id' => 'DESC']);
        $limit = 8;
        $posts = $this->get('knp_paginator')->paginate($posts, $page, $limit);

        return $this->render('post/blog_list.html.twig', [
            'posts' => $posts
        ]);
    }

    public function viewAction(Post $post)
    {
        return $this->render('post/blog-article.html.twig', [
            'post' => $post
        ]);
    }
}