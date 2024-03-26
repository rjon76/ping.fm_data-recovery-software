<?php
function funcGenerateArticle() {

    if (!class_exists('PMXI_Import_List') || !class_exists('PMXI_Plugin')) {
        exit;
    }

    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 300);
    set_time_limit(300);
    ini_set('display_errors',1);
    error_reporting(E_ALL);

    require_once __DIR__ . "/../settings/env.php";

    if($OPENAI_API_KEY == '') {
        echo 'Empty OPENAI_API_KEY';
        exit;
    }

    $params['viewport']  = '1280x720';
    $params['delay'] = '10';
    $params['user_agent'] = 'Mozilla/5.0%20(Macintosh;%20Intel%20Mac%20OS%20X%2010_15_7)%20AppleWebKit/537.36%20(KHTML,%20like%20Gecko)%20Chrome/121.0.0.0%20Safari/537.36';

    $uploads = wp_upload_dir()["basedir"];
    $image_folder = $uploads . "/ai";
    $file = $uploads . '/time_record.txt';
    if(!file_exists($file)) {
        writeTimeGeneration($file, 'done');
    }
    $file_proccess = $uploads . '/process_record.txt';
    $file_queue = $uploads . '/regeneration_queue_ids.txt';
    $cron_job_key = PMXI_Plugin::getInstance()->getOption('cron_job_key');
    $domain_url = get_site_url();
    $isRegenerateFaq = false;
    $isAddFaq = false;
    
    $fields = ['type' => 'file'];
    $fields['name'] = 'generated-post.xml';

    if( isset($_GET["postId"]) && !empty($_GET["postId"]) ) {
        $newStringIds = '';

        if( isset($_GET["regFaq"]) ) {
            $isRegenerateFaq = true;
            $file_queue = $uploads . '/regeneration_faq_queue_ids.txt';
        }

        if( isset($_GET["addQuestions"]) ) {
            $isAddFaq = true;
            $file_queue = $uploads . '/add_questions_faq_queue_ids.txt';
        }

        if(file_exists($file_queue)) {

            $stringsID = file_get_contents($file_queue);

            $aIDs = explode(',', $stringsID);

            foreach($aIDs as $ids) {
                if($ids !== '') {
                    $newStringIds .= $ids . ',';
                }
            }

            $newStringIds .= $_GET["postId"] . ',';
            file_put_contents($file_queue, $newStringIds);
        } else {
            file_put_contents($file_queue, $_GET["postId"] . ',');
        }
    }

    foreach((new PMXI_Import_List())->getBy($fields)->toArray() as $import) {
        $importID = $import["id"];
    }

    do {
        $isDonePrevGeneration = file_get_contents($file);
        if($isDonePrevGeneration !== 'done') {
            sleep(10);
        }
    } while ($isDonePrevGeneration !== 'done');

    if (!is_dir($image_folder)) {
        mkdir($image_folder, 0755);
        echo "The directory $image_folder was successfully created.";
    }

    if( isset($_GET["postId"]) ) {
        $idIdx = $_GET["postId"];

        $theme_meta_title = get_field('page_meta', $idIdx);
        $pageData = get_post($idIdx);
        $theme_title = get_field('title', $idIdx);
        $pageContent = $pageData->post_content;
        $faqContent = get_field('faq', $idIdx);
        $apps_links = get_field('apps_links', $idIdx);
        $youtube_url = get_field('youtube_url', $idIdx);
        $pageYoutube = get_field('youtube_url', $idIdx);
        $page_title = $h1title = get_field('h1title', $idIdx);
        $anchor_url = get_field('url', $idIdx);
        $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/", "‘", "’", "'"], "-", $h1title)));
        $anchor_title = get_field('anchor', $idIdx);
        $url_description = get_field('url_descr', $idIdx);
        $page_url = $post_url = get_field('post_url', $idIdx);
        $faq_theme = get_field('faq_theme', $idIdx);
        $image_src = $image_folder . '/original-' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/", "‘", "’", "'"], "-", $faq_theme))).'.jpg';
        $moved = false;

    } else {

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

        if($_POST["faq_theme"]) {
            $faq_theme = $_POST["faq_theme"];
        }

        $moved = false;

        if (!empty($_FILES['file']["tmp_name"])) {
            $moved = move_uploaded_file($_FILES["file"]["tmp_name"], $image_folder . '/' . str_replace(" ", '-', $_FILES["file"]["name"]));

            if( $moved ) {
                $image_src = $image_folder . '/original-' . str_replace(" ", '-', $_FILES["file"]["name"]);
            }
        }

        if(empty($_FILES['file']["tmp_name"]) && $faq_theme) {
            $image_src = $image_folder . '/original-' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/", "‘", "’", "'"], "-", $faq_theme))).'.jpg';
        }

        if( !$_POST["title"] || !$_POST["meta_title"] || !$_POST["h1title"] ||
            !$_POST["post_url"] || !$_POST["faq_theme"] ) {
            echo 'false';
            exit();
        }
    }

    writeTimeGeneration($file, 'start');

    if(!$isRegenerateFaq && !$isAddFaq) {
        writeTimeGeneration($file_proccess, 'Generating a new article with the title: ' . $theme_title);
    } 

    if($isRegenerateFaq) {
        writeTimeGeneration($file_proccess, 'Regenerating FAQ for: ' . $theme_title);
    }

    if($isAddFaq) {
        writeTimeGeneration($file_proccess, 'Add 10 Questions FAQ for: ' . $theme_title);
    }

    $path = $uploads . '/wpallimport/files/generated-post.xml';
        
    $xw = xmlwriter_open_memory();
    xmlwriter_set_indent($xw, 1);
    $res = xmlwriter_set_indent_string($xw, ' ');
    xmlwriter_start_document($xw, '1.0', 'UTF-8');
    xmlwriter_start_element($xw, 'root');

        $b = null;

        if(!$isRegenerateFaq && !$isAddFaq) {

            do {
                $a = getInfoTitle($theme_title, $anchor_url, $anchor_title, $url_description, $apps_links, $OPENAI_API_KEY);
                if( isset($a->choices[0]->message->function_call) ) {
                    $b = json_decode($a->choices[0]->message->function_call->arguments);
                }
            } while ( is_null($b) || !isset($b->step1) || !isset($b->intro) || !isset($b->conclusion) || !isset($b->tip1) );

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

            $contentString = '';
            $tipsString = $page_tip1 . $page_tip2 . $page_tip3;

            $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/", "‘", "’", "'"], "-", $h1title)));

            if(empty($_FILES['file']["tmp_name"])) {
                $gen_image_src = null;

                do {
                    $gen_image = generateImageDall3($faq_theme, $OPENAI_API_KEY);
                    if( isset($gen_image->data[0]->url) ) {
                        $gen_image_src = $gen_image->data[0]->url;
                    }
                } while ( is_null($gen_image_src) );

                saveAiImage($gen_image_src, $image_src);
                generateImgWithTitle($h1title, $image_src, true, '', '');
            }

            if($moved) {
                generateImgWithTitle($h1title, $image_src, false, '', '');
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
                    preg_match(
                        "/(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:\S*&)?vi?=|(?:embed|v|vi|user|shorts)\/))([^?&\"'>\s]+)/",
                        $youtube_url,
                        $matches
                    );
                    
                    $videoString = '<section><h2>Youtube video to watch</h2><div class="nonp iframe">' . 
                        "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$matches[1]\" frameborder=\"0\" allowfullscreen></iframe>"
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
                    preg_match(
                        "/(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:\S*&)?vi?=|(?:embed|v|vi|user|shorts)\/))([^?&\"'>\s]+)/",
                        $youtube_url,
                        $matches
                    );
                    $string = '<section><div><div>3</div><h2>Youtube video to watch</h2></div><div class="iframe">' . 
                                    "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$matches[1]\" frameborder=\"0\" allowfullscreen></iframe>"
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

            $pageContent .= $contentString;

            $aContentUrls = getUrlsFromString($pageContent);

            if(!empty($aContentUrls[0])) {
                foreach($aContentUrls[0] as $url) {
    
                    $cUrl = str_replace(["'", '"'], '', $url);
                    
                    $isValid = is_valid_uri($cUrl);
                    
                    if(!$isValid) {
                        $pageContent = replaceOfficialWebsite($pageContent, $cUrl);
                        $pageContent = replaceInvalidUrl($pageContent, $cUrl);
                    }
                }
            }

            if($apps_links == 'true') {

                $aSoftUrls = getSoftUrlFromString($pageContent);
    
                if(!empty($aSoftUrls[0])) {
                    foreach($aSoftUrls[0] as $soft) {
    
                        $aUrl = getUrlsFromString($soft);
                        $softUrl = str_replace(["'", '"'], "", $aUrl[0][0]);
                            
                        $language = '';
                        $screenTitle = str_replace(['/', '.', ':', '?', '='], '-', $softUrl) . $language . '.jpg';
                        $screenTitleOriginal = str_replace(['/', '.', ':', '?', '='], '-', $softUrl) . '-original.jpg';
                        // capture
                        if(!file_exists($uploads . '/ai/' . $screenTitleOriginal)) {
                            $call = screenshotlayer($softUrl, $params, $SCREENSHOT_KEY);
                            $result = saveAiImage($call, $uploads . '/ai/' . $screenTitleOriginal);
                            if($result === "NOT_IMAGE") continue;
                            
                            sleep(10);
                        }

                        $softTitle = str_replace(["<h2>", "</h2>", "<b>", "</b>"], "", replaceInvalidUrl($soft, $softUrl));
                        
                        generateImgWithTitle($softTitle, $uploads . '/ai/' . $screenTitleOriginal, true, $language, '', $domain_url, $softUrl);

                        $pageContent = preg_replace('#<h2><a [h,H]ref=[*,",\']'.$softUrl.'+[^>]*.(.*?)<\/a></h2>#i', $soft.'<img src="'. $domain_url .'/wp-content/uploads/ai/'. $screenTitle .'">', $pageContent);
                    }
                }
            }
        }

        $faqString = !$isAddFaq ? '<section class="faq"><h2>FAQ</h2>' : '<h2>FAQ</h2>';

        if($isAddFaq) {
            $tmpFaqContent = explode('<h2>FAQ</h2>', $faqContent);
        }

        writeTimeGeneration($file, 'faq');

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

        $faqFullContent = !$isAddFaq ? $faqString . '</section>' : $tmpFaqContent[0] . $faqString . $tmpFaqContent[1];

        $aFaqUrls = getUrlsFromString($faqFullContent);

        if(!empty($aFaqUrls[0])) {
            foreach($aFaqUrls[0] as $url) {

                $cUrl = str_replace(["'", '"'], '', $url);
                
                $isValid = is_valid_uri($cUrl);
                
                if(!$isValid) {
                    $faqFullContent = replaceInvalidUrl($faqFullContent, $cUrl);
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
                xmlwriter_text($xw, $faqFullContent);
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
    $dom->save($uploads . '/wpallimport/files/generated-post.xml');

    $isImportDone = true;

    if($importID) {
        do {
            try {
                autoImport([$importID], $domain_url, $cron_job_key);
                $isImportDone = true;
            } catch (Exception $e) {
                $isImportDone = false;
            }
        } while (!$isImportDone);
    }

    if( isset($_GET["postId"]) ) {
        $stringsID = file_get_contents($file_queue);
        file_put_contents($file_queue, str_replace($_GET["postId"] . ',', '', $stringsID));
    }
    writeTimeGeneration($file_proccess, 'done');
    writeTimeGeneration($file, 'done');

}
