<?php


if ($_POST["remove_url"]) {
    $postUrl = $_POST["remove_url"];
} else {
    return 'false';
    exit();
}

if ($_POST["domain_url"]) {
    $domain_url = $_POST["domain_url"];
}

require_once( __DIR__ . "/env.php");

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

    if(count($aArticles["page"]) > 0) {

        if(count($aArticles["page"]) > 1 && !empty($aArticles["page"][0]["title"]) && !empty($aArticles["page"][1]["title"])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {

                if($postUrl === $aArticles["page"][$i]["page_url"]) {
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

xmlwriter_end_element($xw);
xmlwriter_end_document($xw);

$dom = new DOMDocument;
$dom->loadXML(xmlwriter_output_memory($xw));
$dom->save(__DIR__ . '/wpallimport/files/generated-post.xml');


foreach($languages as $lang) {

    $path = __DIR__ . "/wpallimport/files/generated-post-$lang.xml";

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

        if($postUrl !== $aArticles["page"]["page_url"]) {
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
    }

    if(count($aArticles["page"]) > 0) {

        if(count($aArticles["page"]) > 1 && !empty($aArticles["page"][0]["title"]) && !empty($aArticles["page"][1]["title"])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {

                if($postUrl === $aArticles["page"][$i]["page_url"]) {
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

    xmlwriter_end_element($xw);
    xmlwriter_end_document($xw);


    $dom = new DOMDocument;
    $dom->loadXML(xmlwriter_output_memory($xw));
    $dom->save(__DIR__ . "/wpallimport/files/generated-post-$lang.xml");
}

autoImport([4, 10, 11, 12], $domain_url);
sleep(30);
autoImport([13, 14, 15, 16], $domain_url);
sleep(30);
autoImport([17, 18, 19], $domain_url);