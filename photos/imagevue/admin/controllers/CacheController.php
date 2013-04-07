<?php

class CacheController extends ivController
{
	/**
	 * Clears cache
	 *
	 */
	function clearAction()
	{
		$path = ivPath::canonizeRelative($this->conf->get('/config/imagevue/settings/contentfolder'));
		if (!ivAcl::isAllowedPath($path)) {
			$path = ivAcl::getAllowedPath();
		}
		if ($this->_clearCache(ROOT_DIR . $path)) {
			ivMessenger::add('notice', '缓存已清除');
		}
		$this->_redirect('index.php');
	}
	
	function _clearCache($path)
	{
		$result = clearCache($path);

		$content = getContent($path);
		foreach ($content as $dir) {
			if (is_dir($path . ivPath::canonizeRelative($dir))) {
				$result &= $this->_clearCache($path . ivPath::canonizeRelative($dir));
			}
		}

		return $result;
	}
	
}
?>