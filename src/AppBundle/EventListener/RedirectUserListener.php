<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectUserListener
{
    private $tokenStorage;
    private $router;

    public function __construct(TokenStorageInterface $t, RouterInterface $r)
    {
        $this->tokenStorage = $t;
        $this->router = $r;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->isUserLogged() && $event->isMasterRequest()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');
            if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)
                || $this->checkUriPathForError()
            ) {
                $response = new RedirectResponse($this->router->generate('homepage'));
                $event->setResponse($response);
            }
        }
    }

    private function checkUriPathForError()
    {
        $request = Request::createFromGlobals();
        $uri = $request->getRequestUri();
        $path = $request->getPathInfo();

        foreach ($this->getDeniedPrefixes() as $deniedPrefix) {
            if (preg_match('#' . $deniedPrefix . '.*#', $path)) {
                return true;
            }
        }

        return false;
//
//        if (preg_match('#/auth.*#', $path)) {
//            $event->setResponse(new RedirectResponse($this->router->generate($redirectLoggedUsersRoute)));
//        } elseif (preg_match('#' . $this->deniedPath . '#', $uri)) {
//            $role = $user->getMainRole();
//            $route = $this->userLinksHelper->getRedirectRouteNameForRole($role);
//            if (empty($route)) {
//                $route = $this->redirectPageRoute;
//            }
//
//            $event->setResponse(new RedirectResponse($this->router->generate($route)));
//        }
    }

    private function isUserLogged()
    {
        return $this->tokenStorage->getToken() &&
            $this->tokenStorage->getToken()->getUser() instanceof  User;

    }

    private function getDeniedPrefixes()
    {
        return [
            '/auth'
        ];
    }

    private function isAuthenticatedUserOnAnonymousPage($currentRoute)
    {
        return in_array(
            $currentRoute,
//            ['order_main', 'payment_finish']
        []
        );
    }
}