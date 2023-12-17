<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once( __DIR__ . "/env.php");

if ($_POST["tranlateUrl"]) {
    $tranlateUrl = $_POST["tranlateUrl"];
}

if(isset($_POST["onlyFaq"])) {
    if($_POST["onlyFaq"] == 'on') {
        $onlyFaq = 'true';
    } else {
        $onlyFaq = 'false';
    }
} else {
    $onlyFaq = 'false';
}

$file = __DIR__ . '/time_record.txt';
writeTimeGeneration($file, 'start');

$path = __DIR__ . '/wpallimport/files/generated-post.xml';
if(file_exists($path)) {
    $xmlstring = file_get_contents($path);
    $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $aArticles = json_decode($json, TRUE);
} else {
    $aArticles = [];
}

if(!empty($aArticles["page"]) && count($aArticles["page"]) > 0) {
    if(empty($aArticles["page"][1]) && empty($aArticles["page"][2]) &&
        (!empty($aArticles["page"]["title"]) && $tranlateUrl == $aArticles["page"]["page_url"])) {
            $page_meta = $aArticles["page"]["page_meta"];                
            $page_image = $aArticles["page"]["page_image"];
            $page_url = $aArticles["page"]["page_url"];
            $page_title = $aArticles["page"]["page_title"];
            $page_content = $aArticles["page"]["page_content"];
            $page_faq = $aArticles["page"]["page_faq"];
            $title = $aArticles["page"]["title"];
            $h1title = $aArticles["page"]["h1title"];
            $url = $aArticles["page"]["url"];
            $url_descr = $aArticles["page"]["url_descr"];
            $anchor = $aArticles["page"]["anchor"];
            $post_url = $aArticles["page"]["post_url"];
            if(empty($aArticles["page"]["youtube_url"])) {
                $youtube_url = '';
            } else {
                $youtube_url = $aArticles["page"]["youtube_url"];
            }
            $apps_links = $aArticles["page"]["apps_links"];
            $faq_theme = $aArticles["page"]["faq_theme"];
    }

    if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1]["title"])) {
        for($i = 0; $i < count($aArticles["page"]); $i++ ) {
            if($tranlateUrl === $aArticles["page"][$i]["page_url"]) {
                $page_meta = $aArticles["page"][$i]["page_meta"];                
                $page_image = $aArticles["page"][$i]["page_image"];
                $page_url = $aArticles["page"][$i]["page_url"];
                $page_title = $aArticles["page"][$i]["page_title"];
                $page_content = $aArticles["page"][$i]["page_content"];
                $page_faq = $aArticles["page"][$i]["page_faq"];
                $title = $aArticles["page"][$i]["title"];
                $h1title = $aArticles["page"][$i]["h1title"];
                $url = $aArticles["page"][$i]["url"];
                $url_descr = $aArticles["page"][$i]["url_descr"];
                $anchor = $aArticles["page"][$i]["anchor"];
                $post_url = $aArticles["page"][$i]["post_url"];
                if(empty($aArticles["page"][$i]["youtube_url"])) {
                    $youtube_url = '';
                } else {
                    $youtube_url = $aArticles["page"][$i]["youtube_url"];
                }
                $apps_links = $aArticles["page"][$i]["apps_links"];
                $faq_theme = $aArticles["page"][$i]["faq_theme"];
                break;
            }
        }
    }
}

$englishH1 = $h1title;

foreach($languages as $lang) {

    $path = __DIR__ . "/wpallimport/files/generated-post-$lang.xml";
    $copy = __DIR__ . "/wpallimport/files/generated-post-$lang-copy.xml";
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

        if($onlyFaq == 'false') {

            $translate_meta = null;

            do {
                $meta = getTranslate($page_meta, $lang, $OPENAI_API_KEY);
                if( isset($meta->choices) && !empty($meta->choices[0]) && isset($meta->choices[0]->message) && isset($meta->choices[0]->message->content) ) {
                    $translate_meta = $meta->choices[0]->message->content;
                }
            } while ( is_null($translate_meta) );
            
            $translate_title = null;

            do {
                $titlepage = getTranslate($page_title, $lang, $OPENAI_API_KEY);
                if( isset($titlepage->choices) && !empty($titlepage->choices[0]) && isset($titlepage->choices[0]->message) && isset($titlepage->choices[0]->message->content) ) {
                    $translate_title = $titlepage->choices[0]->message->content;
                }
            } while ( is_null($translate_title) );

            $translate_content = null;

            do {
                $content = getTranslate($page_content, $lang, $OPENAI_API_KEY);
                if( isset($content->choices) && !empty($content->choices[0]) && isset($content->choices[0]->message) && isset($content->choices[0]->message->content) ) {
                    $translate_content = $content->choices[0]->message->content;
                }
            } while ( is_null($translate_content) );

            $translate_title_2 = null;

            do {
                $title2 = getTranslate($title, $lang, $OPENAI_API_KEY);
                if( isset($title2->choices) && !empty($title2->choices[0]) && isset($title2->choices[0]->message) && isset($title2->choices[0]->message->content) ) {
                    $translate_title_2 = $title2->choices[0]->message->content;
                }
            } while ( is_null($translate_title_2) );

            $translate_h1title = null;

            do {
                $th1title = getTranslate($h1title, $lang, $OPENAI_API_KEY);
                if( isset($th1title->choices) && !empty($th1title->choices[0]) && isset($th1title->choices[0]->message) && isset($th1title->choices[0]->message->content) ) {
                    $translate_h1title = $th1title->choices[0]->message->content;
                }
            } while ( is_null($translate_h1title) );
        }

        $translate_faq = null;

        do {
            $faq = getTranslate($page_faq, $lang, $OPENAI_API_KEY);
            if( isset($faq->choices) && !empty($faq->choices[0]) && isset($faq->choices[0]->message) && isset($faq->choices[0]->message->content) ) {
                $translate_faq = $faq->choices[0]->message->content;
            }
        } while ( is_null($translate_faq) );

        $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";"], "-", $englishH1)));

        if(!empty($aArticles["page"]) && count($aArticles["page"]) > 0) {
            if(empty($aArticles["page"][1]) && empty($aArticles["page"][2]) &&
                (!empty($aArticles["page"]["title"]) && $tranlateUrl != $aArticles["page"]["page_url"])) {
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
            } else {

                if(!empty($aArticles["page"]["page_url"]) && $tranlateUrl == $aArticles["page"]["page_url"] && $onlyFaq == 'true') {
                    $page_meta = $aArticles["page"]["page_meta"];                
                    $page_image = $aArticles["page"]["page_image"];
                    $page_url = $aArticles["page"]["page_url"];
                    $page_title = $aArticles["page"]["page_title"];
                    $page_content = $aArticles["page"]["page_content"];
                    $page_faq = $translate_faq;
                    $title = $aArticles["page"]["title"];
                    $h1title = $aArticles["page"]["h1title"];
                    $url = $aArticles["page"]["url"];
                    $url_descr = $aArticles["page"]["url_descr"];
                    $anchor = $aArticles["page"]["anchor"];
                    $post_url = $aArticles["page"]["post_url"];
                    if(empty($aArticles["page"]["youtube_url"])) {
                        $youtube_url = '';
                    } else {
                        $youtube_url = $aArticles["page"]["youtube_url"];
                    }
                    $apps_links = $aArticles["page"]["apps_links"];
                    $faq_theme = $aArticles["page"]["faq_theme"];
                }
            }

            if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1])) {

                for($i = 0; $i < count($aArticles["page"]); $i++ ) {

                    if($tranlateUrl === $aArticles["page"][$i]["page_url"]) {

                        if($onlyFaq == 'true') {
                            $page_meta = $aArticles["page"][$i]["page_meta"];                
                            $page_image = $aArticles["page"][$i]["page_image"];
                            $page_url = $aArticles["page"][$i]["page_url"];
                            $page_title = $aArticles["page"][$i]["page_title"];
                            $page_content = $aArticles["page"][$i]["page_content"];
                            $page_faq = $translate_faq;
                            $title = $aArticles["page"][$i]["title"];
                            $h1title = $aArticles["page"][$i]["h1title"];
                            $url = $aArticles["page"][$i]["url"];
                            $url_descr = $aArticles["page"][$i]["url_descr"];
                            $anchor = $aArticles["page"][$i]["anchor"];
                            $post_url = $aArticles["page"][$i]["post_url"];
                            if(empty($aArticles["page"][$i]["youtube_url"])) {
                                $youtube_url = '';
                            } else {
                                $youtube_url = $aArticles["page"][$i]["youtube_url"];
                            }
                            $apps_links = $aArticles["page"][$i]["apps_links"];
                            $faq_theme = $aArticles["page"][$i]["faq_theme"];
                        }

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
                            xmlwriter_text($xw, !empty($aArticles["page"][$i]["page_faq"]) ? $aArticles["page"][$i]["page_faq"] : '');
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

        if($onlyFaq == 'false') {
            $page_meta = $translate_meta;
            $page_title = $translate_title;
            $page_content = $translate_content;
            $page_faq = $translate_faq;
            $title = $translate_title_2;
            $h1title = $translate_h1title;
        }

        xmlwriter_start_element($xw, 'page');
            xmlwriter_start_element($xw, 'page_meta');
                xmlwriter_text($xw, $page_meta);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'page_image');
                $pos = stripos($page_image, $image_title . "-$lang");
                if($pos === false) {
                    xmlwriter_text($xw, str_replace($image_title, $image_title . "-$lang", $page_image));
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
                $pos1 = stripos($page_content, $image_title . "-$lang");
                if($pos1 === false) {
                    xmlwriter_text($xw, str_replace($image_title, $image_title . "-$lang", $page_content));
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
    $dom->save(__DIR__ . "/wpallimport/files/generated-post-$lang.xml");
}

writeTimeGeneration($file, 'import');

sleep(5);

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=10&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=10&action=processing');

sleep(5);

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=10&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=10&action=processing' );

sleep(10);

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=11&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=11&action=processing');

sleep(5);

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=11&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=11&action=processing' );

sleep(10);

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=12&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=12&action=processing');

sleep(5);

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=12&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=12&action=processing' );

sleep(10);

writeTimeGeneration($file, 'done');