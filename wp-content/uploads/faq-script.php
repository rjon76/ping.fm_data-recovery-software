<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once( __DIR__ . "/env.php");

if(isset($_POST["regenerate-faq"])) {
    if($_POST["regenerate-faq"] == 'on') {
        $regenerate_faq = 'true';
    } else {
        $regenerate_faq = 'false';
    }
} else {
    $regenerate_faq = 'false';
}

if ($_POST["numberFaq"]) {
    $numberFaq = $_POST["numberFaq"];
}

if ($_POST["faqLastTheme"]) {
    $themeFaq = $_POST["faqLastTheme"];
}

if ($_POST["faqPostUrl"]) {
    $faqPostUrl = $_POST["faqPostUrl"];
}

$file = __DIR__ . '/time_record.txt';

writeTimeGeneration($file, 'faq');

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

    $mainString = $regenerate_faq == 'false' ? '<h2>FAQ</h2>' : '<section class="faq"><h2>FAQ</h2>';

    if($regenerate_faq == 'false') {
        if(!empty($aArticles["page"]) && empty($aArticles["page"][1]) && empty($aArticles["page"][2])) {
            $page_content = $aArticles["page"]["page_faq"];
        }

        if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {
                if($faqPostUrl == $aArticles["page"][$i]["page_url"]) {
                    $page_content = $aArticles["page"][$i]["page_faq"];
                    break;
                }
            }
        }

        $aContent = explode('<h2>FAQ</h2>', $page_content);
    }

    $page_faq = null;

    do {
        $faq = getInfoFaq($themeFaq, $numberFaq, $OPENAI_API_KEY);
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
            $mainString .= '<div class="panel"><div class="toggle-link"><h3 class="panel-title">
                '.trim($p).'</h3></div><div class="panel-collapse"><div class="panel-body"><div>';
        } else {
            $mainString .= '<p>'.trim($p).'</p></div></div></div></div>';
        }
    }

    $pageContent = $regenerate_faq == 'false' ? $aContent[0] . $mainString . $aContent[1] : $mainString . '</section>';

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
                xmlwriter_text($xw, $aArticles["page"]["page_content"]);
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'page_faq');
                xmlwriter_text($xw, $pageContent);
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

    if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1])) {
        for($i = 0; $i < count($aArticles["page"]); $i++ ) {

            if($faqPostUrl == $aArticles["page"][$i]["page_url"]) {
                $content = $pageContent;
            } else {
                $content = $aArticles["page"][$i]["page_faq"];
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
                    xmlwriter_text($xw, $content);
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

xmlwriter_end_element($xw);
xmlwriter_end_document($xw);

$dom = new DOMDocument;
$dom->loadXML(xmlwriter_output_memory($xw));
$dom->save(__DIR__ . '/wpallimport/files/generated-post.xml');

autoImport([4]);

writeTimeGeneration($file, 'done');