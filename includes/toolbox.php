<?php
namespace erdembircan\lorem_plugin\toolbox;

/**
 * Read file contents
 *
 * @param string $path absolute path to file
 * @return string file contents
 */
function read_file($path)
{
    ob_start();
    require($path);
    $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}
