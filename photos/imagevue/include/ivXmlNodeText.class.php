<?php
/**
 * Text XML node class
 *
 * @author McArrow
 */
class ivXmlNodeText extends ivXmlNode
{
	/**
	 * Set node's value
	 *
	 * @param string $value
	 */
	function setValue($value)
	{
		parent::setValue(trim($value));
	}	

	/**
	 * Returns HTML form element for current node
	 *
	 * @param  string $name
	 * @param  string $id
	 * @return string
	 */
	function toFormElement($name, $id)
	{
		$html = '<textarea name="' . $name . '" onfocus="myhelp(true, \'' . $id . '\')" onblur="myhelp(false, \'' . $id . '\')" />' . htmlspecialchars($this->_getSerializedValue()) . '</textarea>';
		return $html;
	}
	
}
?>