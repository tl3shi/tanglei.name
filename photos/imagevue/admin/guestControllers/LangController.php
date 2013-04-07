<?php

class LangController extends ivController
{
	/**
	 * Pre-dispatching
	 *
	 */
	function _preDispatch()
	{
		if (!ivAcl::isAdmin()) {
			$this->_forward('login', 'cred');
			if (ivAuth::getCurrentUserLogin()) {
				ivMessenger::add('error', "你没有权限进入此页面");
			}
			return;
		}
	}
	
	/**
	 * Default action (edit language)
	 *
	 */
	function indexAction()
	{
		$crumbs = &ivPool::get('breadCrumbs');
		$crumbs->push('Languages', 'index.php?c=lang');
		$lang = $this->_getParam('name', 'english');
		if (!preg_match('/^[\w\d\_]+$/', $lang)) {
			ivMessenger::add('error', '仅允许使用字母和‘_’字符在语言文件名中');
			$this->_redirect('index.php?c=lang');
		}
		$this->view->assign('lang', $lang);
		$crumbs->push($lang, 'index.php?c=lang&amp;name=' . $lang);

		$configFile = LANGS_DIR . $lang . '.xml';
		$xml = ivXml::readFromFile($configFile, DEFAULT_LANG_FILE);

		$this->view->assign('flatConfig', $xml->toFlatTree());
		$this->view->assign('langs', getAllLangs());
	}

	/**
	 * Set default language
	 *
	 */
	function useAction()
	{
		$this->_redirect('index.php?c=lang');
	}
	
}
?>