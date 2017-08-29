<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    /**
     * Show dashboard
     *
     * @Route("/dashboard/{oauthUid}", name="dashboard")
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return Response
     * @throws AppException
     */
    public function dashboardAction($oauthUid)
    {
        try {
            $user = $this->getUserManager()->getUserByOauthUid($oauthUid);
            return $this->_respond('dashboard', ['user' => $user]);
        } catch (AppException $exception) {
            throw $exception;
        }
    }

    protected function _respond($twig, array $parameters = [], Response $response = null)
    {
        return parent::_respond('AppBundle:dashboard:' . $twig . '.html.twig', $parameters, $response);
    }
}