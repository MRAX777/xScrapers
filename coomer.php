<?php

require "inc/simple_html_dom.php";
require "inc/func.php";
require "inc/config.php";

$conn = mysqli();

$domain = "coomer.su";

echo "<h1>$domain Scraper</h1>";

$users = file(DATA_DIR . "/coomer_users.txt");
shuffle($users);
foreach ($users as $user) {
    $user = trim($user);
    echo "<h2>$user</h2>";
    myobflush();
    $i = 0;
    $old_i = @file_get_contents(DATA_DIR . "/$domain-$user-i.txt");
    if ($old_i > 0) {
        $i = $old_i;
    }
    while ($i <= 100) {
        $offset = $i * 50;
        $page_url = "https://$domain/onlyfans/user/$user?o=$offset";
        echo "<h3>$page_url</h3>";
        myobflush();
        $page_results = get($page_url);
        file_put_contents(
            HTML_DIR . "/$domain-$user-$offset.html",
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
                $found_posts = false;
                foreach ($page_html->find("article a") as $page_el) {
                    if (!empty($page_el->href)) {
                        if (
                            strpos(" " . $page_el->href . " ", "/$user/post/")
                        ) {
                            file_put_contents(
                                DATA_DIR . "/$domain-$user-i.txt",
                                $i
                            );

                            $post_url = "https://$domain" . $page_el->href;
                            $post_results = get($post_url);
                            $post_content = $post_results["content"];
                            if (
                                !empty($post_content) &&
                                $post_content != null &&
                                $post_content != false
                            ) {
                                $post_html = str_get_html($post_content);
                                $post_title = str_replace(
                                    " | Coomer",
                                    "",
                                    trim(
                                        $post_html->find("title", 0)->innertext
                                    )
                                );
                                foreach (
                                    $post_html->find("a[download]")
                                    as $post_el
                                ) {
                                    $media_link = $post_el->href;
                                    $media_file = $post_el->download;
                                    if (strpos(" $media_link ", ".jpg")) {
                                        //echo "<img style='max-width:360px;max-height:180px;' src='$img'>";
                                        //myobflush();
                                        $media_type = "image";
                                    } else {
                                        //echo "<video width='360' height='180' controls><source src='$link'></source></video>";
                                        //myobflush();
                                        $media_type = "video";
                                    }

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
                            }
                        }
                    }

                    //sleep(5);
                }
                if ($found_posts == false) {
                    break;
                }
            } catch (Exception $e) {
                echo "Caught exception: ", $e->getMessage(), "\n";
                die('<meta http-equiv="refresh" content="1">');
            }
        }
        $i++;
        ////sleep(5);
    }
}

mysqli_close($conn);

