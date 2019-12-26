<?php

namespace AppBundle\Handler;

use AppBundle\Entity\User;
use AppBundle\Helper\ConstantHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use AppBundle\Service\LoginErrorMessageAdapter;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class AuthenticationHandler
 * @package AppBundle\Handler
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Session
     */
    private $session;

    private $env;

    private $tokenStorage;

    private $em;

    /**
     * AuthenticationHandler constructor.
     * @param RouterInterface $router
     * @param Session $session
     */
    public function __construct(RouterInterface $router, SessionInterface $session, TokenStorageInterface $tokenStorage, EntityManagerInterface $em, $env)
    {
        $this->router = $router;
        $this->session = $session;
        $this->env = $env;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return JsonResponse|RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        $user = $token->getUser();

        if ($user->isSuperAdmin()) {
            $baseUrl = '/dashboard/';
            $url = $this->env == 'dev' ? '/app_dev.php' . $baseUrl : $baseUrl;
            return new RedirectResponse($url);
        }

        if ($this->session->has(ConstantHelper::SECURITY_REDIRECT_PAGE)) {
            $url = $this->session->get(ConstantHelper::SECURITY_REDIRECT_PAGE);
            $this->session->remove(ConstantHelper::SECURITY_REDIRECT_PAGE);

            return new RedirectResponse($url);
        }

//        return new RedirectResponse($this->router->generate('homepage'));
        return new RedirectResponse($this->router->generate('account_day_list'));
    }

    protected function disconnect()
    {
        $this->tokenStorage->setToken(null);
        $this->session->invalidate();
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof UsernameNotFoundException) {
            $exception = new UsernameNotFoundException("Пользователь \"{$exception->getUsername()}\" не найден.");
        }

        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse($this->router->generate('login'));
    }
}