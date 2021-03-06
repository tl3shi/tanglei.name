<?php

class ThemeController extends ivController
{
	/**
	 * Default action
	 *
	 */
	function indexAction()
	{
		$crumbs = &ivPool::get('breadCrumbs');
		$crumbs->push('themes', 'index.php?c=theme');
		$selectedTheme = $this->conf->get('/config/imagevue/settings/theme');
		$themes = array_diff(ivTheme::getAllThemes(), array($selectedTheme));
		sort($themes);
		array_unshift($themes, $selectedTheme);
		$this->view->assign('theme', $selectedTheme);
		$this->view->assign('themes', $themes);
	}

	/**
	 * Edit theme cascade stylesheet
	 *
	 */
	function editcssAction()
	{
		$crumbs = &ivPool::get('breadCrumbs');
		$crumbs->push('themes', 'index.php?c=theme');
		$themeName = $this->_getParam('name', 'default', 'alnum');
		$crumbs->push($themeName . ' stylesheet', 'index.php?c=theme&amp;a=editcss&amp;name=' . $themeName);
		$theme = ivTheme::get($themeName);
		if (!$theme) {
			ivMessenger::add('error', "主题 '$themeName' 未找到");
			$this->_redirect('?c=theme');
		}
		$this->view->assign('theme', $theme);
		$this->view->assign('themes', ivTheme::getAllThemes());
	}

	/**
	 * Edit theme configuration
	 *
	 */
	function editconfigAction()
	{
		$crumbs = &ivPool::get('breadCrumbs');
		$crumbs->push('themes', 'index.php?c=theme');
		$themeName = $this->_getParam('name', 'default', 'alnum');
		$crumbs->push($themeName . ' config', 'index.php?c=theme&amp;a=editconfig&amp;name=' . $themeName);

		$theme = ivTheme::get($themeName);
		if (!$theme) {
			ivMessenger::add('error', "主题 '$themeName' 未找到");
			$this->_redirect('?c=theme');
		}
		$xml = $theme->getConfig();

		$this->view->assign('flatConfig', $xml->toFlatTree());
		$this->view->assign('themes', ivTheme::getAllThemes());
		$this->view->assign('themeName', $themeName);

		if (isset($_COOKIE['ivconf'])) {
			$openedPanels = array_unique(array_explode_trim(',', $_COOKIE['ivconf']));
		} else {
			$openedPanels = array('config_imagevue_settings', 'config_imagevue_controls', 'config_imagevue_audioplayer', 'config_imagevue_image', 'config_imagevue_thumbnails', 'config_imagevue_menu', 'config_imagevue_misc', 'config_imagevue_modules', 'config_imagevue_style', 'config_imagevue_textpage');
			setcookie('ivconf', implode(',', $openedPanels), time() + 365 * 86400);
		}
		$this->view->assign('openedPanels', $openedPanels);
	}

	/**
	 * Set default theme
	 *
	 */
	function useAction()
	{
		$this->_redirect('index.php?c=theme');
	}
	
	/**
	 * Copy theme
	 *
	 */
	function copyAction()
	{
		$newTheme = (string) $this->_getParam('new');
		if (!ctype_alnum($newTheme)) {
			ivMessenger::add('error', '主题名称仅允许使用字母');
			$this->_redirect('index.php?c=theme');
		}
		ivMessenger::add('notice', "主题 $newTheme 创建失败");
		$this->_redirect('index.php?c=theme');
	}

	/**
	 * Delete theme
	 *
	 */
	function deleteAction()
	{
		$theme = $this->_getParam('name', null, 'alnum');
		ivMessenger::add('notice', "主题 $theme 删除失败");
		$this->_redirect('index.php?c=theme');
	}

	/**
	 * Upload background file
	 *
	 */
	function uploadAction()
	{
		$theme = $this->_getParam('name', null, 'alnum');
		if (!$theme) {
			$this->_redirect($_SERVER['HTTP_REFERER']);
		}
		$this->_setNoRender();
		if (!isset($_FILES['Filedata'])) {
			ivMessenger::add('error', '文件未找到');
			$this->_redirect($_SERVER['HTTP_REFERER']);
		}
		$imageData = $_FILES['Filedata'];
		if (!@getimagesize($imageData['tmp_name'])) {
			ivMessenger::add('error', '文件不兼容');
			$this->_redirect($_SERVER['HTTP_REFERER']);
		}
		ivMessenger::add('notice', "文件 {$imageData['name']} 上传失败");
		$this->_redirect($_SERVER['HTTP_REFERER']);
	}

}
?>