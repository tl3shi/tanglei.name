<?php
/**
 * Languages XML node class
 *
 * @author McArrow
 */
class ivXmlNodeLanguage extends ivXmlNodeEnum
{

	/**
	 * Constructor
	 *
	 * @access protected
	 * @param  string    $name
	 * @param  array     $attrs
	 */	
	function __construct($name, $attrs = array())
	{
		parent::__construct($name, $attrs);
		$this->_values = getAllLangs();
	}
	
}
?>