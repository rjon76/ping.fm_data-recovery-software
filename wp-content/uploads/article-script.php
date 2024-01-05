<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 240);
set_time_limit(240);
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once( __DIR__ . "/env.php");

$image_folder = __DIR__ . "/ai";

if (!is_dir($image_folder)) {
    mkdir($image_folder, 0755);
    echo "The directory $image_folder was successfully created.";
}

if ($_POST["title"]) {
    $theme_title = $_POST["title"];
}

if(isset($_POST["apps_links"])) {
    if($_POST["apps_links"] == 'on') {
        $apps_links = 'true';
    } else {
        $apps_links = 'false';
    }
} else {
    $apps_links = 'false';
}

if(isset($_POST["youtube_url"])) {
    $youtube_url = trim($_POST["youtube_url"]);
    $pageYoutube = trim($_POST["youtube_url"]);
} else {
    $youtube_url = '';
    $pageYoutube = '';
}

if ($_POST["h1title"]) {
    $h1title = $_POST["h1title"];
}

if ($_POST["meta_title"]) {
    $theme_meta_title = $_POST["meta_title"];
}

if ($_POST["url"]) {
    $anchor_url = trim($_POST["url"]);
} else {
    $anchor_url = '';
}

if ($_POST["url_descr"]) {
    $url_description = trim($_POST["url_descr"]);
} else {
    $url_description = '';
}

if ($_POST["anchor"]) {
    $anchor_title = trim($_POST["anchor"]);
} else {
    $anchor_title = '';
}

if ($_POST["post_url"]) {
    $post_url = $_POST["post_url"];
}

if ($_POST["domain_url"]) {
    $domain_url = $_POST["domain_url"];
}

if($domain_url === 'https://www.ping.fm/howto') {
    var_dump($OPENAI_API_KEY);
    exit();
    die();
}

if($_POST["faq_theme"]) {
    $faq_theme = $_POST["faq_theme"];
}

if (!empty($_FILES['file']["tmp_name"])) {
    $moved = move_uploaded_file($_FILES["file"]["tmp_name"], $image_folder . '/' . str_replace(" ", '-', $_FILES["file"]["name"]));

    if( $moved ) {
        $image_src = $image_folder . '/original-' . str_replace(" ", '-', $_FILES["file"]["name"]);
    }
}

if(empty($_FILES['file']["tmp_name"]) && $faq_theme) {
    $image_src = $image_folder . '/original-' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/"], "-", $faq_theme))).'.jpg';
}

if( !$_POST["title"] || !$_POST["meta_title"] || !$_POST["h1title"] ||
    !$_POST["post_url"] || !$_POST["faq_theme"] ) {
    echo 'false';
    exit();
}

$file = __DIR__ . '/time_record.txt';

writeTimeGeneration($file, 'start');

$path = __DIR__ . '/wpallimport/files/generated-post.xml';
$copy = __DIR__ . '/wpallimport/files/generated-post-copy.xml';
copy($path, $copy);

if(file_exists($path)) {
    $xmlstring = file_get_contents($path);
    $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $aArticles = json_decode($json, TRUE);
} else {
    $aArticles = [];
}
  
$xw = xmlwriter_open_memory();
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, ' ');
xmlwriter_start_document($xw, '1.0', 'UTF-8');
xmlwriter_start_element($xw, 'root');

    $b = null;

    do {
        $a = getInfoTitle($theme_title, $anchor_url, $anchor_title, $url_description, $apps_links, $OPENAI_API_KEY);
        if( isset($a->choices[0]->message->function_call) ) {
            $b = json_decode($a->choices[0]->message->function_call->arguments);
        }
    } while ( is_null($b) || !isset($b->step1) || !isset($b->intro) || !isset($b->conclusion) || !isset($b->tip1) );

    writeTimeGeneration($file, 'faq');

    $page_url = $post_url;

    $page_title = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $h1title
    );
    $page_intro = isset($b->intro) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->intro
    ) : '';
    $page_infomation = isset($b->information) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->information
    ) : '';
    $page_infotitle = isset($b->infotitle) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->infotitle
    ) : '';
    $page_scenario1 = isset($b->scenario1) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->scenario1
    ) : '';
    $page_scenario2 = isset($b->scenario2) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->scenario2
    ) : '';
    $page_scenario3 = isset($b->scenario3) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->scenario3
    ) : '';
    $scenarious = $page_scenario1 . $page_scenario2 . $page_scenario3;
    
    $page_step1 = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step1
    );
    $page_step2 = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step2
    );
    $page_step3 = isset($b->step3) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step3
    ) : '';
    $page_step4 = isset($b->step4) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step4
    ) : '';
    $page_step5 = isset($b->step5) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step5
    ) : '';
    $page_step6 = isset($b->step6) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step6
    ) : '';
    $page_step7 = isset($b->step7) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step7
    ) : '';
    $page_step8 = isset($b->step8) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->step8
    ) : '';
    $steps = $page_step1 . $page_step2 . $page_step3 . $page_step4 . $page_step5 . $page_step6 . $page_step7 . $page_step8;
    // ================
    $page_tip1 = isset($b->tip1) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->tip1
    ) : '';
    $page_tip2 = isset($b->tip2) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->tip2
    ) : '';
    $page_tip3 = isset($b->tip3) ? preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->tip3
    ) : '';
    // ================
    $page_conclusion = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->conclusion
    );
    
    $pageContent = '';
    $faqString = '<section class="faq"><h2>FAQ</h2>';
    $contentString = '';
    $tipsString = $page_tip1 . $page_tip2 . $page_tip3;

    $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/"], "-", $h1title)));

    if(empty($_FILES['file']["tmp_name"])) {
        $gen_image_src = null;

        do {
            $gen_image = generateImageDall3($faq_theme, $OPENAI_API_KEY);
            if( isset($gen_image->data[0]->url) ) {
                $gen_image_src = $gen_image->data[0]->url;
            }
        } while ( is_null($gen_image_src) );

        saveAiImage($gen_image_src, $image_src);
        generateImgWithTitle($h1title, $image_src, true, '');
    }

    if($moved) {
        generateImgWithTitle($h1title, $image_src, false, '');
    }

    if($apps_links == 'true') {

        $st4 = '';
        $st5 = '';
        $st6 = '';
        $st7 = '';
        $st8 = '';
        $st9 = '';
        $st10 = '';

        if(!empty($page_step4)) {
            $st4 = '<section><div class="no-medal"><div>4</div></div>'.$page_step4.'</section>';
        }

        if(!empty($page_step5)) {
            $st5 = '<section><div class="no-medal"><div>5</div></div>'.$page_step5.'</section>';
        }

        if(!empty($page_step6)) {
            $st6 = '<section><div class="no-medal"><div>6</div></div>'.$page_step6.'</section>';
        }

        if(!empty($page_step7)) {
            $st7 = '<section><div class="no-medal"><div>7</div></div>'.$page_step7.'</section>';
        }

        if(!empty($page_step8)) {
            $st8 = '<section><div class="no-medal"><div>8</div></div>'.$page_step8.'</section>';
        }

        if(!empty($page_step9)) {
            $st9 = '<section><div class="no-medal"><div>9</div></div>'.$page_step9.'</section>';
        }

        if(!empty($page_step10)) {
            $st10 = '<section><div class="no-medal"><div>10</div></div>'.$page_step10.'</section>';
        }

        if(empty($youtube_url)) {
            $videoString = '';
        } else {
            $videoString = '<section><h2>Youtube video to watch</h2><div class="nonp iframe">' . 
            preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $youtube_url)
                . '</div></section>';
        }

        $contentString .= '<section><h1>'.$page_title.'</h1><div class="inbrief"><div>
            <img src="' . $domain_url . '/wp-content/uploads/ai/'.$image_title.'.jpg" alt="'.$h1title.'" title="'.$h1title.'" width="1280" height="720">
            <p>'.$page_intro.'</p></div></div></section>
            <section><div class="medal"><div>&#129351;</div></div>'.$page_step1.'</section>
            <section><div class="medal"><div>&#129352;</div></div>'.$page_step2.'</section>
            <section><div class="medal"><div>&#129353;</div></div>'.$page_step3.'</section>'
            . $st4 . $st5 . $st6 . $st7 . $st8 . $st9 . $st10 . $videoString . '
            <section><h2>Conclusion:</h2><div class="nonp">'.$page_infomation.'</div></section>';

    } else {

        if(empty($youtube_url)) {
            $string = '<section><div><div>3</div><h2>Precautions and Tips:</h2></div>'.$tipsString.'</section>
                        <section><div><div>4</div><h2>'.$page_infotitle.'</h2></div>'.$page_infomation.'</section>
                        <section><h2>Conclusion:</h2><div class="nonp">'.$page_conclusion.'</div></section>';
        } else {
            $string = '<section><div><div>3</div><h2>Youtube video to watch</h2></div><div class="iframe">' . 
                            preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $youtube_url)
                        . '</div></section>
                        <section><div><div>4</div><h2>Precautions and Tips:</h2></div>'.$tipsString.'</section>
                        <section><div><div>5</div><h2>'.$page_infotitle.'</h2></div>'.$page_infomation.'</section>
                        <section><h2>Conclusion:</h2><div class="nonp">'.$page_conclusion.'</div></section>';
        }
    
        $contentString .= '<section><h1>'.$page_title.'</h1><div class="inbrief"><div>
            <img src="' . $domain_url . '/wp-content/uploads/ai/'.$image_title.'.jpg" alt="'.$h1title.'" title="'.$h1title.'" width="1280" height="720">
            <p>'.$page_intro.'</p></div></div></section>
            <section><div><div>1</div><h2>Common Scenarios:</h2></div>'.$scenarious.'</section>
            <section class="stepByStep"><div><div>2</div><h2>Step-by-Step Guide. '.ucwords($faq_theme).':</h2></div>'.$steps.'</section>' . $string;

    }

    $page_faq = null;

    do {
        $faq = getInfoFaq($faq_theme, 10, $OPENAI_API_KEY);
        if( isset($faq->choices) && !empty($faq->choices[0]) && isset($faq->choices[0]->message) && isset($faq->choices[0]->message->content) ) {
            $page_faq = $faq->choices[0]->message->content;
        }
    } while ( is_null($page_faq) );

    writeTimeGeneration($file, 'import');
    
    $faqParag = explode('<p>', $page_faq);
    $faqNoParag = str_replace(["<p>", "</p>", "</section>", '"', "</article>"], '', $faqParag);
    foreach($faqNoParag as $key => $p) {
        if($key === 0) continue;
                    
        if($key%2==1) {
            $faqString .= '<div class="panel"><div class="toggle-link"><h3 class="panel-title">
                '.trim($p).'</h3></div><div class="panel-collapse"><div class="panel-body"><div>';
        } else {
            $faqString .= '<p>'.trim($p).'</p></div></div></div></div>';
        }
    }

    $faqString.='</section>';
    $pageContent .= $contentString;

    if(!empty($aArticles["page"]) && count($aArticles["page"]) > 0) {
        if(empty($aArticles["page"][1]) && empty($aArticles["page"][2]) &&
            (!empty($aArticles["page"]["title"]) && $page_url != $aArticles["page"]["page_url"])) {
            xmlwriter_start_element($xw, 'page');
                xmlwriter_start_element($xw, 'page_meta');
                    xmlwriter_text($xw, $aArticles["page"]["page_meta"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'page_image');
                    xmlwriter_text($xw, $aArticles["page"]["page_image"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'page_url');
                    xmlwriter_text($xw, $aArticles["page"]["page_url"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'page_title');
                    xmlwriter_text($xw, $aArticles["page"]["page_title"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'page_content');
                    xmlwriter_text($xw, $aArticles["page"]["page_content"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'page_faq');
                    xmlwriter_text($xw, $aArticles["page"]["page_faq"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'title');
                    xmlwriter_text($xw, $aArticles["page"]["title"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'h1title');
                    xmlwriter_text($xw, $aArticles["page"]["h1title"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'url');
                    xmlwriter_text($xw, is_array($aArticles["page"]["url"]) ? '' : $aArticles["page"]["url"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'url_descr');
                    xmlwriter_text($xw, is_array($aArticles["page"]["url_descr"]) ? '' : $aArticles["page"]["url_descr"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'anchor');
                    xmlwriter_text($xw, is_array($aArticles["page"]["anchor"]) ? '' : $aArticles["page"]["anchor"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'original_image');
                    xmlwriter_text($xw, is_array($aArticles["page"]["original_image"]) ? '' : $aArticles["page"]["original_image"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'post_url');
                    xmlwriter_text($xw, $aArticles["page"]["post_url"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'youtube_url');
                    if(empty($aArticles["page"]["youtube_url"])) {
                        xmlwriter_text($xw, '');
                    } else {
                        xmlwriter_text($xw, $aArticles["page"]["youtube_url"]);
                    }
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'apps_links');
                    xmlwriter_text($xw, $aArticles["page"]["apps_links"]);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'faq_theme');
                    xmlwriter_text($xw, $aArticles["page"]["faq_theme"]);
                xmlwriter_end_element($xw);
            xmlwriter_end_element($xw);
        }

        if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1]["title"])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {

                if($page_url === $aArticles["page"][$i]["page_url"]) {
                    continue;
                }

                if(empty($aArticles["page"][$i]["title"])) {
                    continue;
                }

                xmlwriter_start_element($xw, 'page');
                    xmlwriter_start_element($xw, 'page_meta');
                        xmlwriter_text($xw, $aArticles["page"][$i]["page_meta"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_image');
                        xmlwriter_text($xw, $aArticles["page"][$i]["page_image"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_url');
                        xmlwriter_text($xw, $aArticles["page"][$i]["page_url"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_title');
                        xmlwriter_text($xw, $aArticles["page"][$i]["page_title"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_content');
                        xmlwriter_text($xw, $aArticles["page"][$i]["page_content"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_faq');
                        xmlwriter_text($xw, $aArticles["page"][$i]["page_faq"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'title');
                        xmlwriter_text($xw, $aArticles["page"][$i]["title"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'h1title');
                        xmlwriter_text($xw, $aArticles["page"][$i]["h1title"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'url');
                        xmlwriter_text($xw, is_array($aArticles["page"][$i]["url"]) ? '' : $aArticles["page"][$i]["url"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'url_descr');
                        xmlwriter_text($xw, is_array($aArticles["page"][$i]["url_descr"]) ? '' : $aArticles["page"][$i]["url_descr"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'anchor');
                        xmlwriter_text($xw, is_array($aArticles["page"][$i]["anchor"]) ? '' : $aArticles["page"][$i]["anchor"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'original_image');
                        xmlwriter_text($xw, is_array($aArticles["page"][$i]["original_image"]) ? '' : $aArticles["page"][$i]["original_image"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'post_url');
                        xmlwriter_text($xw, $aArticles["page"][$i]["post_url"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'youtube_url');
                        if(empty($aArticles["page"][$i]["youtube_url"])) {
                            xmlwriter_text($xw, '');
                        } else {
                            xmlwriter_text($xw, $aArticles["page"][$i]["youtube_url"]);
                        }
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'apps_links');
                        xmlwriter_text($xw, $aArticles["page"][$i]["apps_links"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'faq_theme');
                        xmlwriter_text($xw, $aArticles["page"][$i]["faq_theme"]);
                    xmlwriter_end_element($xw);
                xmlwriter_end_element($xw);
            }
        }
    }

    xmlwriter_start_element($xw, 'page');
        xmlwriter_start_element($xw, 'page_meta');
            xmlwriter_text($xw, $theme_meta_title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_image');
            xmlwriter_text($xw, $domain_url . '/wp-content/uploads/ai/'.$image_title.'.jpg');
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_url');
            xmlwriter_text($xw, $page_url);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_title');
            xmlwriter_text($xw, $page_title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_content');
            xmlwriter_text($xw, $pageContent);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_faq');
            xmlwriter_text($xw, $faqString);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'title');
            xmlwriter_text($xw, $theme_title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'h1title');
            xmlwriter_text($xw, $h1title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'url');
            xmlwriter_text($xw, $anchor_url);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'url_descr');
            xmlwriter_text($xw, $url_description);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'anchor');
            xmlwriter_text($xw, $anchor_title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'original_image');
            xmlwriter_text($xw, $image_src);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'post_url');
            xmlwriter_text($xw, $post_url);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'youtube_url');
            xmlwriter_text($xw, $pageYoutube);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'apps_links');
            xmlwriter_text($xw, $apps_links);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'faq_theme');
            xmlwriter_text($xw, $faq_theme);
        xmlwriter_end_element($xw);
    xmlwriter_end_element($xw);

xmlwriter_end_element($xw);
xmlwriter_end_document($xw);


$dom = new DOMDocument;
$dom->loadXML(xmlwriter_output_memory($xw));
$dom->save(__DIR__ . '/wpallimport/files/generated-post.xml');

autoImport([4], $domain_url);

writeTimeGeneration($file, 'done');