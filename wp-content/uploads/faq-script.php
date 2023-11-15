<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
ini_set('display_errors',1);
error_reporting(E_ALL);


if ($_POST["numberFaq"]) {
    $numberFaq = $_POST["numberFaq"];
}

if ($_POST["apikey"]) {
    $OPENAI_API_KEY = $_POST["apikey"];
}

$file = __DIR__ . '/time_record.txt';
function writeTimeGeneration($path_to_file, $seconds) {
    if(file_exists($path_to_file)) {
        $current = (int)file_get_contents($path_to_file);

        if(time() > $current) {
            file_put_contents($path_to_file, time() + $seconds);
        }
    } else {
        file_put_contents($path_to_file, time());
    }
}

writeTimeGeneration($file, 120);

function getInfoFaq($title, $number, $OPENAI_API_KEY) {

    $data = array(
        'model' => 'gpt-4-1106-preview',
        'messages' => [
            [
                "role" => "system",
                "content" => "Generate a set of $number related to this keyword questions and $number their corresponding answers for $title.
                            write about $title, not only about soft;
                            provide 1-2 another external hyperlinks only on homepages for convenient user navigation, with attributes rel='nofollow' target='_blank';
                            MUST be $number related questions and $number their corresponding answers.
                            Question ONLY inside once <p> and answer ONLY inside once <p> without QUESTION and ANSWER words and without Q and A letters, and don't numeric.
                            A fully rewritten unique original content that can't be identified by artificial intelligence or plagiarism detection tools.
                            When outlining procedures or services, always provide external hyperlinks for convenient user navigation.
                            Inside paragraphs extensively use '<b></b>' for topics, keywords and objects of actions;
                            use American English;
                            do not use personal names or channel names;
                            do not insert links to images;
                            when mentioning URLs make them <a href> links",
            ],
        ],
    );

    $post_json = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = "Authorization: Bearer $OPENAI_API_KEY";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    return json_decode($result);

    curl_close($ch);
}

$path = __DIR__ . '/wpallimport/files/generated-post.xml';
$xmlstring = file_get_contents($path);
$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json, TRUE);
  
$xw = xmlwriter_open_memory();
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, ' ');
xmlwriter_start_document($xw, '1.0', 'UTF-8');
xmlwriter_start_element($xw, 'root');

    $mainString = '<section itemscope="" itemtype="https://schema.org/FAQPage">';
    $page_content = $array["page"]["page_content"];
    $aContent = explode('<section itemscope="" itemtype="https://schema.org/FAQPage">', $array["page"]["page_content"]);

    $faq = getInfoFaq($theme_title, $numberFaq, $OPENAI_API_KEY);
    $page_faq = $faq->choices[0]->message->content;
    $faqParag = explode('<p>', $page_faq);
    $faqNoParag = str_replace(["<p>", "</p>", "</section>", '"', "</article>"], '', $faqParag);
    foreach($faqNoParag as $key => $p) {
        if($key === 0) continue;
                    
        if($key%2==1) {
            $mainString .= '<div class="panel" itemprop="mainEntity" itemscope="" itemtype="https://schema.org/Question"><div class="toggle-link"><h3 class="panel-title" itemprop="name">
                '.trim($p).'</h3></div><div class="panel-collapse"><div class="panel-body" itemprop="acceptedAnswer" itemscope="" itemtype="https://schema.org/Answer"><div itemprop="text">';
        } else {
            $mainString .= '<p>'.trim($p).'</p></div></div></div></div>';
        }
    }

    $pageContent = $aContent[0] . $mainString . $aContent[1];

    xmlwriter_start_element($xw, 'page');
        xmlwriter_start_element($xw, 'page_meta');
            xmlwriter_text($xw, $array["page"]["page_meta"]);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_image');
            xmlwriter_text($xw, $array["page"]["page_image"]);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_url');
            xmlwriter_text($xw, $array["page"]["page_url"]);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_title');
            xmlwriter_text($xw, $array["page"]["page_title"]);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'page_content');
            xmlwriter_text($xw, $pageContent);
        xmlwriter_end_element($xw);
    xmlwriter_end_element($xw);

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
