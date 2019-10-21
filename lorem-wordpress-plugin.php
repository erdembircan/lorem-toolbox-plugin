<?php
/**
 * Plugin Name: Lorem Wordpress Plugin
 * Plugin URI: https://erdembircan.github.io/lorem-wordpress-plugin/
 * Author: Erdem Bircan
 * Author URI: https://erdembircan.github.io
 * Description: Lorem Wordpress Plugin
 * Requires at least: 5.1
 * Requires PHP: 7.2
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace erdembircan\lorem_plugin;

$dir_path = \plugin_dir_path(__FILE__);
require $dir_path . 'includes/LoremClass.php';

// read default lorem contents
$lorem_raw = file_get_contents($dir_path . 'assets/plain/lorem-source.txt');

// define default options for plugin
$args = array('file'=>__FILE__, 'internal'=>array('lorem_raw'=>$lorem_raw));

$oopConstructed = new construct\LoremClass($args);
