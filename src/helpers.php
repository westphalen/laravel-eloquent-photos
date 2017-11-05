<?php
/**
 * Created by PhpStorm.
 * User: sune
 * Date: 05/11/2017
 * Time: 13.26
 */

/**
 * Get the path to the photos folder.
 *
 * @param string $path
 * @return string
 */
function photo_path($path = null)
{
    $dir = rtrim(config('photo.path', storage_path()), DIRECTORY_SEPARATOR);

    return $dir . ($path ? DIRECTORY_SEPARATOR . $path : '');
}
