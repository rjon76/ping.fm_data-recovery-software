<?php


if ($_POST["remove_title"]) {
    $theme_title = $_POST["remove_title"];
} else {
    return 'false';
    exit();
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

    if(count($aArticles["page"]) > 0) {

        if(count($aArticles["page"]) > 1 && !empty($aArticles["page"][0]["title"]) && !empty($aArticles["page"][1]["title"])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {

                if($theme_title === $aArticles["page"][$i]["title"]) {
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
                    xmlwriter_start_element($xw, 'title');
                        xmlwriter_text($xw, $aArticles["page"][$i]["title"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'h1title');
                        xmlwriter_text($xw, $aArticles["page"][$i]["h1title"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'url');
                        xmlwriter_text($xw, $aArticles["page"][$i]["url"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'url_descr');
                        xmlwriter_text($xw, $aArticles["page"][$i]["url_descr"]);
                    xmlwriter_end_element($xw);
                    xmlwriter_start_element($xw, 'anchor');
                        xmlwriter_text($xw, $aArticles["page"][$i]["anchor"]);
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

function fetch_headers($url) {
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch); 
    curl_close($ch);
    return;
}

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing');

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing' );

sleep(10);

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing');

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing' );