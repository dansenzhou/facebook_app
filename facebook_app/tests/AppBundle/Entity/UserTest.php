<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Constant\OAuthProvider;
use AppBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testUuid()
    {
        $user = $this->getUser();
        $this->assertNotNull($user->getUuid());
    }

    public function testEmail()
    {
        $user = $this->getUser();
        $this->assertNull($user->getEmail());
        $expected = "test@abc.com";
        $user->setEmail($expected);
        $this->assertSame($expected, $user->getEmail());
        $this->assertSame($expected, $user->getUsername());
    }

    public function testFirstName()
    {
        $user = $this->getUser();
        $this->assertNull($user->getFirstName());
        $expected = "Max";
        $user->setFirstName($expected);
        $this->assertSame($expected, $user->getFirstName());
    }

    public function testLastName()
    {
        $user = $this->getUser();
        $this->assertNull($user->getLastName());
        $expected = "Musterman";
        $user->setLastName($expected);
        $this->assertSame($expected, $user->getLastName());
    }

    public function testOauthProvider()
    {
        $user = $this->getUser();
        $this->assertNull($user->getOauthProvider());
        $user->setFacebookProvider();
        $this->assertSame(OAuthProvider::FACEBOOK, $user->getOauthProvider());
    }

    public function testOauthUid()
    {
        $user = $this->getUser();
        $this->assertNull($user->getOauthUid());
        $oauthUid = "random";
        $user->setOauthUid($oauthUid);
        $this->assertSame($oauthUid, $user->getOauthUid());
    }

    public function testPicture()
    {
        $user = $this->getUser();
        $this->assertNull($user->getPicture());
        $expect = "/path/to/picture";
        $user->setPicture($expect);
        $this->assertSame($expect, $user->getPicture());
    }

    public function testToken()
    {
        $user = $this->getUser();
        $this->assertNull($user->getToken());
        $expect = "whatevertoken";
        $user->setToken($expect);
        $this->assertSame($expect, $user->getToken());
    }

    public function testIsActive()
    {
        $user = $this->getUser();
        $this->assertTrue($user->isActive());
        $user->setIsActive(false);
        $this->assertSame(false, $user->isActive());
    }

    public function testCreated()
    {
        $user = $this->getUser();
        $this->assertNull($user->getCreated());
        $expect = new \DateTime();
        $user->setCreated($expect);
        $this->assertSame($expect, $user->getCreated());
    }

    public function testUpdated()
    {
        $user = $this->getUser();
        $this->assertNull($user->getUpdated());
        $expect = new \DateTime();
        $user->setUpdated($expect);
        $this->assertSame($expect, $user->getUpdated());
    }

    public function testRoles()
    {
        $user = $this->getUser();
        $this->assertSame(["ROLE_USER"], $user->getRoles());
        $roles = ["ROLE_WHATEVER"];
        $user->setRoles($roles);
        $this->assertSame($roles, $user->getRoles());
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->getMockForAbstractClass('AppBundle\Entity\User');
    }

}