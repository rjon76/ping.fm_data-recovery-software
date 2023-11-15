<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
ini_set('display_errors',1);
error_reporting(E_ALL);

$image_folder = __DIR__ . "/ai";

var_dump($_POST);
exit();

if (!is_dir($image_folder)) {
    mkdir($image_folder, 0755);
    echo "The directory $image_folder was successfully created.";
}

if ($_POST["title"]) {
    $theme_title = $_POST["title"];
}

if ($_POST["h1title"]) {
    $h1title = $_POST["h1title"];
}

if ($_POST["meta_title"]) {
    $theme_meta_title = $_POST["meta_title"];
}

if ($_POST["url"]) {
    $anchor_url = $_POST["url"];
}

if ($_POST["url_descr"]) {
    $url_description = $_POST["url_descr"];
}

if ($_POST["anchor"]) {
    $anchor_title = $_POST["anchor"];
}

if ($_POST["post_url"]) {
    $post_url = $_POST["post_url"];
}

if ($_POST["apikey"]) {
    $OPENAI_API_KEY = $_POST["apikey"];
}

if ($_POST["domain_url"]) {
    $domain_url = $_POST["domain_url"];
}

if ($_FILES['file'] && empty($_POST["file_url"])) {
    $moved = move_uploaded_file($_FILES["file"]["tmp_name"], $image_folder . '/' . str_replace(" ", '-', $_FILES["file"]["name"]));

    if( $moved ) {
        $image_src = $image_folder . '/' . str_replace(" ", '-', $_FILES["file"]["name"]);
    } else {
        $image_src = '';
        echo 'false';
        exit();
    }
}

if($_POST["file_url"]) {
    $image_src = $_POST["file_url"];
}

if( !$_POST["anchor"] || !$_POST["url"] || !$_POST["apikey"] ||
    !$_POST["title"] || !$_POST["meta_title"] || !$_POST["h1title"] ||
    !$_POST["url_descr"] || !$_POST["post_url"] ) {
    echo 'false';
    exit();
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

function imagettftextcenter($image, $size, $p, $x, $y, $color, $fontfile, $text){
	// Get height of single line
	$rect = imagettfbbox($size, 0, $fontfile, "Tq");
	$minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
	$maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
	$h1 = $maxY - $minY;
	
	// Get height of two lines
	$rect = imagettfbbox($size, 0, $fontfile, "Tq\nTq");
	$minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
	$maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
	$h2 = $maxY - $minY;
	
	// amount of padding that should be between each line
	$vpadding = ($h2 - $h1 - $h1) + 8;
	
	// calculate the dimensions of the text itself
	$frect = imagettfbbox($size, 0, $fontfile, $text);
	$minX = min(array($frect[0],$frect[2],$frect[4],$frect[6]));
	$maxX = max(array($frect[0],$frect[2],$frect[4],$frect[6]));
	$text_width = $maxX - $minX;
	$box = imagettfbbox($size, 0, $fontfile, $text);
    $text_height = abs($box[5]) - abs($box[3]);
    $image_height = imagesy($image);
    $yY = ($image_height + $text_height) / 2.1;
	$text = explode("\n", $text);
	foreach($text as $txt){
		$rect = imagettfbbox($size, 0, $fontfile, $txt);
		$minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
		$maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
		$minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
		$maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
		
		$width = $maxX - $minX;
		$height = $maxY - $minY; 
		
		$_x = $x;
		
		imagettftext($image, $size, 0, $_x, $y, $color, $fontfile, $txt);
		$y += 70;
	}
	
	return $rect;
}

function generateImgWithTitle($title, $image_src) {
    $capture        = imagecreatefromjpeg($image_src);
    $font_path      = __DIR__ . "/fonts/Inter-Bold.ttf";
    $save_file      = __DIR__ . "/ai" . '/' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";"], "-", $title))).'.jpg';
    $degrees = rand(-5, 5);
    $width = 1366;
    $height = 768;

    $imagesize = getimagesize($image_src);
    $w = $imagesize[0];
    $h = $imagesize[1];

    $w_div_h = $w / $h;  // пропорция

    $w_new = 1280;        // устанавливаем новую ширину
    $h_new = $w_new / $w_div_h;

    $transformX = rand(140, $w - $w_new - 140);
    $transformY = rand(140, $h - $h_new - 140);

    $bgColor = imagecolorallocatealpha($capture, 1, 1, 1, 127);
    $capture = imagerotate($capture, $degrees, $bgColor);

    $capture = imagecrop($capture, ['x' => $transformX, 'y' => $transformY, 'width' => $width, 'height' => $height]);

    # Add Title
    $color = imagecolorallocate($capture, 0x00, 0x00, 0x00);
    $white = imagecolorallocate($capture, 255, 255, 255);
    $rTitle = $title;
    //  $text = $title;
    $rTitle = ucwords($rTitle);
    $text = wordwrap($rTitle, 16, "\n");
    $strings = explode("\n", $text);
    $length = count($strings);
    if($length >=5 ) {
        $hSq = 530;

        if($length == 6) {
            $hSq = 600;  
        }
        if($length == 7) {
            $hSq = 670;  
        }
        if($length == 8) {
            $hSq = 800;
        }
        if($length == 9) {
            $hSq = 900;
        }
    } elseif($length == 4) {
        $hSq = 470;
    } elseif($length == 3) {
        $hSq = 350;
    } elseif($length == 2) {
        $hSq = 260;
    } else {
        $hSq = 160;
    }

    $cY = ($length * 70) - 105;

    $image = imagecreatetruecolor(400, 400);
    $black = imagecolorallocate($image, 0, 0, 0);

    imagefill($image, 0, 0, $black);
      
    imagecopyresampled($capture, $image, $width / 2.6, round(($height - $hSq) / 2), 0, 0, round($width - $width / 2.6), $hSq, 400, 400);
    imagettftextcenter($capture, 48, 0, $width / 2.1, round(($height - $cY) / 2), $white, $font_path, $text);

    # Save Image  
    imagejpeg($capture, $save_file, 70);
    imagedestroy($capture);
}

function getInfoTitle($title, $anchor_url, $anchor_title, $url_description, $OPENAI_API_KEY) {
    // Use these abstracts when creating an article: What are Microsoft Teams?, Access Teams, Exploring Teams, Personalizing Teams, Channels, Private chat, Calendar, Teams meetings, Summary;
    // direct links without anchors but include the address, such as https://www...
    $data = array(
        'model' => 'gpt-4-1106-preview',
        'messages' => [
            [
                "role" => "system",
                "content" => "MUST use JSON format response;
                            Generate 1500 words in-depth blog article about $title, make it seo friendly;
                            use reddit style posts and american english model;
                            Add $anchor_url with exact match for this $anchor_title, like <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a>;
                            Place the link anchor $anchor_title organically as a part of the content, surrounded by the link description to ensure it looks natural - $url_description;
                            Important - Do not add another name for link $anchor_url;
                            provide another relevant links with anchor/title such as https://www... or at your discretion in <a> tag with attributes rel='nofollow' target='_blank';
                            inside paragraphs extensively use '<b></b>' for topics, keywords;
                            do not use personal names or channel names;
                            do not insert links to images;
                            Important when mentioning URLs make them <a href> links.",
            ],
        ],
        'functions' => [
            [
                "name" => "article",
                'parameters' => [
                    "type" => "object",
                    "properties" => [
                        "intro" => [
                            "type" => "string",
                            "description" => "Introduction paragraph of the article. Important provide external hyperlinks for convenient user navigation",
                        ],
                        "scenario1" => [
                            "type" => "string",
                            "description" => "<h3>Common Scenario Description - extensively use emojis </h3>. Brief overviews of the solution inside <ul> and <li> tags. Don't use scenario word",
                        ],
                        "scenario2" => [
                            "type" => "string",
                            "description" => "<h3>Common Scenario Description - extensively use emojis </h3>. Brief overviews of the solution inside <ul> and <li> tags. Don't use scenario word",
                        ],
                        "scenario3" => [
                            "type" => "string",
                            "description" => "<h3>Common Scenario Description - extensively use emojis </h3>. Brief overviews of the solution inside <ul> and <li> tags. Don't use scenario word",
                        ],
                        "step1" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 1: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step2" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 2: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step3" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 3: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step4" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 4: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step5" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 5: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step6" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 6: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step7" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 7: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "step8" => [
                            "type" => "string",
                            "description" => "Step-by-Step Guide. <h3>Method 8: [Name of the Method] - extensively use emojis</h3> Steps with links to high authority relevant websites/apps in <ul> tag; <p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion or pros & cons</p>",
                        ],
                        "tips" => [
                            "type" => "array",
                            "description" => "Precautions and Tips. Important provide external hyperlinks for convenient user navigation. extensively use emojis only in title",
                            "items" => [
                                "type" => "string",
                                "description" => "Precautions and Tips description.",
                            ],
                        ],
                        "infotitle" => [
                            "type" => "string",
                            "description" => "Optimized title for block with text 1-2 words, not conclusion: added valuable information around 500 words at the end of article with links to high authority relevant websites",
                        ],
                        "information" => [
                            "type" => "string",
                            "description" => "add valuable information around 500 words at the end of article with links without anchors but include the address, such as https://www... in <a> tag with attributes rel='nofollow' target='_blank', to high authority relevant websites",
                        ],
                        "conclusion" => [
                            "type" => "string",
                            "description" => "Optimized conclusion of the article. Important provide external hyperlinks for convenient user navigation",
                        ],
                    ],
                    'required' => ["intro", "scenario1", "scenario2", "scenario3", "step1", "step2", "step3", "tips", "infotitle", "information", "conclusion"],
                ],
            ]
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

function emoji_to_entity($emoji) {
    if(is_numeric($emoji)) {
        return $emoji;
    }
    return sprintf(
        '&#%s;',
        unpack(
            'Ntgt',
            mb_convert_encoding($emoji, 'UTF-32', 'UTF-8')
        )['tgt']
    );
}

$emoji_regex = '/[\x{23}-\x{23}\x{2a}-\x{2a}\x{30}-\x{39}\x{a9}-\x{a9}\x{ae}-\x{ae}\x{200d}-\x{200d}\x{203c}-\x{203c}\x{2049}-\x{2049}\x{20e3}-\x{20e3}\x{2122}-\x{2122}\x{2139}-\x{2139}\x{2194}-\x{2199}\x{21a9}-\x{21aa}\x{231a}-\x{231b}\x{2328}-\x{2328}\x{2388}-\x{2388}\x{23cf}-\x{23cf}\x{23e9}-\x{23f3}\x{23f8}-\x{23fa}\x{24c2}-\x{24c2}\x{25aa}-\x{25ab}\x{25b6}-\x{25b6}\x{25c0}-\x{25c0}\x{25fb}-\x{25fe}\x{2600}-\x{2605}\x{2607}-\x{2612}\x{2614}-\x{2685}\x{2690}-\x{2705}\x{2708}-\x{2712}\x{2714}-\x{2714}\x{2716}-\x{2716}\x{271d}-\x{271d}\x{2721}-\x{2721}\x{2728}-\x{2728}\x{2733}-\x{2734}\x{2744}-\x{2744}\x{2747}-\x{2747}\x{274c}-\x{274c}\x{274e}-\x{274e}\x{2753}-\x{2755}\x{2757}-\x{2757}\x{2763}-\x{2767}\x{2795}-\x{2797}\x{27a1}-\x{27a1}\x{27b0}-\x{27b0}\x{27bf}-\x{27bf}\x{2934}-\x{2935}\x{2b05}-\x{2b07}\x{2b1b}-\x{2b1c}\x{2b50}-\x{2b50}\x{2b55}-\x{2b55}\x{3030}-\x{3030}\x{303d}-\x{303d}\x{3297}-\x{3297}\x{3299}-\x{3299}\x{fe0f}-\x{fe0f}\x{1f000}-\x{1f0ff}\x{1f10d}-\x{1f10f}\x{1f12f}-\x{1f12f}\x{1f16c}-\x{1f171}\x{1f17e}-\x{1f17f}\x{1f18e}-\x{1f18e}\x{1f191}-\x{1f19a}\x{1f1ad}-\x{1f1ff}\x{1f201}-\x{1f20f}\x{1f21a}-\x{1f21a}\x{1f22f}-\x{1f22f}\x{1f232}-\x{1f23a}\x{1f23c}-\x{1f23f}\x{1f249}-\x{1f53d}\x{1f546}-\x{1f64f}\x{1f680}-\x{1f6ff}\x{1f774}-\x{1f77f}\x{1f7d5}-\x{1f7ff}\x{1f80c}-\x{1f80f}\x{1f848}-\x{1f84f}\x{1f85a}-\x{1f85f}\x{1f888}-\x{1f88f}\x{1f8ae}-\x{1f8ff}\x{1f90c}-\x{1f93a}\x{1f93c}-\x{1f945}\x{1f947}-\x{1faff}\x{1fc00}-\x{1fffd}\x{e0020}-\x{e007f}]/u';
  
$xw = xmlwriter_open_memory();
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, ' ');
xmlwriter_start_document($xw, '1.0', 'UTF-8');
xmlwriter_start_element($xw, 'root');

    $a = getInfoTitle($theme_title, $anchor_url, $anchor_title, $url_description, $OPENAI_API_KEY);
    
    if( is_null( $a->choices[0]->message->function_call->arguments ) || 
        empty( $a->choices[0]->message->function_call->arguments ) ) {
            echo 'false';
            writeTimeGeneration($file, 0);
            exit();
    }
    $b = json_decode($a->choices[0]->message->function_call->arguments);

    $page_url = $post_url;

    $page_title = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $h1title
    );
    $page_intro = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->intro
    );
    $page_infomation = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->information
    );
    $page_infotitle = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->infotitle
    );
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
    if( !is_string($b->step1) ) {
        echo 'false';
        writeTimeGeneration($file, 0);
        exit();
    }
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
    $tips = $b->tips;
    // ================
    $page_conclusion = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->conclusion
    );
    
    $pageContent = '';
    $mainString = '<section itemscope="" itemtype="https://schema.org/FAQPage">';
    $contentString = '';
    $tipsString = '';
                        
    foreach($tips as $ke => $step) {
        $stepTmp = preg_replace_callback(
            $emoji_regex,
            function($a) { return emoji_to_entity($a[0]); },
            $step
        );
        $tipsString .= '<li>'.$stepTmp.'</li>';
    }

    $image_title = str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";"], "-", $h1title)));

    if(!$_POST["file_url"]) {
        generateImgWithTitle($h1title, $image_src);
    }

    $contentString .= '<article><section><h1>'.$page_title.'</h1><div class="inbrief"><div>
        <img src="' . $domain_url . '/wp-content/uploads/ai/'.$image_title.'.jpg" alt="'.$h1title.'" title="'.$h1title.'" width="1280" height="720">
        <p>'.$page_intro.'</p></div></div></section>
        <section><div><div>1</div><h2>Common Scenarios:</h2></div>'.$scenarious.'</section>
        <section><div><div>2</div><h2>Step-by-Step Guide:</h2></div>'.$steps.'</section>
        <section><div><div>3</div><h2>Precautions and Tips:</h2></div><ol>'.$tipsString.'</ol></section>
        <section><div><div>4</div><h2>'.$page_infotitle.'</h2></div><p>'.$page_infomation.'</p></section>
        <section><h2>Conclusion:</h2><div class="nonp">'.$page_conclusion.'</div></section>';

    $faq = getInfoFaq($theme_title, 10, $OPENAI_API_KEY);
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

    $mainString.='</section>';
    $pageContent .= $contentString . $mainString . "</article>";

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
    xmlwriter_end_element($xw);

xmlwriter_end_element($xw);
xmlwriter_end_document($xw);

$dom = new DOMDocument;
$dom->loadXML(xmlwriter_output_memory($xw));
$dom->save(__DIR__ . '/wpallimport/files/generated-post.xml');

$xw = xmlwriter_open_memory();
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, ' ');
xmlwriter_start_document($xw, '1.0', 'UTF-8');
xmlwriter_start_element($xw, 'root');

    xmlwriter_start_element($xw, 'page');
        xmlwriter_start_element($xw, 'title');
            xmlwriter_text($xw, $theme_title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'h1title');
            xmlwriter_text($xw, $h1title);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'meta_title');
            xmlwriter_text($xw, $theme_meta_title);
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
        xmlwriter_start_element($xw, 'post_url');
            xmlwriter_text($xw, $post_url);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'file');
            xmlwriter_text($xw, __DIR__ . "/ai/$image_title.jpg");
        xmlwriter_end_element($xw);
    xmlwriter_end_element($xw);

xmlwriter_end_element($xw);
xmlwriter_end_document($xw);

$dom = new DOMDocument;
$dom->loadXML(xmlwriter_output_memory($xw));
$dom->save(__DIR__ . '/last-article.xml');

function fetch_headers($url) {
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch); 
    curl_close($ch);
    return;
}

if(!$_POST["file_url"]) {
    unlink($image_src);
}

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing');

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing' );
