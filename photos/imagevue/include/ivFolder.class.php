<?php
/**
 * Folder class
 *
 * @author McArrow
 */
class ivFolder extends ivFSItem 
{
	/**
	 * Properties
	 * @var array
	 */
	var $_properties = array(
		'name' => null,
		'path' => null,
		'date' => null,
		'fileCount' => null,
		'totalFileCount' => null
	);
	
	/**
	 * Attributes
	 * @var array
	 */
	var $_attributes = array(
		'title' => null,
		'previewimage' => null,
		'description' => null,
		'pageContent' => null,
		'sort' => null,
		'hidden' => null,
		'page' => null,
		'fileMod' => null,
		'parameters' => null
	);

	var $_force = false;

	/**
	 * Constructor
	 *
	 * @param  string $path
	 * @access protected
	 */
	function __construct($path)
	{
		parent::__construct($path);
		$this->_path = ivPath::canonizeAbsolute($path);
		$this->_xml = &ivXml::readFromFile($this->_path . 'folderdata.xml');
		
		if (!file_exists($this->_path . 'folderdata.xml') || (filemtime($this->_path . '.') > filemtime($this->_path . 'folderdata.xml'))) {
			$this->_force = true;
			$this->getMaxThumbSize(true);
		}

		$conf = &ivPool::get('conf');
		$this->_userAttributes = array_fill_keys($conf->get('/config/imagevue/settings/attributes/folder'), null);

		$this->_initProperties();
		$this->_initAttributes();
		$this->_initUserAttributes();
	}

	/**
	 * Initialize properties
	 *
	 * @access protected
	 */
	function _initProperties()
	{
		$this->_setProperty('name', basename(rtrim($this->_path, DS)));
		$this->_setProperty('path', $this->_getRelativePath());
		$this->_setProperty('date', filectime($this->_path));
		$this->_initFileCounts();
	}
	
	/**
	 * Initialize file counts in current folder
	 *
	 * @access private
	 */
	function _initFileCounts()
	{
		$conf = &ivPool::get('conf');
		
		$fileCount = null;
		$totalFileCount = null;

		if (!$this->_force) {
			if ($conf->get('/config/imagevue/settings/useCache')) {
				$folderNode = &$this->_xml->findByXPath('/folder');
				if ($folderNode) {
					$fileCount = $folderNode->getAttribute('fileCount');
					$totalFileCount = $folderNode->getAttribute('totalFileCount');
				}
			}
		}
		
		if (is_null($fileCount) || is_null($totalFileCount)) {
			$excludefilesprefix = $conf->get('/config/imagevue/settings/excludefilesprefix');
			$includefilesext = $conf->get('/config/imagevue/settings/includefilesext');
			$allowedext = $conf->get('/config/imagevue/settings/allowedext');

			$content = getContent($this->_path);
			$fileCount = 0;
			$totalFileCount = 0;
			foreach ($content as $item) {
				if (!ivFilepath::matchPrefix($item, $excludefilesprefix)) {
					if (ivFilepath::matchSuffix($item, $allowedext)) {
						$totalFileCount++;
						if (ivFilepath::matchSuffix($item, $includefilesext)) {
							$fileCount++;
						}
					}
				}
			}

			if ($conf->get('/config/imagevue/settings/useCache')) {
				$folderNode = &$this->_xml->findByXPath('/folder');
				if (!$folderNode) {
					$folderNode = &ivXmlNode::create('folder');
					$this->_xml->setNodeTree($folderNode);
				}
				$folderNode->setAttribute('fileCount', $fileCount);
				$folderNode->setAttribute('totalFileCount', $totalFileCount);
				$this->_xml->writeToFile();
			}
		}
		
		$this->_setProperty('fileCount', $fileCount);
		$this->_setProperty('totalFileCount', $totalFileCount);
	}

	/**
	 * Initialize attributes
	 *
	 * @access protected
	 */
	function _initAttributes()
	{
		// Read saved attributes
		$folderNode = &$this->_xml->findByXPath('/folder');
		if ($folderNode) {
			foreach ($folderNode->getAttributes() as $name => $value) {
				$this->setAttribute($name, $value);
			}
		}

		if ($this->getProperty('fileCount') > 0) {
			if (!$this->getAttribute('previewimage')) {
				$conf = &ivPool::get('conf');
				foreach (getContent($this->_path) as $item) {
					if (!ivFilepath::matchPrefix($item, array($this->_thumbnailPrefix))
						&& ivFileImage::isSupported($this->_path . $item)) {
						$this->setAttribute('previewimage', $item);
						$this->_setState(STATE_DIRTY);
						break;
					}
				}
			}
		} elseif ($this->getAttribute('previewimage')) {
			$this->setAttribute('previewimage', '');
			$this->_setState(STATE_DIRTY);
		}
		
		if ('_altimage' == $this->getProperty('name')) {
			$this->setAttribute('hidden', 'true');
		}
	}
	
	/**
	 * Initialize user attributes
	 *
	 * @access protected
	 */
	function _initUserAttributes()
	{
		$conf = &ivPool::get('conf');
		foreach($conf->get('/config/imagevue/settings/attributes/folder') as $name) {
			$this->_userAttributes[$name] = null;
		}
		
		// Read saved attributes
		$folderNode = &$this->_xml->findByXPath('/folder');
		if ($folderNode) {
			foreach ($folderNode->getAttributes() as $name => $value) {
				$this->setUserAttribute($name, $value);
			}
		}
	}
	
	/**
	 * Checks if path is dir
	 *
	 * @static
	 * @param  string $path
	 * @return boolean
	 */
	function isSupported($path)
	{
		return is_dir($path);
	}

	/**
	 * Save folder data
	 *
	 * @return boolean
	 */
	function save()
	{
		$folderNode = &$this->_xml->findByXPath('/folder');
		if (!$folderNode) {
			$folderNode = &ivXmlNode::create('folder');
			$this->_xml->setNodeTree($folderNode);
		}
		$attributes = array_merge($this->getUserAttributes(), $this->getAttributes());
		$folderNode->setAttributes($attributes);

		$result = $this->_xml->writeToFile();
		if ($result) {
			$this->_setState(STATE_CLEAN);
		}
		return $result;
	}
	
	/**
	 * Return possible sort types
	 *
	 * @static
	 * @return array
	 */
	function getSortTypes()
	{
		return array(
			'auto' => array('name' => 'Auto'),
			'na' => array('name' => 'Name asc', 'paramName' => 'name', 'reverse' => false),
			'nd' => array('name' => 'Name desc', 'paramName' => 'name', 'reverse' => true),
			'da' => array('name' => 'Date asc', 'paramName' => 'date', 'reverse' => false),
			'dd' => array('name' => 'Date desc', 'paramName' => 'date', 'reverse' => true),
			'sa' => array('name' => 'Size asc', 'paramName' => 'size', 'reverse' => false),
			'sd' => array('name' => 'Size desc', 'paramName' => 'size', 'reverse' => true),
			'rnd' => array('name' => 'Random'),
		);
	}
	
	/**
	 * Return folder's files
	 *
	 * @param  string $sort
	 * @return array
	 */
	function getFiles($sort = null)
	{
		$conf = &ivPool::get('conf');
		$excludefilesprefix = $conf->get('/config/imagevue/settings/excludefilesprefix');
		$items = getContent($this->_path);
		$content = array();
		foreach ($items as $item) {
			if (is_file($this->_path . $item)
				&& (ivFilepath::matchSuffix($item, $conf->get('/config/imagevue/settings/allowedext')) && !ivFilepath::matchPrefix($item, $excludefilesprefix))) {
				$content[] = ivFSItem::create($this->_path . $item);
			}
		}
		if (empty($sort)) {
			$sort = $this->getAttribute('sort');
		}
		if ($sort && 'auto' !== $sort) {
			$this->_sort($content, $sort);
		} else {
			$this->_sort($content, $conf->get('/config/imagevue/settings/defaultSortFiles'));
		}
		return $content;
	}
	
	/**
	 * Return folder's folders
	 *
	 * @return array
	 */
	function getFolders()
	{
		$conf = &ivPool::get('conf');
		$items = getContent($this->_path);
		$content = array();
		foreach ($items as $item) {
			if (is_dir($this->_path . $item)) {
				$content[] = ivFSItem::create($this->_path . $item);
			}
		}
		$this->_sort($content, $conf->get('/config/imagevue/settings/defaultSortFolders'));
		return $content;
	}

	/**
	 * Sort content
	 *
	 * @param array  $content
	 * @param string $sort
	 */
	function _sort(&$content, $sort)
	{
		$sortTypes = ivFolder::getSortTypes();
		if (in_array($sort, array_keys($sortTypes))) {
			if ('rnd' == $sort) {
				usort($content, array('ivComparator', 'random'));
			} elseif ('auto' == $sort) {
				// Nothing to do here
			} else {
				$comparator = &new ivComparatorFileProperty($sortTypes[$sort]['paramName']);
				usort($content, array(&$comparator, 'compare'));
				if ($sortTypes[$sort]['reverse']) {
					$content = array_reverse($content);
				}
			}
		}

	}

	/**
	 * Return flat folder tree starts from current
	 *
	 * @param  boolean $withHidden
	 * @param  integer $depth
	 * @return array
	 */
	function getFlatFolderTree($withHidden = true, $depth = 0)
	{
		$result = array();
		$result[] = array('folder' => $this, 'depth' => $depth);
		foreach ($this->getFolders() as $childFolder) {
			if ($withHidden || !$childFolder->isHidden()) {
				foreach ($childFolder->getFlatFolderTree($withHidden, $depth + 1) as $childChild) {
					$result[] = $childChild;
				}
			}
		}
		return $result;
	}

	/**
	 * Return siblings of child
	 *
	 * @param  ivFSItem $child
	 * @return array
	 */
	function getSiblings(&$child)
	{
		$content = $this->getFiles();
		foreach ($content as $key => $item) {
			if ($child->getProperty('name') == $item->getProperty('name')) {
				$prev = $key - 1;
				$current = $key + 1;
				$next = $key + 1;
			}
		}
		if ($next >= count($content)) {
			$next = 0;
		}
		if ($prev < 0) {
			$prev = count($content) - 1;
		}
		$nextFile = $content[$next];
		$prevFile = $content[$prev];
		$count = count($content);
		return array(
			'next' => &$nextFile,
			'previous' => &$prevFile,
			'current' => $current,
			'count' => $count
		);
	}
	
	/**
	 * Return path to thumbnail
	 *
	 * @return string
	 */
	function getThumb()
	{
//		$previewImage = $this->getAttribute('previewimage');
//		if (!empty($previewImage)) {
//			$image = ivFSItem::create($this->_path . $previewImage);
//			$thumb = $image->getThumb();
//		}
		if (isset($thumb)) {
			return $thumb;
		} else {
			return BASE_DIR . ($this->isLink() ? 'images/folder1_128x128_link.png' : 'images/folder1_128x128.png');
		}
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
		if ($this->isLink() && isset($attributes['pageContent'])) {
			$attributes['link'] = $attributes['pageContent']; 
			unset($attributes['pageContent']); 
		}
		if (!isset($attributes['title']) && $conf->get('/config/imagevue/settings/autoTitling')) {
			$attributes['title'] = $this->getTitle();
		}
		if ($this->isFilemod()) {
			unset($attributes['sort']); 
			unset($attributes['date']); 
			unset($attributes['fileCount']);
			if (isset($attributes['parameters'])) {
				$attributes['fileParams'] = $attributes['parameters']; 
				unset($attributes['parameters']); 
			}
		}
		$attributes['date'] = formatDate($this->getProperty('date'));
		$node = &new ivXmlNode('folder', $attributes);
		return $node;
	}
	
	/**
	 * Return true if folder is hidden
	 *
	 * @return boolean
	 */
	function isHidden()
	{
		return $this->getAttribute('hidden') === 'true';
	}
	
	/**
	 * Return true if folder used as text page
	 *
	 * @return boolean
	 */
	function isPage()
	{
		return $this->getAttribute('page') === 'html';
	}
	
	/**
	 * Return true if folder used as link
	 *
	 * @return boolean
	 */
	function isLink()
	{
		return $this->getAttribute('page') === 'link';
	}
	
	/**
	 * Return true if folder used as filemod
	 *
	 * @return boolean
	 */
	function isFilemod()
	{
		return $this->getAttribute('page') === 'filemod';
	}
	
	/**
	 * Returns array of max thumb's dimensions
	 *
	 * @param  $force boolean
	 * @return array
	 */
	function getMaxThumbSize($force = false)
	{
		$conf = &ivPool::get('conf');

		if (!$force) {
			if ($conf->get('/config/imagevue/settings/useCache')) {
				$folderNode = &$this->_xml->findByXPath('/folder');
				if ($folderNode) {
					$maxThumbX = $folderNode->getAttribute('maxThumbWidth');
					$maxThumbY = $folderNode->getAttribute('maxThumbHeight');
					if (!is_null($maxThumbX) && !is_null($maxThumbY)) {
						return array(
							'maxThumbWidth' => $maxThumbX,
							'maxThumbHeight' => $maxThumbY
						);
					}
				}
			}
		}
		
		$maxThumbX = 0;
		$maxThumbY = 0;
		$content = getContent($this->_path);
		foreach ($content as $item) {
			if (ivFilepath::matchSuffix($item, $conf->get('/config/imagevue/settings/allowedext')) && ivFilepath::matchPrefix($item, array($this->_thumbnailPrefix))) {
				$data = @getimagesize($this->_path . $item);
				if (isset($data[2])) {
					$maxThumbX = isset($data[0]) && $data[0] > $maxThumbX ? $data[0] : $maxThumbX;
					$maxThumbY = isset($data[1]) && $data[1] > $maxThumbY ? $data[1] : $maxThumbY;
				}
			}
		}
		if ($conf->get('/config/imagevue/settings/useCache')) {
			$folderNode = &$this->_xml->findByXPath('/folder');
			if (!$folderNode) {
				$folderNode = &ivXmlNode::create('folder');
				$this->_xml->setNodeTree($folderNode);
			}
			$folderNode->setAttribute('maxThumbWidth', $maxThumbX);
			$folderNode->setAttribute('maxThumbHeight', $maxThumbY);
			$this->_xml->writeToFile();
		}
		return array(
			'maxThumbWidth' => $maxThumbX,
			'maxThumbHeight' => $maxThumbY
		);
	}
	
	function sanityFileName($name)
	{
		$nameWOExt = ivFilepath::filename($name);
		// Letters A–Z, a-z
		// Numbers 0–9
		// Space
		// ! # $ % & ' ( ) - @ ^ _ ` { } ~
		// + , . ; = [ ]
		$sanitiedName = trim(preg_replace('/[^a-zA-Z0-9\s\!\#\$\%\&\'\(\)\-\@\^\_\`\{\}\~\+\,\.\;\=\[\]]/u', '_', $nameWOExt));
		$ext = ivFilepath::suffix($name);
		if (!trim($sanitiedName, '_')) {
			$sanitiedName = '';
			$newName = "00001";
			$i = 2;
		} else {
			$newName = "$sanitiedName";
			$i = 1;
		}
		while (count(glob($this->_path . $newName . '.*'))) {
			$newName = sprintf("$sanitiedName%05d", $i++);
		}
		return "$newName.$ext";
	}

}
?>