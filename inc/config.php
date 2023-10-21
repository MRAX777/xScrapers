<?php
$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36";77
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set("user_agent", $_SERVER["HTTP_USER_AGENT"]);
set_time_limit(0);
ob_implicit_flush(true);

define("DATA_DIR", "data");
define("MEDIA_DIR", "storage");
define("HTML_DIR", "html");
define("HTTP_USER_AGENT", $_SERVER["HTTP_USER_AGENT"]);
define("MYSQLI_HOST", "localhost");
define("MYSQLI_USER", "root");
define("MYSQLI_PASS", "");
define("MYSQLI_DB", "");
