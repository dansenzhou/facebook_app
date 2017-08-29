<?php

namespace Test\AppBundle\DataManager;

use AppBundle\DataManager\UserManager;
use AppBundle\Entity\User;
use AppBundle\Exception\AppException;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\ReferenceRepository;

class UserManagerTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $referenceRepository;

    /**
     * @var UserManager
     */
    private $userManager;

    public function setUp()
    {
        $fixtures = array(
            'AppBundle\DataFixtures\ORM\UserFixture',
        );
        $this->referenceRepository = $this->loadFixtures($fixtures)->getReferenceRepository();

        $this->userManager = $this->getContainer()->get('app.data_manager.user');
    }

    public function testGetUserById()
    {
        $id = $this->referenceRepository->getReference('user-1')->getId();
        $this->assertNotNull($id);
        try {
            $user = $this->userManager->getUserById($id);
            $this->assertTrue($user instanceof User);
        } catch (AppException $exception) {
            $this->fail("Exception should not throw");
        }
    }

    public function testGetUserByIdWithException()
    {
        try {
            $this->userManager->getUserById(100);
            $this->fail("User does not exist, should throw exception");
        } catch (AppException $exception) {
            $this->assertNotNull($exception);
        }
    }

    public function testSaveUser()
    {
        $id = $this->referenceRepository->getReference('user-1')->getId();
        $this->assertNotNull($id);
        try {
            $user = $this->userManager->getUserById($id);
            $this->assertTrue($user instanceof User);
            $expect = "new@test.com";
            $user->setEmail($expect);
            $this->userManager->saveUser($user);
            $updatedUser = $this->userManager->getUserById($id);
            $this->assertSame($expect, $updatedUser->getEmail());
        } catch (AppException $exception) {
            $this->fail("Exception should not throw");
        }
    }

    public function testDeactivateUser()
    {
        $id = $this->referenceRepository->getReference('user-1')->getId();
        $this->assertNotNull($id);
        try {
            $user = $this->userManager->getUserById($id);
            $this->assertTrue($user instanceof User);
            $this->userManager->deactivateUser($user);
            $updatedUser = $this->userManager->getUserById($id);
            $this->assertSame(false, $updatedUser->isActive());
        } catch (AppException $exception) {
            $this->fail("Exception should not throw");
        }
    }

    public function testGenerateUserFromFacebook()
    {
        try {
            $fbProfile = array(
                'first_name' => 'Max',
                'last_name' => 'Musterman',
                'picture' => ['url' => 'http://a.picture.com'],
                'email' => 'test@gmx.com',
                'id' => 'uuuu-id'
            );
            $user = $this->userManager->generateUserFromFacebook($fbProfile, "random");
            $this->assertTrue($user instanceof User);
        } catch (AppException $exception) {
            $this->fail("Exception should not throw");
        }
    }

    public function testGenerateUserFromFacebookEmptyProfile()
    {
        $fbProfile = null;
        try {
            $this->userManager->generateUserFromFacebook($fbProfile, "random");
            $this->fail("Facebook profile is empty, should throw exception");
        } catch (AppException $exception) {
            $this->assertNotNull($exception);
        }
    }
}