<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once( __DIR__ . "/env.php");

$pageContent = '';
$pageFaq = '';

if ($_POST["pageContent"]) {
    $pageContent = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $_POST["pageContent"]
    );
}

if ($_POST["pageFaq"]) {
    $pageFaq = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $_POST["pageFaq"]
    );
}

if ($_POST["themeFaq"]) {
    $themeFaq = $_POST["themeFaq"];
}

$path = __DIR__ . '/wpallimport/files/generated-post.xml';
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

    if(!empty($aArticles["page"]) && empty($aArticles["page"][1]) && empty($aArticles["page"][2])) {
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
                xmlwriter_text($xw, $pageContent);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'page_faq');
                xmlwriter_text($xw, $pageFaq);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'title');
                xmlwriter_text($xw, $aArticles["page"]["title"]);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'h1title');
                xmlwriter_text($xw, $aArticles["page"]["h1title"]);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'url');
                xmlwriter_text($xw, $aArticles["page"]["url"]);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'url_descr');
                xmlwriter_text($xw, $aArticles["page"]["url_descr"]);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'anchor');
                xmlwriter_text($xw, $aArticles["page"]["anchor"]);
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

    if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1])) {
        for($i = 0; $i < count($aArticles["page"]); $i++ ) {

            if($themeFaq == $aArticles["page"][$i]["faq_theme"]) {
                $content = $pageContent;
                $faq = $pageFaq;
            } else {
                $content = $aArticles["page"][$i]["page_content"];
                $faq = $aArticles["page"][$i]["page_faq"] ?? '';
                $faq = is_array($faq) ? '' : $faq;
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
                    xmlwriter_text($xw, $content);
                xmlwriter_end_element($xw);
                xmlwriter_start_element($xw, 'page_faq');
                    xmlwriter_text($xw, $faq);
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

xmlwriter_end_element($xw);
xmlwriter_end_document($xw);

$dom = new DOMDocument;
$dom->loadXML(xmlwriter_output_memory($xw));
$dom->save(__DIR__ . '/wpallimport/files/generated-post.xml');

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing');

sleep(5);

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing' );

sleep(20);