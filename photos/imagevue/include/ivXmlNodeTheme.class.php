<?php
/**
 * Themes XML node class
 *
 * @author McArrow
 */
class ivXmlNodeTheme extends ivXmlNodeEnum
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
		$this->_values = ivTheme::getAllThemes();
	}
	
}
?>