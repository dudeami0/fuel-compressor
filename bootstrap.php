<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

Autoloader::add_core_namespace('Compressor');

Autoloader::add_classes(array(
	'Compressor\\Compressor'       => __DIR__.'/classes/compressor.php',
	'Compressor\\HtmlCompressor'   => __DIR__.'/classes/html-compressor/html-compressor.php',
	'Compressor\\JavaScriptPacker' => __DIR__.'/classes/js-packer/class.JavaScriptPacker.php',
	'Compressor\\CssMin'           => __DIR__.'/classes/cssmin/cssmin.php',
));


/* End of file bootstrap.php */