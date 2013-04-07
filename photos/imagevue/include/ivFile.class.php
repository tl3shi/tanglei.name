<?php
/**
 * File class
 *
 * @author McArrow
 */
class ivFile extends ivFSItem
{
	/**
	 * Properties
	 * @var array
	 */
	var $_properties = array(
		'name' => null,
		'path' => null,
		'date' => null,
		'size' => null
	);
	
	/**
	 * Attributes
	 * @var array
	 */
	var $_attributes = array(
		'title' => null,
		'description' => null
	);
	
	/**
	 * Constructor
	 *
	 * @param  string $path
	 * @access protected
	 */
	function __construct($path)
	{
		parent::__construct($path);
		$this->_path = $path;
		$this->_xml = &ivXml::readFromFile(dirname($this->_path) . '/folderdata.xml');

		$this->_initProperties();
		$this->_initAttributes();
		$this->_initUserAttributes();
	}

	/**
	 * Initialize properties
	 *
	 * @abstract 
	 * @access protected
	 */
	function _initProperties()
	{
		$this->_setProperty('name', ivFilepath::basename($this->_path));
		$this->_setProperty('path', $this->_getRelativePath());
		$this->_setProperty('date', filectime($this->_path));
		$this->_setProperty('size', filesize($this->_path));
	}

	/**
	 * Initializes user attributes
	 *
	 * @access protected
	 */
	function _initUserAttributes()
	{
		$conf = &ivPool::get('conf');
		foreach($conf->get('/config/imagevue/settings/attributes/image') as $name) {
			$this->_userAttributes[$name] = null;
		}
		
		// Read saved attributes
		$fileNode = &$this->_xml->findByXPath('/folder/file[name=' . $this->getProperty('name') . ']');
		if ($fileNode) {
			foreach ($fileNode->getAttributes() as $name => $value) {
				$this->setUserAttribute($name, $value);
			}
		}
	}
	
	/**
	 * Checks if path is file
	 *
	 * @static
	 * @param  string $path
	 * @return boolean
	 */
	function isSupported($path)
	{
		return is_file($path);
	}
	
	/**
	 * Return siblings of this
	 *
	 * @return array
	 */
	function getSiblings()
	{
		$parentFolder = $this->getAncestor();
		return $parentFolder->getSiblings($this);
	}
	
	/**
	 * Save file data
	 *
	 * @return boolean
	 */
	function save()
	{
		$fileNode = &$this->_xml->findByXPath('/folder/file[name=' . $this->getProperty('name') . ']');
		if (!$fileNode) {
			$fileNode = &ivXmlNode::create('file', array('name' => $this->getProperty('name')));
			$folderNode = &$this->_xml->findByXPath('/folder');
			if (!$folderNode) {
				$folderNode = &ivXmlNode::create('folder');
				$this->_xml->setNodeTree($folderNode);
			}
			$folderNode->addChild($fileNode);
		}
		$attributes = array_merge($this->getUserAttributes(), $this->getAttributes());
		$fileNode->setAttributes($attributes);

		$result = $this->_xml->writeToFile();
		if ($result) {
			$this->_setState(STATE_CLEAN);
		}
		return $result;
	}

	/**
	 * Initialize attributes
	 *
	 * @access protected
	 */
	function _initAttributes()
	{
		// Read saved attributes
		$fileNode = &$this->_xml->findByXPath('/folder/file[name=' . $this->getProperty('name') . ']');
		if ($fileNode) {
			foreach ($fileNode->getAttributes() as $name => $value) {
				$this->setAttribute($name, $value);
			}
		}
	}
	
	/**
	 * Return path to thumbnail
	 *
	 * @param  boolean $force
	 * @return string
	 */
	function getThumb($force = false)
	{
		$conf = &ivPool::get('conf');
		$thumbPath = $this->_getThumbPath();
		if (!is_file($thumbPath) && $force) {
			$this->generateThumb();
		}
		return is_file($thumbPath) ? $thumbPath : parent::getThumb();
	}

	/**
	 * Return thumbnail path
	 *
	 * @access protected
	 * @return string
	 */
	function _getThumbPath()
	{
		return dirname($this->_path) . DIRECTORY_SEPARATOR . $this->_thumbnailPrefix . ivFilepath::filename($this->_path) . '.jpg';
	}
	
	/**
	 * Generate thumb
	 *
	 */
	function generateThumb()
	{
		$conf = &ivPool::get('conf');
		$thumbPath = $this->_getThumbPath();
		$defaultThumbPath = BASE_DIR . 'images/thumbs/' . strtolower(ivFilepath::suffix($this->_path)) . '.jpg';
		if (!is_file($defaultThumbPath)) {
			$defaultThumbPath = parent::getThumb();
		}
		@copy($defaultThumbPath, $thumbPath);
	}
	
	/**
	 * Returns self XML-node
	 *
	 * @return ivXmlNode
	 */
	function &asXml()
	{
		$conf = ivPool::get('conf');
		$attributes = array_merge(
			array_clean($this->getUserAttributes()),
			array_clean($this->getAttributes()),
			$this->getProperties()
		);
		if (!isset($attributes['title']) && $conf->get('/config/imagevue/settings/autoTitling')) {
			$attributes['title'] = $this->getTitle();
		}
		$attributes['date'] = formatDate($this->getProperty('date'));
		$node = &ivXmlNode::create('file', $attributes); 
		return $node;
	}

	/**
	 * Deletes file, it's thumbnail and folderdata record
	 * 
	 * @return boolean
	 */
	function delete()
	{
		$conf = &ivPool::get('conf');
		@unlink($this->_getThumbPath());

		$fileNode = &$this->_xml->findByXPath('/folder/file[name=' . $this->getProperty('name') . ']');
		if ($fileNode) {
			$this->_setState(STATE_CLEAN);
			$this->_xml->remove($fileNode);
			$this->_xml->writeToFile();
		}

		return @unlink($this->_path);
	}

	/**
	 * Copies file, it's thumbnail and folderdata record to given folder
	 *
	 * @param  string  $destPath
	 * @return boolean
	 */
	function copyTo($destPath)
	{
		$conf = &ivPool::get('conf');
		$mode = octdec($conf->get('/config/imagevue/settings/chmod'));

		$result = @copy($this->_path, $destPath . $this->getProperty('name'));
		iv_chmod($destPath . $this->getProperty('name'), $mode);

		$newFile = ivFSItem::create($destPath . $this->getProperty('name'));

		@copy($this->_getThumbPath(), $newFile->_getThumbPath());
		iv_chmod($newFile->_getThumbPath(), $mode);

		foreach ($this->getAttributes() as $name => $value) {
			$newFile->setAttribute($name, $value);
		}
		foreach ($this->getUserAttributes() as $name => $value) {
			$newFile->setUserAttribute($name, $value);
		}
		$newFile->save();

		return $result;
	}
	
	/**
	 * Checks if current file can be shown on front-end
	 *
	 * @return boolean
	 */
	function isVisibleOnFrontEnd()
	{
		$conf = &ivPool::get('conf');
		return ivFilepath::matchSuffix($this->getProperty('name'), $conf->get('/config/imagevue/settings/includefilesext'));
	}

}
?>