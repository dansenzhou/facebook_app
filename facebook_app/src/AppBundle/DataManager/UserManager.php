<?php

namespace AppBundle\DataManager;

use AppBundle\Entity\User;
use AppBundle\Exception\DatabaseException;
use AppBundle\Exception\DataManagerException;
use Doctrine\DBAL\DBALException;

class UserManager extends AbstractManager
{
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
        if (!empty($fbUserProfile)) {
            throw new DataManagerException($log, "Profile is empty");
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
}