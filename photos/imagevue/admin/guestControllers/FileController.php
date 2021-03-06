<?php

class FileController extends ivController
{
	/**
	 * Path
	 * @var string
	 */
	var $path;

	/**
	 * Pre-dispatching
	 *
	 */
	function _preDispatch()
	{
		$path = ivPath::canonizeRelative($this->_getParam('path', $this->conf->get('/config/imagevue/settings/contentfolder'), 'path'), true);
		if (!ivAcl::isAllowedPath($path)) {
			$this->_redirect('index.php');
		}
		$fullPath = ROOT_DIR . $path;
		$include = $this->conf->get('/config/imagevue/settings/allowedext');
		if (is_dir($fullPath)) {
			$this->_redirect('index.php?path=' . urlencode($path));
		} elseif (!is_file($fullPath)) {
			$this->_redirect('index.php');
		} elseif (!in_array(strtolower(ivFilepath::suffix($fullPath)), $include)) {
			$this->_redirect('index.php?path=' . urlencode(ivFilepath::directory($path)));
		}
		$this->path = $path;
	}
	
	/**
	 * Default action
	 *
	 */
	function indexAction()
	{
		$contentFolder = ivFSItem::create(ROOT_DIR . ivAcl::getAllowedPath());
		$flatFolderTree = $contentFolder->getFlatFolderTree();

		$this->view->assign('path', $this->path);
		$this->placeholder->set('path', $this->path);
		$this->view->assign('flatFolderTree', $flatFolderTree);

		$file = ivFSItem::create(ROOT_DIR . $this->path);

		$newdata = $this->_getParam('newdata');
		if (is_string($this->_getParam('save')) && is_array($newdata)) {
			ivMessenger::add('notice', '文件数据保存失败');
		}
		
		$siblings = $file->getSiblings();

		$this->view->assign('file', $file);
		$this->view->assign('nextFile', $siblings['next']);
		$this->view->assign('prevFile', $siblings['previous']);
		$this->view->assign('current', $siblings['current']);
		$this->view->assign('count', $siblings['count']);
	}

	/**
	 * Copy file
	 *
	 */
	function copyAction()
	{
		ivMessenger::add('notice', 'File succesfully copied');
		$this->_redirect('index.php?path=' . urlencode($this->path));
	}
	
	/**
	 * Move file
	 *
	 */
	function moveAction()
	{
		ivMessenger::add('notice', '文件复制失败');
		$this->_redirect('index.php?path=' . urlencode($this->path));
	}
	
	/**
	 * Delete file
	 *
	 */
	function deleteAction()
	{
		ivMessenger::add('notice', '文件删除失败');
		$this->_redirect('index.php?path=' . urlencode(ivFilepath::directory($this->path)));
	}
	
	/**
	 * Rotate image
	 *
	 */
	function rotateAction()
	{
		ivMessenger::add('notice', '图片旋转失败');
		$this->_redirect('index.php?c=file&path=' . urlencode($this->path));
	}
	
	/**
	 * Post-dispatching
	 *
	 */
	function _postDispatch()
	{
		$crumbs = &ivPool::get('breadCrumbs');
		$brCrumbsKeys = array_explode_trim('/', $this->path);
		if ($brCrumbsKeys !== false) {
			$lastCrumbKey = end($brCrumbsKeys);
			$path = '';
			foreach ($brCrumbsKeys as $key) {
				if ($lastCrumbKey == $key) {
					$path .= $key;
					$file = ivFSItem::create(ROOT_DIR . $path);
					$crumbs->push($file->getTitle(), 'index.php?c=file&amp;path=' . urlencode($path));
				} else {
					$path .= $key . '/';
					$folder = ivFSItem::create(ROOT_DIR . $path);
					if (!$folder) {
						continue;
					}
					$crumbs->push($folder->getTitle(), 'index.php?path=' . urlencode($path), '', ($folder->isHidden() ? 'hidden' : ''));
				}
			}
		}
	}

}
?>