<?php
/**
 * Plugin Name: Lorem Wordpress Plugin
 * Author: Erdem Bircan
 * Description: Lorem Wordpress Plugin
 * Version: 1.0.0
 */
namespace erdembircan\lorem_plugin;

$dir_path = \plugin_dir_path(__FILE__);
require $dir_path . 'includes/LoremClass.php';

// read default lorem contents
$lorem_raw = file_get_contents($dir_path . 'assets/plain/lorem-source.txt');

// define default options for plugin
$args = array('file'=>__FILE__, 'internal'=>array('lorem_raw'=>$lorem_raw));

$oopConstructed = new construct\LoremClass($args);
