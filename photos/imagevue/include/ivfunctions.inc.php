<?php

/**
 * Error-free file_put_contents function
 *
 * @param  string $filename
 * @param  mixed  $data
 * @return int
 */
function iv_file_put_contents($filename, $data)
{
	$isWritable = file_exists($filename) && is_writable($filename);
	$notExistsAndIsWritable = !file_exists($filename) && is_writable(dirname($filename));

	if ($isWritable || $notExistsAndIsWritable) {
		return @file_put_contents($filename, $data);
	}

	ivMessenger::add('error', "Can't write to file " . substr($filename, strlen(ROOT_DIR)));

	return false;
}

/**
 * Error-free chmod function
 *
 * @param  string  $filename
 * @param  integer $mode
 * @return boolean
 */
function iv_chmod($filename, $mode)
{
	if (file_exists($filename) && is_writable($filename)) {
		ivErrors::disable();
		$result = @chmod($filename, $mode);
		ivErrors::enable();
		return $result;
	}

	return false;
}

/**
 * Error-free mkdir function
 *
 * @param  string  $pathname
 * @param  integer $mode
 * @return boolean
 */
function iv_mkdir($pathname, $mode)
{
	if (file_exists(dirname($pathname)) && is_writable(dirname($pathname))) {
		return @mkdir($pathname, $mode);
	}

	ivMessenger::add('error', "Can't create folder " . substr($pathname, strlen(ROOT_DIR)));

	return false;
}

?>