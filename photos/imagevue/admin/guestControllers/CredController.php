<?php

class CredController extends ivController
{
	/**
	 * Log in
	 *
	 */
	function loginAction()
	{
		$login = (string) $this->_getParam('login');
		$password = (string) $this->_getParam('password');
		if (!empty($login) && !empty($password)) {
			$rememberme = (boolean) $this->_getParam('rememberme');
			$result = ivAuth::authenticate($login, $password, $rememberme);
			if ($result) {
				ivMessenger::add('notice', "欢迎, $login");
			} else {
				ivMessenger::add('error', '不正确的用户名或密码');
			}
			$this->_redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?');
		}

		$guest = $userManager->getUser('guest');
		$this->view->assign('defaultGuest', ($guest && ('35675e68f4b5af7b995d9205ad0fc43842f16450' === $guest->passwordHash)));
	}

	/**
	 * Log out
	 *
	 */
	function logoutAction()
	{
		ivAuth::authenticate('', '', false);
		$this->_redirect($_SERVER['HTTP_REFERER']);
	}

}
?>