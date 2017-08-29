<?php

namespace AppBundle\Service;

use Facebook\Facebook;
use Facebook\Helpers\FacebookRedirectLoginHelper;

class FacebookService
{
    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var FacebookRedirectLoginHelper
     */
    private $redirectLoginHelper;

    public function __construct($appId, $appSecret)
    {
        $this->facebook = new Facebook(array(
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.10'
        ));

        $this->secret = $appSecret;
        // Get redirect login helper
        $this->redirectLoginHelper = $this->facebook->getRedirectLoginHelper();
    }

    /**
     * @return Facebook
     */
    public function getFacebook(): Facebook
    {
        return $this->facebook;
    }

    /**
     * @return FacebookRedirectLoginHelper
     */
    public function getRedirectLoginHelper(): FacebookRedirectLoginHelper
    {
        return $this->redirectLoginHelper;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }
}