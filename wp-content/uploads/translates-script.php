<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once( __DIR__ . "/env.php");

if ($_POST["tranlateUrl"]) {
    $tranlateUrl = $_POST["tranlateUrl"];
}

$langQuant = '';

if ($_POST["langQuant"]) {
    $langQuant = (int)$_POST["langQuant"];
}

if($langQuant == 5) {
    sleep(60);
}

if ($_POST["domain_url"]) {
    $domain_url = $_POST["domain_url"];
}

$fullLanguage = '';

if ($_POST["fullLanguage"]) {
    $fullLanguage = $_POST["fullLanguage"];
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

$isPageHasTranslate = false;

$path = __DIR__ . '/wpallimport/files/generated-post-German.xml';

$xmlstring = file_get_contents($path);
$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$aArticles = json_decode($json, TRUE);

if(count($aArticles) > 0) {
    if(!empty($aArticles["page"]) && !empty($aArticles["page"]["post_url"])) {
        $post_url = $aArticles["page"]["post_url"];
        if($post_url === $tranlateUrl) {
            $isPageHasTranslate = true;
        }
    } else {
        if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][0]["post_url"]) && !empty($aArticles["page"][1]["post_url"])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {
                if(empty($aArticles["page"][$i]["post_url"])) { continue; }

                if($aArticles["page"][$i]["post_url"] === $tranlateUrl) {
                    $isPageHasTranslate = true;
                    break;
                }
            }
        }
    }
}

if(!$isPageHasTranslate && $onlyFaq !== 'false') {
    echo "not";
    exit();
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
            $image_src = $aArticles["page"]["original_image"];
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
                $image_src = $aArticles["page"][$i]["original_image"];
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

$aContentFirst = [];
$aContentSecond = [];
$aContentSections = explode("</section>", $page_content);
list($aContentFirst, $aContentSecond) = array_chunk($aContentSections, ceil(count($aContentSections)/2));
$sContentFirst = '';
$sContentSecond = '';

foreach($aContentFirst as $cF) {
    $sContentFirst .= $cF . '</section>';
}
foreach($aContentSecond as $key => $cS) {
    if($cS !== '') {
        if($key !== count($aContentSecond) - 1) {
            $sContentSecond .= $cS . '</section>';
        } else {
            $sContentSecond .= $cS;
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

foreach($languages as $key => $lang) {

    if($fullLanguage !== '' && $fullLanguage !== $lang) {
        continue;
    }

    if($langQuant == 0) {
        if($key > 4) {
            continue;
        }
    }

    if($langQuant == 5) {
        if($key < 5) {
            continue;
        }
    }

    $isUpdateLanguage = true;

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
                    $translate_meta = str_replace(["- $lang", "-$lang"], "", $meta->choices[0]->message->content);
                }
            } while ( is_null($translate_meta) );
            
            $translate_title = null;

            do {
                $titlepage = getTranslate($page_title, $lang, $OPENAI_API_KEY);
                if( isset($titlepage->choices) && !empty($titlepage->choices[0]) && isset($titlepage->choices[0]->message) && isset($titlepage->choices[0]->message->content) ) {
                    $translate_title = str_replace(["- $lang", "-$lang"], "", $titlepage->choices[0]->message->content);
                }
            } while ( is_null($translate_title) );

            $translate_content_first = null;

            do {
                $content_first = getTranslate($sContentFirst, $lang, $OPENAI_API_KEY);
                if( isset($content_first->choices) && !empty($content_first->choices[0]) && isset($content_first->choices[0]->message) && isset($content_first->choices[0]->message->content) ) {
                    $translate_content_first = str_replace(["- $lang", "-$lang"], "", $content_first->choices[0]->message->content);
                }
            } while ( is_null($translate_content_first) );

            $translate_content_second = null;

            do {
                $content_second = getTranslate($sContentSecond, $lang, $OPENAI_API_KEY);
                if( isset($content_second->choices) && !empty($content_second->choices[0]) && isset($content_second->choices[0]->message) && isset($content_second->choices[0]->message->content) ) {
                    $translate_content_second = str_replace(["- $lang", "-$lang"], "", $content_second->choices[0]->message->content);
                }
            } while ( is_null($translate_content_second) );

            $translate_h1title = null;

            do {
                $th1title = getTranslate($h1title, $lang, $OPENAI_API_KEY);
                if( isset($th1title->choices) && !empty($th1title->choices[0]) && isset($th1title->choices[0]->message) && isset($th1title->choices[0]->message->content) ) {
                    $translate_h1title = str_replace(["- $lang", "-$lang"], "", $th1title->choices[0]->message->content);
                }
            } while ( is_null($translate_h1title) );

            generateImgWithTitle($translate_h1title, $image_src, true, $lang, $englishH1);
        }

        $translate_faq_first = null;

        do {
            $faq_first = getTranslate($sFaqFirst, $lang, $OPENAI_API_KEY);
            if( isset($faq_first->choices) && !empty($faq_first->choices[0]) && isset($faq_first->choices[0]->message) && isset($faq_first->choices[0]->message->content) ) {
                $translate_faq_first = str_replace(["- $lang", "-$lang"], "", $faq_first->choices[0]->message->content);
            }
        } while ( is_null($translate_faq_first) );

        $translate_faq_second = null;

        do {
            $faq_second = getTranslate($sFaqSecond, $lang, $OPENAI_API_KEY);
            if( isset($faq_second->choices) && !empty($faq_second->choices[0]) && isset($faq_second->choices[0]->message) && isset($faq_second->choices[0]->message->content) ) {
                $translate_faq_second = str_replace(["- $lang", "-$lang"], "", $faq_second->choices[0]->message->content);
            }
        } while ( is_null($translate_faq_second) );

        $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/"], "-", $englishH1)));

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
            } else {

                if(!empty($aArticles["page"]["page_url"]) && $tranlateUrl == $aArticles["page"]["page_url"] && $onlyFaq == 'true') {
                    $page_meta = $aArticles["page"]["page_meta"];                
                    $page_image = $aArticles["page"]["page_image"];
                    $page_url = $aArticles["page"]["page_url"];
                    $page_title = $aArticles["page"]["page_title"];
                    $page_content = $aArticles["page"]["page_content"];
                    $page_faq = '<section class="faq">' . $translate_faq_first . $translate_faq_second . '</section>';
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
                            $page_faq = '<section class="faq">' . $translate_faq_first . $translate_faq_second . '</section>';
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

        if($onlyFaq == 'false') {
            $page_meta = $translate_meta;
            $page_title = $translate_title;
            $page_content = $translate_content_first . $translate_content_second;
            $page_faq = '<section class="faq">' . $translate_faq_first . $translate_faq_second . '</section>';
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
    $dom->save(__DIR__ . "/wpallimport/files/generated-post-$lang.xml");

    sleep(5);

    if($lang == 'French') {
        autoImport([11], $domain_url);
    }
    if($lang == 'German') {
        autoImport([10], $domain_url);
    }
    if($lang == 'Spanish') {
        autoImport([12], $domain_url);
    }
    if($lang == 'Italian') {
        autoImport([13], $domain_url);
    }
    if($lang == 'Japanese') {
        autoImport([14], $domain_url);
    }
    if($lang == 'Portuguese') {
        autoImport([15], $domain_url);
    }
    if($lang == 'Dutch') {
        autoImport([16], $domain_url);
    }
    if($lang == 'Arabic') {
        autoImport([17], $domain_url);
    }
    if($lang == 'Chinese') {
        autoImport([18], $domain_url);
    }
    if($lang == 'Swedish') {
        autoImport([19], $domain_url);
    }
}

if($fullLanguage !== '' && $isUpdateLanguage) {
    writeTimeGeneration($file, 'done');
}

if($fullLanguage === '') {
    writeTimeGeneration($file, 'done');
}