<?php

/**
 * A utility for fuel for compressing HTML, CSS, and JavaScript.
 *
 * The code will automatically spit out the asset tag in a
 * production server. In development, it will check if
 * any files changed that were added to this file.
 *
 * Credits to
 *    http://github.com/tylerhall/html-compressor ... HTML compressior.
 *    http://dean.edwards.name/packer/ .............. JS Packer
 *    http://code.google.com/p/cssmin/ .............. CssMin
 *
 * Note that those libraries have small modifications to them
 * to work with this package. If using them, get the originals :)
 *
 */

namespace Compressor;

class Compressor {

	/**
	 * Compresses JS code or files given to it. The first input can either be an array or a string.
	 *
	 * Arrays must be a list of files. Note no extension can be given.
	 *
	 * @param   Mixed   $input      The JavaScript files or code to compress
	 * @param   string  $filename   Either null to return, or the filename of the new file (with no extensions).
	 * @param   bool    $force      If on, ignores any checks to update.
	 */
	static function js($input, $filename = null, $force = false) {
		\Config::load('compressor', 'compressor');
		// To make sure production runs well, return the asset tag.
		if (\Config::get('environment') === \Fuel::PRODUCTION)
			return \Asset::js($filename.'.js');
		// Now we continue like normal
		$code = '';
		$return = $path = null;
		$lastmodified = $merged_lastmodified = 0;
		$path = \Config::get('compressor.cache_dir') . \Config::get('asset.js_dir');
		if (is_array($input)) {
				// Inital loop
				for ($i = 0; $i < count($input); $i++) {
					// Get the location
					$input[$i] = \Asset::find_file($input[$i], 'js');
					// Finally, check if this is newer than our other files
					if (filemtime($input[$i]) > $lastmodified)
						$lastmodified = filemtime($input[$i]);
				}
				// Check if the output file exists, and get its modified time.
				if (is_file($path.$filename.'.js'))
					$merged_lastmodified = filemtime($path.$filename.'.js');
				// We need to update, start reading!
				if ($merged_lastmodified < $lastmodified)
					for ($i = 0; $i < count($input); $i++) {
						if ($input[$i] !== false)
							$code .= file_get_contents($input[$i]);
					}
		} else
			$code = $input;
		// Check if code is empty or not
		if ($filename === null && empty($code))
			return '';
		$packer = new JavaScriptPacker($code);
		$code = $packer->pack();
		if ($filename !== null && !empty($code)) {
			$return = file_put_contents($path.$filename.'.js', $code);
			$return = $return !== false ? \Asset::js($filename . '.js') : false;
		} else if ($filename !== null && is_file($path.$filename.'.js')) {
			$return = \Asset::js($filename . '.js');
		}
		return is_null($return) ? $code : $return;
	}
	
	/**
	 * Compresses CSS code or files given to it. The first input can either be an array or a string.
	 *
	 * Arrays must be a list of files. Note no extension is given.
	 *
	 * @param   Mixed   $input      The CSS files or code to compress
	 * @param   string  $filename   Either null to return, or the filename of the new file (with no extensions).
	 * @param   bool    $force      If on, ignores any checks to update.
	 */
	static function css($input, $filename = null, $force = false) {
		// To make sure production runs well, return the asset tag.
		if (\Config::get('environment') === \Fuel::PRODUCTION)
			return \Asset::css($filename.'.css');
		// Now we continue like normal
		$code = '';
		$return = $path = null;
		$lastmodified = $merged_lastmodified = 0;
		$path = \Config::get('compressor.cache_dir') . \Config::get('asset.css_dir');
		if (is_array($input)) {
				// Inital loop
				for ($i = 0; $i < count($input); $i++) {
					// Get the location
					$input[$i] = \Asset::find_file($input[$i], 'css');
					// Finally, check if this is newer than our other files
					if (filemtime($input[$i]) > $lastmodified)
						$lastmodified = filemtime($input[$i]);
				}
				// Check if the output file exists, and get its modified time.
				if (is_file($path.$filename.'.css'))
					$merged_lastmodified = filemtime($path.$filename.'.css');
				// We need to update, start reading!
				if ($merged_lastmodified < $lastmodified)
					for ($i = 0; $i < count($input); $i++) {
						if ($input[$i] !== false)
							$code .= file_get_contents($input[$i]);
					}
		} else
			$code = $input;
		// Check if code is empty or not
		if ($filename === null && empty($code))
			return '';
		$code = CssMin::minify($code);
		if ($filename !== null && !empty($code)) {
			$return = file_put_contents($path.$filename.'.css', $code);
			$return = $return !== false ? \Asset::css($filename . '.css') : false;
		} else if ($filename !== null && is_file($path.$filename.'.css')) {
			$return = \Asset::css($filename . '.css');
		}
		return is_null($return) ? $code : $return;
	}

	/**
	 * Compress HTML code or files given to it. The first input can either be an array or a string.
	 *
	 * Arrays must be a list of files, and relevant paths will use the asset library.
	 *
	 * @param   Mixed   $input   The HTML files or code to compress
	 */
	static function html($input) {
		$compressor = new HtmlCompressor();
		return $compressor->html_compress($input);
	}
}