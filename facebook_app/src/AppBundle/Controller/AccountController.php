<?php

namespace AppBundle\Controller;

use AppBundle\Constant\ExceptionCode;
use AppBundle\Exception\AppException;
use AppBundle\Exception\ControllerException;
use AppBundle\Service\FacebookService;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccountController extends AbstractController
{

    /**
     * Show login page
     *
     * @Route("/", name="login")
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return Response
     */
    public function loginWithFacebookAction()
    {
        $permissions = ['email'];
        $loginUrl = $this->getFacebookService()->getRedirectLoginHelper()->getLoginUrl($this->_url('facebook_redirect', [], UrlGeneratorInterface::ABSOLUTE_URL), $permissions);

        return $this->_respond('facebook_login', ['facebookUrl' => $loginUrl]);
    }

    /**
     * Handle facebook redirect
     *
     * @Route("/facebook_redirect", name="facebook_redirect")
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AppException
     * @throws ControllerException
     */
    public function facebookRedirectAction()
    {
        $log = "Handle facebook login: ";
        try {
            $accessToken = $this->getFacebookService()->getRedirectLoginHelper()->getAccessToken();
        } catch (FacebookResponseException $exception) {
            throw new ControllerException($log . $exception->getMessage());
        } catch (FacebookSDKException $exception) {
            throw new ControllerException($log . $exception->getMessage());
        }

        // check if access token has been fetched
        if (!isset($accessToken)) {
            if ($this->getFacebookService()->getRedirectLoginHelper()->getError()) {
                throw new ControllerException($log . $this->getFacebookService()->getRedirectLoginHelper()->getError());
            } else {
                throw new ControllerException($log . "Bad request", "Bad request", false, ExceptionCode::BAD_REQUEST);
            }
        }

        // Exchanges a short-lived access token for a long-lived one
        $oAuth2Client = $this->getFacebookService()->getFacebook()->getOAuth2Client();
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

        try {
            $response = $this->getFacebookService()->getFacebook()->get('/me?fields=name,first_name,last_name,email,picture', $longLivedAccessToken);
            $user = $this->getUserManager()->generateUserFromFacebook($response->getGraphNode()->asArray(), $longLivedAccessToken);
            $this->getUserManager()->saveUser($user);

            return $this->_redirect('dashboard', 302, ['oauthUid' => $user->getOauthUid()]);
        } catch (FacebookResponseException $exception) {
            throw new ControllerException($log . $exception->getMessage());
        } catch (FacebookSDKException $exception) {
            throw new ControllerException($log . $exception->getMessage());
        } catch (AppException $exception) {
            throw $exception;
        }
    }

    /**
     * Handle logout
     *
     * @Route("/logout", name="logout")
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $signedRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AppException
     * @throws ControllerException
     */
    public function logoutAction($signedRequest)
    {
        $result = $this->parseSignedRequest($signedRequest);
        if (empty($result) || !key_exists('user_id', $result)) {
            throw new ControllerException("Bad request");
        }

        try {
            $user = $this->getUserManager()->getUserByOauthUid($result['user_id']);
            $this->getUserManager()->deactivateUser($user);
        } catch (AppException $exception) {
            throw $exception;
        }
        return $this->_redirect('login');
    }

    protected function _respond($twig, array $parameters = [], Response $response = null)
    {
        return parent::_respond('AppBundle:account:' . $twig . '.html.twig', $parameters, $response);
    }

    /**
     * Get facebook service
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @return FacebookService
     * @throws ControllerException
     */
    private function getFacebookService()
    {
        $service = $this->get('app.service.facebook');
        if ($service instanceof FacebookService) {
            return $service;
        }
        throw new ControllerException("No facebook service found");
    }

    /**
     * Parse facebook signed request
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $signedRequest
     * @return mixed
     * @throws ControllerException
     */
    private function parseSignedRequest($signedRequest)
    {
        list($encoded_sig, $payload) = explode('.', $signedRequest, 2);

        $secret = $this->getFacebookService()->getSecret();

        // decode the data
        $sig = $this->base64UrlDecode($encoded_sig);
        $data = json_decode($this->base64UrlDecode($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            throw new ControllerException("Bad Signed JSON signature!");
        }

        return $data;
    }

    /**
     * Decode base 64 url
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $input
     * @return bool|string
     */
    private function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}