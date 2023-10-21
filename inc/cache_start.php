<?php

$cache_filename = 'cache/'.basename($_SERVER['PHP_SELF']) . "-" . $_SERVER['QUERY_STRING'].'.html';
$cache_limit_in_mins = 0 * 60; // this forms 32hrs
// check if we have a cached file already
if ( file_exists($cache_filename) )
{
    $secs_in_min = 60;
    $diff_in_secs = (time() - ($secs_in_min * $cache_limit_in_mins)) - filemtime($cache_filename);
    // check if the cached file is older than our limit
    if ( $diff_in_secs < 0 )
    {
        // it isn't, so display it to the user and stop
        print file_get_contents($cache_filename);
        exit();
    }
}