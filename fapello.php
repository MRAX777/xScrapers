<?php

require "inc/simple_html_dom.php";
require "inc/func.php";
require "inc/config.php";

$conn = mysqli();

$domain = "fapello.com";

echo "<h1>$domain Scraper</h1>";

$users = file(DATA_DIR . "/fapello_users.txt");
$users = array_reverse($users);
foreach ($users as $user) {
    $user = trim($user);
    echo "<h2>$user</h2>";
    myobflush();
    $i = 0;
    $old_i = @file_get_contents(DATA_DIR . "/$domain-$user-i.txt");
    if ($old_i > 0) {
        $i = $old_i;
    }
    $found_posts = true;
    while ($found_posts) {
        $post_url = "https://$domain/$user/$i/";
        echo "<h3>$post_url</h3>";
        myobflush();
        $page_results = get($post_url);
        file_put_contents(
            HTML_DIR . "/$domain-$user-$i.html",
            $page_results["content"]
        );
        $page_content = $page_results["content"];
        if (
            !empty($page_content) &&
            $page_content != null &&
            $page_content != false
        ) {
            $page_html = str_get_html($page_content);

            try {
                file_put_contents(DATA_DIR . "/$domain-$user-i.txt", $i);
                $post_title = $page_html->find("title", 0)->innertext;
                foreach ($page_html->find("a") as $page_el) {
                    $found_posts = false;
                    $media_link = $page_el->href;
                    if (
                        strpos(" $media_link ", $user) &&
                        strpos(" $media_link ", ".jpg")
                    ) {
                        $media_type = "image";
                        $media_file = basename($media_link);
                        $line = "$user|$post_url|$media_type|$media_file|$media_link|$post_title";
                        $line = "$user|$post_url|$media_type|$media_file|$media_link|$post_title";
                        $post_title = addslashes($post_title);
                        $sql = "INSERT INTO 
                                     media (`user`, `post_url`, `type`, `path`, `url`, `title`)
                                    VALUES ('$user', '$post_url', '$media_type', '$media_file', '$media_link', '$post_title')";

                        if (mysqli_query($conn, $sql)) {
                            echo "New record created successfully";
                        } else {
                            echo "Error: " .
                                $sql .
                                "<br>" .
                                mysqli_error($conn);
                        }
                        echo "   $line<br>";
                        addline(DATA_DIR . "/media.txt", $line);
                        myobflush();
                        $found_posts = true;
                    }
                    if ($found_posts == false) {
                        break;
                    }
                }
            } catch (Exception $e) {
                echo "Caught exception: ", $e->getMessage(), "\n";
                //die('<meta http-equiv="refresh" content="1">');
            }
        }
        $i++;
        ////sleep(5);
    }
}
mysqli_close($conn);

