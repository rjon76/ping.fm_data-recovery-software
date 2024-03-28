<?php

function funcTranslateArticle() {

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

    $uploads = wp_upload_dir()["basedir"];
    $image_folder = $uploads . "/ai";
    $file = $uploads . '/time_record.txt';
    if(!file_exists($file)) {
        writeTimeGeneration($file, 'done');
    }
    $file_proccess = $uploads . '/process_record.txt';
    $file_queue = $uploads . '/translation_queue_ids.txt';
    $cron_job_key = PMXI_Plugin::getInstance()->getOption('cron_job_key');
    $domain_url = get_site_url();
    $onlyFaq = 'false';
    $fullLanguage = '';

    global $sitepress;

    if(isset($_GET["trid"]) && !empty($_GET["trid"])) {
        $translations = apply_filters( 'wpml_get_element_translations', NULL, $_GET['trid'], 'post_page' );
        foreach ( $translations as $lang => $translation ) {
                    
            if('en' == $translation->language_code) {
                $_GET["postId"] = $translation->element_id;
                break;
            }
        }
    }

    if( isset($_GET["postId"]) && !empty($_GET["postId"]) || isset($_GET["trid"]) && !empty($_GET["trid"]) ) {

        if( isset($_GET["onlyFaq"]) && !empty($_GET["onlyFaq"]) ) {
            $onlyFaq = 'true';
            $file_queue = $uploads . '/translate_faq_queue_ids.txt';
        }

        if( isset($_GET["fullLanguage"]) && !empty($_GET["fullLanguage"]) ) {
            $fullLanguage = $_GET["fullLanguage"];
            $file_queue = $uploads . '/translate_single_queue_ids.txt';
        }

        $newStringIds = '';
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

    do {
        $isDonePrevGeneration = file_get_contents($file);
        if($isDonePrevGeneration !== 'done') {
            sleep(10);
        }
    } while ($isDonePrevGeneration !== 'done');

    if( isset($_GET["postId"]) && !empty($_GET["postId"]) ) {

        $idIdx = $_GET["postId"];

        if( isset($_GET["fullLanguage"]) && !empty($_GET["fullLanguage"]) ) {
            $post_id = $_GET["postId"];
            $type = apply_filters( 'wpml_element_type', get_post_type( $post_id ) );
            $trid = apply_filters( 'wpml_element_trid', false, $post_id, $type );
        
            $translations = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );
            foreach ( $translations as $lang => $translation ) {
                
                if($fullLanguage == $translation->language_code) {
                    $idIdx = $translation->element_id;
                    break;
                }
            }
            
        }

        $page_meta = get_field('page_meta', $idIdx);
        $page_image = get_field('page_image', $idIdx);
        $page_url = get_field('page_url', $idIdx);
        $pageData = get_post($idIdx);
        $page_title = $pageData->post_title;
        $page_content = $pageData->post_content;
        $page_faq = get_field('faq', $idIdx);
        $title = get_field('title', $idIdx);
        $h1title = get_field('h1title', $idIdx);
        $url = get_field('url', $idIdx);
        $url_descr = get_field('url_descr', $idIdx);
        $anchor = get_field('anchor', $idIdx);
        $image_src = get_field('original_image', $idIdx);
        $post_url = get_field('post_url', $idIdx);
        $youtube_url = get_field('youtube_url', $idIdx);
        $apps_links = get_field('apps_links', $idIdx);
        $faq_theme = get_field('faq_theme', $idIdx);
    }

    writeTimeGeneration($file, 'start');
    if($onlyFaq == 'false' && !isset($_GET["fullLanguage"])) {
        writeTimeGeneration($file_proccess, 'Translating article with id: ' . $idIdx);
    } else if(isset($_GET["fullLanguage"])) {
        writeTimeGeneration($file_proccess, 'Translating single locale: ' . $_GET["fullLanguage"]);
    } else {
        writeTimeGeneration($file_proccess, 'Translating FAQ for article with id: ' . $idIdx);
    }



    $aContentFirst = [];
    $aContentSecond = [];
    $aContentThird = [];
    $aContentSections = explode("</section>", $page_content);
    list($aContentFirst, $aContentSecond, $aContentThird) = array_chunk($aContentSections, ceil(count($aContentSections)/3));
    $sContentFirst = '';
    $sContentSecond = '';
    $sContentThird = '';

    foreach($aContentFirst as $cF) {
        $sContentFirst .= $cF . '</section>';
    }
    foreach($aContentSecond as $key => $cS) {
        $sContentSecond .= $cS . '</section>';
    }
    foreach($aContentThird as $key => $cT) {
        if($cT !== '') {
            if($key !== count($aContentThird) - 1) {
                $sContentThird .= $cT . '</section>';
            } else {
                $sContentThird .= $cT;
            }
        }
    }

    $aFaqFirst = [];
    $aFaqSecond = [];
    $aFaqSections = explode("</p></div></div></div></div>", str_replace(['<section class="faq">', '</section>'], '', $page_faq));
    list($aFaqFirst, $aFaqSecond) = array_chunk($aFaqSections, ceil(count($aFaqSections)/2));
    $sFaqFirst = '';
    $sFaqSecond = '';

    foreach($aFaqFirst as $fF) {
        if($fF !== '') {
            $sFaqFirst .= $fF . '</p></div></div></div></div>';
        }
    }
    foreach($aFaqSecond as $key => $fS) {
        if($fS !== '') {
            if($key !== count($aFaqSecond) - 1) {
                $sFaqSecond .= $fS . '</p></div></div></div></div>';
            } else {
                $sFaqSecond .= $fS;
            }
        }
    }

    $englishH1 = $h1title;
    $isUpdateLanguage = false;

    if( isset($sitepress) && is_object($sitepress) ) {
        $languages = $sitepress->get_ls_languages();

        foreach($languages as $lang) {

            if($lang["translated_name"] == 'English') {
                continue;
            } elseif($lang["translated_name"] == '中文') {
                $currentLanguage = 'Chinese';
            } elseif($lang["translated_name"] == 'Português') {
                $currentLanguage = 'Portuguese';
            } elseif($lang["translated_name"] == 'Norwegian Bokmål') {
                $currentLanguage = 'Norwegian';
            } elseif($lang["translated_name"] == '中文 (台灣)') {
                $currentLanguage = 'Taiwanese(Mandarin)';
            } else {
                $currentLanguage = $lang["translated_name"];
            }

            $currentCode = $lang["code"];

            if($fullLanguage !== '' && $fullLanguage !== $currentCode) {
                continue;
            }

            $isUpdateLanguage = true;

            $pathLanguage = $uploads . "/wpallimport/files/generated-post-$currentLanguage.xml";

            $xw = xmlwriter_open_memory();
            xmlwriter_set_indent($xw, 1);
            $res = xmlwriter_set_indent_string($xw, ' ');
            xmlwriter_start_document($xw, '1.0', 'UTF-8');
            xmlwriter_start_element($xw, 'root');

                if($onlyFaq == 'false') {

                    $translate_meta = null;

                    do {
                        $meta = getTranslate($page_meta, $currentLanguage, $OPENAI_API_KEY);
                        if( isset($meta->choices) && !empty($meta->choices[0]) && isset($meta->choices[0]->message) && isset($meta->choices[0]->message->content) ) {
                            $translate_meta = $meta->choices[0]->message->content;
                        }
                    } while ( is_null($translate_meta) );
                    
                    $translate_title = null;

                    do {
                        $titlepage = getTranslate($page_title, $currentLanguage, $OPENAI_API_KEY);
                        if( isset($titlepage->choices) && !empty($titlepage->choices[0]) && isset($titlepage->choices[0]->message) && isset($titlepage->choices[0]->message->content) ) {
                            $translate_title = $titlepage->choices[0]->message->content;
                        }
                    } while ( is_null($translate_title) );

                    $translate_content_first = null;

                    do {
                        $content_first = getTranslate($sContentFirst, $currentLanguage, $OPENAI_API_KEY);
                        if( isset($content_first->choices) && !empty($content_first->choices[0]) && isset($content_first->choices[0]->message) && isset($content_first->choices[0]->message->content) ) {
                            $translate_content_first = $content_first->choices[0]->message->content;
                        }
                    } while ( is_null($translate_content_first) );

                    $translate_content_second = null;

                    do {
                        $content_second = getTranslate($sContentSecond, $currentLanguage, $OPENAI_API_KEY);
                        if( isset($content_second->choices) && !empty($content_second->choices[0]) && isset($content_second->choices[0]->message) && isset($content_second->choices[0]->message->content) ) {
                            $translate_content_second = $content_second->choices[0]->message->content;
                        }
                    } while ( is_null($translate_content_second) );

                    $translate_content_third = null;

                    do {
                        $content_third = getTranslate($sContentThird, $currentLanguage, $OPENAI_API_KEY);
                        if( isset($content_third->choices) && !empty($content_third->choices[0]) && isset($content_third->choices[0]->message) && isset($content_third->choices[0]->message->content) ) {
                            $translate_content_third = $content_third->choices[0]->message->content;
                        }
                    } while ( is_null($translate_content_third) );

                    $translate_h1title = null;

                    do {
                        $th1title = getTranslate($h1title, $currentLanguage, $OPENAI_API_KEY);
                        if( isset($th1title->choices) && !empty($th1title->choices[0]) && isset($th1title->choices[0]->message) && isset($th1title->choices[0]->message->content) ) {
                            $translate_h1title = $th1title->choices[0]->message->content;
                        }
                    } while ( is_null($translate_h1title) );

                    generateImgWithTitle($translate_h1title, $image_src, true, $currentLanguage, $englishH1);
                }

                $translate_faq_first = null;

                do {
                    $faq_first = getTranslate($sFaqFirst, $currentLanguage, $OPENAI_API_KEY);
                    if( isset($faq_first->choices) && !empty($faq_first->choices[0]) && isset($faq_first->choices[0]->message) && isset($faq_first->choices[0]->message->content) ) {
                        $translate_faq_first = $faq_first->choices[0]->message->content;
                    }
                } while ( is_null($translate_faq_first) );

                $translate_faq_second = null;

                do {
                    $faq_second = getTranslate($sFaqSecond, $currentLanguage, $OPENAI_API_KEY);
                    if( isset($faq_second->choices) && !empty($faq_second->choices[0]) && isset($faq_second->choices[0]->message) && isset($faq_second->choices[0]->message->content) ) {
                        $translate_faq_second = $faq_second->choices[0]->message->content;
                    }
                } while ( is_null($translate_faq_second) );

                $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/", "‘", "’", "'"], "-", $englishH1)));

                if($onlyFaq == 'true') {
                    $post_id = $_GET["postId"];
  
                    $type = apply_filters( 'wpml_element_type', get_post_type( $post_id ) );
                    $trid = apply_filters( 'wpml_element_trid', false, $post_id, $type );
                    
                    $translations = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );
                    foreach ( $translations as $lang => $translation ) {
                        
                        if($currentCode == $translation->language_code) {
                            $idLocale = $translation->element_id;
                            break;
                        }
                    }
                    $page_meta = get_field('page_meta', $idLocale);
                    $pageDataLocal = get_post($idLocale);
                    $page_title = $pageDataLocal->post_title;
                    $page_content = $pageDataLocal->post_content;
                    $page_faq = '<section class="faq">' . $translate_faq_first . $translate_faq_second . '</section>';
                    $h1title = get_field('h1title', $idLocale);
                } else {
                    $page_meta = $translate_meta;
                    $page_title = $translate_title;
                    $page_content = $translate_content_first . $translate_content_second . $translate_content_third;
                    $page_faq = '<section class="faq">' . $translate_faq_first . $translate_faq_second . '</section>';
                    $h1title = $translate_h1title;

                    if($apps_links == 'true') {

                        $aSoftUrls = getSoftUrlFromString($page_content);
            
                        if(!empty($aSoftUrls[0])) {
                            foreach($aSoftUrls[0] as $soft) {
            
                                $aUrl = getUrlsFromString($soft);
                                $softUrl = str_replace(["'", '"'], "", $aUrl[0][0]);
                                    
                                $language = $currentLanguage;
                                $screenTitleEnglish = str_replace(['/', '.', ':', '?', '='], '-', $softUrl) . '.jpg';
                                $screenTitle = str_replace(['/', '.', ':', '?', '='], '-', $softUrl) . $language . '.jpg';
                                $screenTitleOriginal = str_replace(['/', '.', ':', '?', '='], '-', $softUrl) . '-original.jpg';
                                // capture
                                $softTitle = str_replace(["<h2>", "</h2>", "<b>", "</b>"], "", replaceInvalidUrl($soft, $softUrl));

                                if(file_exists($uploads . '/ai/' . $screenTitleOriginal) && !file_exists($uploads . '/ai/' . $screenTitle)) {
                                
                                    generateImgWithTitle($softTitle, $uploads . '/ai/' . $screenTitleOriginal, true, $language, '', $domain_url, $softUrl);
            
                                    $posScreen = stripos($page_content, $screenTitle);
                                    if($posScreen === false) {
                                        $page_content = str_replace($screenTitleEnglish, $screenTitle, $page_content);
                                    }
                                }
                            }
                        }
                    }
                }

                xmlwriter_start_element($xw, 'page');
                    xmlwriter_start_element($xw, 'page_meta');
                        xmlwriter_text($xw, $page_meta);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_image');
                        $pos = stripos($page_image, $image_title . "-$currentLanguage");
                        if($pos === false) {
                            xmlwriter_text($xw, str_replace($image_title, $image_title . "-$currentLanguage", $page_image));
                        } else {
                            xmlwriter_text($xw, $page_image);
                        }
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_url');
                        xmlwriter_text($xw, $page_url);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_title');
                        xmlwriter_text($xw, $page_title);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_content');
                        $pos1 = stripos($page_content, $image_title . "-$currentLanguage");
                        if($pos1 === false) {
                            xmlwriter_text($xw, str_replace($image_title, $image_title . "-$currentLanguage", $page_content));
                        } else {
                            xmlwriter_text($xw, $page_content);
                        }
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'page_faq');
                        xmlwriter_text($xw, $page_faq);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'title');
                        xmlwriter_text($xw, $title);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'h1title');
                        xmlwriter_text($xw, $h1title);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'url');
                        xmlwriter_text($xw, is_array($url) ? '' : $url);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'url_descr');
                        xmlwriter_text($xw, is_array($url_descr) ? '' : $url_descr);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'anchor');
                        xmlwriter_text($xw, is_array($anchor) ? '' : $anchor);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'original_image');
                        xmlwriter_text($xw, $image_src);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'post_url');
                        xmlwriter_text($xw, $post_url);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'youtube_url');
                        xmlwriter_text($xw, is_array($youtube_url) ? '' : $youtube_url);
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
            $dom->save($pathLanguage);

            $fields = ['type' => 'file'];
            $fields['name'] = "generated-post-$currentLanguage.xml";

            $importID = null;

            foreach((new PMXI_Import_List())->getBy($fields)->toArray() as $import) {
                $importID = $import["id"];
            }

            $isImportDone = true;

            if(!is_null($importID)) {
                do {
                    try {
                        autoImport([$importID], $domain_url, $cron_job_key);
                        $isImportDone = true;
                    } catch (Exception $e) {
                        $isImportDone = false;
                    }
                } while (!$isImportDone);
            }
        }
    }

    if( isset($_GET["postId"]) ) {
        $stringsID = file_get_contents($file_queue);
        file_put_contents($file_queue, str_replace($_GET["postId"] . ',', '', $stringsID));
    }

    writeTimeGeneration($file_proccess, 'done');
    writeTimeGeneration($file, 'done');
}