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
				ivMessenger::add('error', "你没有权限访问该页面！");
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
		$crumbs->push('语言包管理', 'index.php?c=lang');
		$lang = $this->_getParam('name', 'english');
		if (!preg_match('/^[\w\d\_]+$/', $lang)) {
			ivMessenger::add('error', '语言包只能使用数字、字母和符号“_”等字符命名');
			$this->_redirect('index.php?c=lang');
		}
		$this->view->assign('lang', $lang);
		$crumbs->push($lang, 'index.php?c=lang&amp;name=' . $lang);
		$configFile = LANGS_DIR . $lang . '.xml';
		if (isset($_POST['save']) && isset($_POST['lang'])) {
			$xml = ivXml::readFromFile($configFile, DEFAULT_LANG_FILE);
			foreach ($_POST['lang'] as $path => $value) {
				$node = &$xml->findByXPath($path);
				if ($node) {
					$node->setValue((string) $value);
				}
			}
			$result = $xml->writeToFile();
			if ($result) {
				ivMessenger::add('notice', "语言文件 $lang.xml 保存成功");
			} else {
				ivMessenger::add('error', "语言文件 $lang.xml 保持失败");
			}
		}

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
		$lang = $this->_getParam('name', 'english');
		if (!preg_match('/^[\w\d\_]+$/', $lang)) {
			ivMessenger::add('error', '仅允许使用字母和"_"作为语言文件名称');
			$this->_redirect('index.php?c=lang');
		}
		if (!is_null($lang)) {
			$xml = ivXml::readFromFile(CONFIG_FILE, DEFAULT_CONFIG_FILE);
			$node = &$xml->findByXPath('/config/imagevue/settings/language');
			if ($node) {
				$node->setValue((string) $lang);
			}
			$result = $xml->writeToFile();
			if ($result) {
				ivMessenger::add('notice', '配置文件保存成功');
			} else {
				ivMessenger::add('error', "配置文件保存失败 " . substr(CONFIG_FILE, strlen(BASE_DIR)));
			}
		}
		$this->_redirect('index.php?c=lang&name=' . $lang);
	}
	
}
?>