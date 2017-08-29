<?php

namespace AppBundle\DataManager;

use AppBundle\Entity\User;
use AppBundle\Exception\DatabaseException;
use AppBundle\Exception\DataManagerException;
use AppBundle\Repository\UserRepository;
use Doctrine\DBAL\DBALException;

class UserManager extends AbstractManager
{
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * Get user by id
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $id
     * @return User
     * @throws DataManagerException
     */
    public function getUserById($id)
    {
        $user = $this->userRepository->find($id);
        if ($user instanceof User) {
            return $user;
        } else {
            throw new DataManagerException("Get user by id: user not found");
        }
    }

    /**
     * Save user into database
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param User $user
     * @throws DatabaseException
     */
    public function saveUser(User $user)
    {
        $this->_entityManager->persist($user);
        try {
            $this->_entityManager->flush();
        } catch (DBALException $exception) {
            throw new DatabaseException("Fail to save user: " . $exception);
        }
    }

    /**
     * Deactivate user
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param User $user
     * @throws DatabaseException
     */
    public function deactivateUser(User $user)
    {
        $user->setIsActive(false);
        $this->_entityManager->persist($user);
        try {
            $this->_entityManager->flush();
        } catch (DBALException $exception) {
            throw new DatabaseException("Fail to set user inactive: " . $exception);
        }
    }

    /**
     * Generate user from facebook
     *
     * @author Dansen Zhou <zds8978704@gmail.com>
     *
     * @param $fbUserProfile
     * @param $token
     * @return User
     * @throws DataManagerException
     */
    public function generateUserFromFacebook($fbUserProfile, $token): User
    {
        $log = "Try to generate user from facebook profile: ";
        if (empty($fbUserProfile)) {
            throw new DataManagerException($log, "Profile is empty");
        }

        if (!key_exists('first_name', $fbUserProfile)) {
            throw new DataManagerException($log, "First name is missing");
        }

        if (!key_exists('last_name', $fbUserProfile)) {
            throw new DataManagerException($log, "Last name is missing");
        }

        if (!key_exists('picture', $fbUserProfile)) {
            throw new DataManagerException($log, "Picture is missing");
        }

        if (!key_exists('email', $fbUserProfile)) {
            throw new DataManagerException($log, "Email is missing");
        }

        if (!key_exists('id', $fbUserProfile)) {
            throw new DataManagerException($log, "Facebook id is missing");
        }

        $user = new User();
        $user->setFacebookProvider();
        $user->setFirstName($fbUserProfile['first_name']);
        $user->setLastName($fbUserProfile['last_name']);
        $user->setPicture($fbUserProfile['picture']['url']);
        $user->setEmail($fbUserProfile['email']);
        $user->setOauthUid($fbUserProfile['id']);
        $user->setToken($token);

        return $user;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}