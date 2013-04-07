<?php

class CacheController extends ivController
{
	/**
	 * Clears cache
	 *
	 */
	function clearAction()
	{
		ivMessenger::add('notice', '缓存已清除');
		$this->_redirect('index.php');
	}

}
?>