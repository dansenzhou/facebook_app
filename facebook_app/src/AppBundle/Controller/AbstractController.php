<?php

namespace AppBundle\Controller;

use AppBundle\DataManager\UserManager;
use AppBundle\Entity\User;
use AppBundle\Exception\ControllerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

abstract class AbstractController extends Controller
{
    /**
     * Get current user in sesstion
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return User
     * @throws ControllerException
     */
    protected function _getUser()
    {
        $log = "Get user from token storage: ";
        $token = $this->getTokenStorage()->getToken();
        if (null === $token)
            throw new ControllerException($log . "token is null");

        $user = $token->getUser();
        if ($user instanceof User)
            return $user;
        throw new ControllerException($log . "not found");
    }

    /**
     * generate the absolute or relative url of $route
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $route
     * @param array $parameters
     * @param int $absolute
     * @return string
     */
    protected function _url($route, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->getRouter()->generate($route, $parameters, $absolute);
    }

    /**
     * redirect the page to $route, with $parameters
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param string $route
     * @param int $status_code
     * @param array $parameters
     * @param array $headers
     * @return RedirectResponse
     */
    public function _redirect($route, $status_code = 302, array $parameters = [], array $headers = [])
    {
        $url = $this->_url($route, $parameters);
        return new RedirectResponse($url, $status_code, $headers);
    }

    /**
     * respond to $twig with $parameters
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $twig
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function _respond($twig, array $parameters = [], Response $response = null)
    {
        return $this->getTemplating()->renderResponse($twig, $parameters, $response);
    }

    /**
     * Get templating
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return EngineInterface
     */
    protected function getTemplating()
    {
        $templating = $this->get('templating');
        if ($templating instanceof EngineInterface) {
            return $templating;
        }
        throw new NotFoundHttpException("Templating not found");
    }

    /**
     * Get router
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        $router = $this->get('router');
        if ($router instanceof RouterInterface) {
            return $router;
        }
        throw new NotFoundHttpException("Router not found");
    }

    /**
     * Get user manager
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return UserManager
     */
    public function getUserManager()
    {
        $userManager = $this->get('app.data_manager.user');
        if ($userManager instanceof UserManager) {
            return $userManager;
        }
        throw new NotFoundHttpException("User manager not found");
    }

    /**
     * Get token storages
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        $tokenStorage = $this->container->get('security.token_storage');
        if ($tokenStorage instanceof TokenStorage) {
            return $tokenStorage;
        }
        throw new NotFoundHttpException('service not found');
    }
}