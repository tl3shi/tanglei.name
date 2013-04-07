<?php

$GLOBALS['ivErrors_enabled'] = true;

/**
 * Errors storage wrapper
 *
 * @static
 * @author McArrow
 */
class ivErrors
{
	/**
	 * Adds new error
	 *
	 * @param integer $severity
	 * @param string  $message
	 * @param string  $filepath
	 * @param integer $line
	 * @param boolean $defaultError
	 */
	function add($severity, $message, $filepath, $line, $defaultError = true)
	{
		if (!isset($_SESSION['imagevue']['errors'])) {
			ivErrors::clear();
		}

		if (false !== strpos($filepath, '/getid3/')) {
			if (in_array($severity, array(E_NOTICE, E_USER_NOTICE))) {
				return;
			}
		}

		if (!$GLOBALS['ivErrors_enabled']) {
			return;
		}

		if (in_array($severity, array(E_STRICT, E_DEPRECATED, E_USER_DEPRECATED))) {
			return;
		}

		$filepath = ivPath::canonizeAbsolute($filepath, true);

		// For safety reasons we do not show the full file path
		if (false !== strpos($filepath, '/')) {
			$filepath = substr($filepath, strlen(ROOT_DIR) - 1);
		}

		$levels = array(
			E_ERROR => 'Error',
			E_WARNING => 'Warning',
			E_PARSE => 'Parsing Error',
			E_NOTICE => 'Notice',
			E_CORE_ERROR => 'Core Error',
			E_CORE_WARNING => 'Core Warning',
			E_COMPILE_ERROR => 'Compile Error',
			E_COMPILE_WARNING => 'Compile Warning',
			E_USER_ERROR => 'User Error',
			E_USER_WARNING => 'User Warning',
			E_USER_NOTICE => 'User Notice',
			E_STRICT => 'Strict',
			E_RECOVERABLE_ERROR  => 'Recoverable Error',
			E_DEPRECATED => 'Deprecated',
			E_USER_DEPRECATED => 'User Deprecated',
		);

		$severity = (!isset($levels[$severity])) ? $severity : $levels[$severity];

		$newError = array(
			'severity' => $severity,
			'message' => $message,
			'filepath' => $filepath,
			'line' => $line,
			'defaultError' => $defaultError
		);
		foreach ($_SESSION['imagevue']['errors'] as $error) {
			if ($error == $newError) {
				return;
			}
		}
		$_SESSION['imagevue']['errors'][] = $newError;
	}
	
	/**
	 * Returns all errors and empties storage
	 *
	 * @param  integer $severity
	 * @return array
	 */
	function get()
	{
		$errors = array();
		if (isset($_SESSION['imagevue']['errors'])) {
			$errors = $_SESSION['imagevue']['errors'];
		}
		ivErrors::clear();
		return $errors;
	}
	
	/**
	 * Clears error storage
	 *
	 */
	function clear()
	{
		$_SESSION['imagevue']['errors'] = array();
	}
	
	/**
	 * Checks if error storage is empty
	 *
	 * @return boolean
	 */
	function isEmpty()
	{
		if (isset($_SESSION['imagevue']['errors'])) {
			return (count($_SESSION['imagevue']['errors']) == 0);
		}
		return true;
	}
	
	/**
	 * Enables error logging
	 *
	 */
	function enable()
	{
		$GLOBALS['ivErrors_enabled'] = true;
	}
	
	/**
	 * Disables error logging
	 *
	 */
	function disable()
	{
		$GLOBALS['ivErrors_enabled'] = false;
	}

}

?>