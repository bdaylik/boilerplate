<?php

require_once(dirname(__FILE__) . '/BaseService.php');
require_once(dirname(__FILE__) . '/../model/exceptions/UserNotFoundException.php');

class LoginService extends BaseService
{
    /**
     * If the credentials are ok, user will be returned. Otherwise UserNotFoundException is thrown
     *
     * @param $username
     * @param $password
     * @throws UserNotFoundException
     */
    public function loginUser($username, $password)
    {
        $sha1Password = sha1($password);

        $user = null;
        try {
            $user = $this->em->getRepository('entities\User')->findOneBy(array('username' => $username, 'password' => $sha1Password));
        } catch (\Doctrine\ORM\NoResultException $nre) {
        }

        if (is_null($annotator)) {
            throw new UserNotFoundException();
        } else {
            $_SESSION['user'] = serialize($user);
        }
    }

    public function getLoggedInUser()
    {
        $user = isset($_SESSION['user']) ? unserialize($_SESSION['user']) : null;

        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function logoutUser()
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}
