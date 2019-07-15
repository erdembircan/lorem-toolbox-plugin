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
require $dir_path . 'includes/toolbox.php';

$lorem_raw = toolbox\read_file($dir_path . 'assets/plain/lorem-source.txt');
$args = array('file'=>__FILE__, 'lorem_raw'=>$lorem_raw);
$oopConstructed = new construct\LoremClass($args);
