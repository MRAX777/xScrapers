<?php

function mysqli()
{
    // Create connection
    $conn = mysqli_connect(MYSQLI_HOST, MYSQLI_USER, MYSQLI_PASS, MYSQLI_DB);
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function getAbsoluteUrl($relativeUrl, $baseUrl)
{
    // if already absolute URL
    if (parse_url($relativeUrl, PHP_URL_SCHEME) !== null) {
        return $relativeUrl;
    }

    // queries and anchors
    if ($relativeUrl[0] === "#" || $relativeUrl[0] === "?") {
        return $baseUrl . $relativeUrl;
    }

    // parse base URL and convert to: $scheme, $host, $path, $query, $port, $user, $pass
    extract(parse_url($baseUrl));

    // if base URL contains a path remove non-directory elements from $path
    if (isset($path) === true) {
        $path = preg_replace('#/[^/]*$#', "", $path);
    } else {
        $path = "";
    }

    // if realtive URL starts with //
    if (substr($relativeUrl, 0, 2) === "//") {
        return $scheme . ":" . $relativeUrl;
    }

    // if realtive URL starts with /
    if ($relativeUrl[0] === "/") {
        $path = null;
    }

    $abs = null;

    // if realtive URL contains a user
    if (isset($user) === true) {
        $abs .= $user;

        // if realtive URL contains a password
        if (isset($pass) === true) {
            $abs .= ":" . $pass;
        }

        $abs .= "@";
    }

    $abs .= $host;

    // if realtive URL contains a port
    if (isset($port) === true) {
        $abs .= ":" . $port;
    }

    $abs .=
        $path .
        "/" .
        $relativeUrl .
        (isset($query) === true ? "?" . $query : null);

    // replace // or /./ or /foo/../ with /
    $re = ["#(/\.?/)#", "#/(?!\.\.)[^/]+/\.\./#"];
    for ($n = 1; $n > 0; $abs = preg_replace($re, "/", $abs, -1, $n)) {
    }

    // return absolute URL
    return $scheme . "://" . $abs;
}

function savejpeg($file)
{
    try {
        $im = @imagecreatefromstring(file_get_contents($file));
        if ($im != false) {
            imagejpeg($im, $file, 100);
        } else {
            # code...
            unlink($file);
        }
    } catch (Exception $e) {
        //echo $e;
    }
}

function myobflush()
{
    echo str_repeat(" ", 1024 * 64);
    usleep(500);
}

function addline($file, $line)
{
    if (!file_exists($file)) {
        file_put_contents($file, "");
    }

    $lines = file($file);
    if (!in_array($line, $lines)) {
        file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function cookies2str($cookies_file)
{
    $lines = file($cookies_file);
    foreach ($lines as $l) {
        $segs = explode("	", $l);
        $cookies[] = "$segs[5]=$segs[6]";
    }
    // print_r($cookies);
    return implode("; ", $cookies);
}

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */
function get($url, $follow = true)
{
    $options = [
        CURLOPT_URL => $url,
        //  CURLOPT_VERBOSE => true,
        CURLOPT_COOKIESESSION => true,
        CURLOPT_COOKIEJAR => "data/cookiejar.txt",
        CURLOPT_COOKIEFILE => "data/cookiefile.txt",
        CURLOPT_REFERER => "http://coomer.us/",
        /*   CURLOPT_HTTPHEADER => [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8",
            "Accept-Language: *",
            "Connection: keep-alive",
            "Upgrade-Insecure-Requests: 0",
        ],*/
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_ENCODING => "", // handle all encodings
        CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"], // who am i
        CURLOPT_CONNECTTIMEOUT => 30, // timeout on connect
        CURLOPT_TIMEOUT => 120, // timeout on response
        CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false, // Disabled SSL Cert checks
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FAILONERROR => true,
    ];
    if ($follow) {
        $opt[CURLOPT_FOLLOWLOCATION] = true; // follow redirects
        $opt[CURLOPT_AUTOREFERER] = true; // set referer on redirect
        array_push($options, $opt);
    }
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $header["errno"] = $err;
    $header["errmsg"] = $errmsg;
    $header["content"] = $content;
    $header["info"] = $info;
    return $header;
}
