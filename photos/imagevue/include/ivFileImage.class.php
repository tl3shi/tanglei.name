<?php
/**
 * Image file class
 *
 * @author McArrow
 */
class ivFileImage extends ivFile
{
	/**
	 * Properties
	 * @var array
	 */
	var $_properties = array(
		'name' => null,
		'path' => null,
		'date' => null,
		'size' => null,
		'width' => null,
		'height' => null,
		'type' => null
	);
	
	/**
	 * Plain exif data for image. Used for caching purposes
	 * @var array
	 */
	var $_exifData = null;

	/**
	 * Plain iptc data for image. Used for caching purposes
	 * @var array
	 */
	var $_iptcData = null;

	/**
	 * Initialize properties
	 *
	 * @access protected
	 */
	function _initProperties()
	{
		parent::_initProperties();
		$data = getimagesize($this->_path);
		$this->_setProperty('width', $data[0]);
		$this->_setProperty('height', $data[1]);
		$this->_setProperty('type', imageTypeToString($data[2]));

		$exifData = $this->exifReadData();
		if (is_array($exifData)) {
			$pattern = '/^(\d{4})\:(\d{2})\:(\d{2})\s+?(\d{2})\:(\d{2})\:(\d{2})$/';
			if (isset($exifData['DateTimeOriginal']) && preg_match($pattern, $exifData['DateTimeOriginal'], $m)) {
				$this->_setProperty('date', mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]));
			} else if (isset($exifData['DateTime']) && preg_match($pattern, $exifData['DateTime'], $m)) {
				$this->_setProperty('date', mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]));
			}
		}
	}

	/**
	 * Initialize attributes
	 *
	 * @access protected
	 */
	function _initAttributes()
	{
		parent::_initAttributes();
		if ((is_null($this->getAttribute('title')) || is_null($this->getAttribute('description')))) {
			$iptcData = $this->iptcReadData();
			if (is_array($iptcData)) {
				if (is_null($this->getAttribute('title')) && isset($iptcData['2#105'][0])) {
					$title = trim($iptcData['2#105'][0]);
					if (!empty($title)) {
						$this->setAttribute('title', $title);
						$this->_setState(STATE_DIRTY);
					}
				}
				if (is_null($this->getAttribute('description')) && isset($iptcData['2#120'][0])) {
					$description = trim($iptcData['2#120'][0]);
					if (!empty($description)) {
						$this->setAttribute('description', $description);
						$this->_setState(STATE_DIRTY);
					}
				}
			}
		}
		if ((is_null($this->getAttribute('title')) || is_null($this->getAttribute('description')))) {
			$exifData = $this->exifReadData();
			if (is_array($exifData)) {
				if (is_null($this->getAttribute('title')) && isset($exifData['DocumentName'])) {
					$title = trim($exifData['DocumentName']);
					if (!empty($title)) {
						$this->setAttribute('title', $title);
						$this->_setState(STATE_DIRTY);
					}
				}
				if (is_null($this->getAttribute('description')) && isset($exifData['ImageDescription'])) {
					$description = trim($exifData['ImageDescription']);
					if (!empty($description) && 'IF' != $description) {
						$this->setAttribute('description', $description);
						$this->_setState(STATE_DIRTY);
					}
				}
			}
		}
	}
	
	/**
	 * Checks if path is image
	 *
	 * @static
	 * @param  string $path
	 * @return boolean
	 */
	function isSupported($path)
	{
		if (!in_array(strtolower(ivFilepath::suffix($path)), array('gif', 'jpeg', 'jpg', 'tif', 'tiff', 'png'))) {
			return false;
		}

		$supportedTypes = array(
			'image/gif',
			'image/jpeg',
			'image/tiff',
			'image/png'
		);
		if (parent::isSupported($path)) {
			$data = @getimagesize($path);
			return in_array($data['mime'], $supportedTypes);
		} else {
			return false;
		}
		return true;
	}
	
	/**
	 * Generate thumb
	 *
	 */
	function generateThumb()
	{
		$conf = &ivPool::get('conf');
		$thumbPath = $this->_getThumbPath();
		$img = new ivImage($this->_path);
		if ($img->makeThumb(
			$conf->get('/config/imagevue/thumbnails/thumbnail/boxwidth'),
			$conf->get('/config/imagevue/thumbnails/thumbnail/boxheight'),
			$conf->get('/config/imagevue/thumbnails/thumbnail/quality'),
			$conf->get('/config/imagevue/thumbnails/thumbnail/resizetype'),
			$conf->get('/config/imagevue/thumbnails/thumbnail/keepaspect'),
			$conf->get('/config/imagevue/thumbnails/thumbnail/allowscaleup')
		)) {
			$img->write($thumbPath);
		}
	}
	
	/**
	 * Parses and returns exif data
	 *
	 * @return array
	 */
	function getExifData()
	{
		$exifParser = new ivExifParser();
		return $exifParser->parse($this->exifReadData());
	}
	
	/**
	 * Parses and returns iptc data
	 *
	 * @return array
	 */
	function getIptcData()
	{
		$iptcParser = new ivIptcParser();
		return $iptcParser->parse($this->iptcReadData());
	}
	
	/**
	 * Returns self XML-node
	 *
	 * @param  boolean   $expanded
	 * @return ivXmlNode
	 */
	function &asXml($expanded = true)
	{
		$node = &parent::asXml();
		if ($expanded) {
			$exifData = $this->getExifData();
			if (is_array($exifData)) {
				$exifNode = &ivXmlNode::create('exif');
				foreach ($exifData as $key => $value) {
					$tag = &ivXmlNode::create(str_replace(array(' ', '(', ')'), array('_', '', ''), $key));
					$tag->setValue($value);
					$tag->setAttribute('title', $key);
					$exifNode->addChild($tag);
					unset($tag);
				}
				$node->addChild($exifNode);
			}
			$iptcData = $this->getIptcData();
			if (is_array($iptcData)) {
				$iptcNode = &ivXmlNode::create('iptc');
				foreach ($iptcData as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $v) {
							$tag = &ivXmlNode::create(str_replace(array(' ', '(', ')', '/'), array('_', '', ''), $key));
							$tag->setValue($v);
							$tag->setAttribute('title', $key);
							$iptcNode->addChild($tag);
						}
					} else {
						$tag = &ivXmlNode::create(str_replace(array(' ', '(', ')', '/'), array('_', '', ''), $key));
						$tag->setValue($value);
						$tag->setAttribute('title', $key);
						$iptcNode->addChild($tag);
					}
					unset($tag);
				}
				$node->addChild($iptcNode);
			}
		}
		return $node;
	}
	
	/**
	 * exif_read_data wrapper
	 *
	 * Returns false if exif_read_data() function not exists or no data can be returned
	 *
	 * @return array
	 */
	function exifReadData()
	{
		if (is_null($this->_exifData)) {
			$this->_exifData = false;
			if (function_exists('exif_read_data')) {
				ivErrors::disable();
				$data = @exif_read_data($this->_path);
				ivErrors::enable();
				$this->_exifData = $this->_filterExifData($data);
			}
		}
		return $this->_exifData;
	}
	
	/**
	 * iptcparse wrapper
	 *
	 * Returns false if iptcparse() function not exists or no data can be returned
	 *
	 * @return array
	 */
	function iptcReadData()
	{
		if (is_null($this->_iptcData)) {
			$this->_iptcData = false;
			if (function_exists('iptcparse') && function_exists('getimagesize')) {
				ivErrors::disable();
				@getimagesize($this->_path, $info);
				if(isset($info['APP13'])) {
				    $this->_iptcData = @iptcparse($info['APP13']);
				}
				ivErrors::enable();
			}
		}
		return $this->_iptcData;
	}
	
	function _filterExifData($data)
	{
		if (!is_array($data)) {
			return $data;
		}
		
		$allowedTags = array(
			'Make',
			'Model',
			'ExposureTime',
			'FNumber',
			'FocalLength',
			'ISOSpeedRatings',
			'ExposureBiasValue',
			'Flash',
			'ImageDescription',
			'Orientation',
			'ResolutionUnit',
			'XResolution',
			'YResolution',
			'Software',
			'DateTime',
			'YCbCrPositioning',
			'Copyright',
			'ExposureProgram',
			'DateTimeOriginal',
			'DateTimeDigitized',
			'CompressedBitsPerPixel',
			'ShutterSpeedValue',
			'BrightnessValue',
			'MaxApertureValue',
			'SubjectDistance',
			'MeteringMode',
			'ColorSpace',
			'FocalPlaneResolutionUnit',
			'FocalPlaneXResolution',
			'FocalPlaneYResolution',
			'ExposureIndex',
			'SensingMethod',
			'Quality',
			'JPEGQuality',
			'ColorMode',
			'ImageAdjustment',
			'Focus',
			'DigitalZoom',
			'WhiteBalance',
			'Sharpness',
			'FlashMode',
			'FlashStrength',
			'PictureMode',
			'SoftwareRelease',
			'ImageType',
			'ImageNumber',
			'OwnerName',
			'ExifImageWidth',
			'ExifImageLength',
			'DocumentName',
		);
		
		$filteredData = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $allowedTags)) {
				$filteredData[$key] = $value;
			}
		}
		
		if (isset($data['THUMBNAIL']) && is_array($data['THUMBNAIL'])) {
			$filteredData['THUMBNAIL'] = array();

			$allowedThumbTags = array(
				'BitsPerSample',
				'Compression',
				'PhotometricInterpretation',
				'StripOffsets',
				'SamplesPerPixel',
				'RowsPerStrip',
				'StripByteCounts',
			);

			foreach ($data['THUMBNAIL'] as $key => $value) {
				if (in_array($key, $allowedThumbTags)) {
					$filteredData['THUMBNAIL'][$key] = $value;
				}
			}
		}
		
		return $filteredData;
	}

}
?>
