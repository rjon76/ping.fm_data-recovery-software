<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 240);
set_time_limit(240);
ini_set('display_errors',1);
error_reporting(E_ALL);

$part1 = 'sk-';
$part2 = 'w72LW8bySt9XV';
$part3 = '9wfD1YjT3Blbk';
$part4 = 'FJHpJFuE4XU';
$part5 = 'uqUVWrXOLQf';

$image_folder = __DIR__ . "/ai";

if (!is_dir($image_folder)) {
    mkdir($image_folder, 0755);
    echo "The directory $image_folder was successfully created.";
}

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

if ($_POST["domain_url"]) {
    $domain_url = $_POST["domain_url"];
}

if($_POST["faq_theme"]) {
    $faq_theme = $_POST["faq_theme"];
}

if (!empty($_FILES['file']["tmp_name"])) {
    $moved = move_uploaded_file($_FILES["file"]["tmp_name"], $image_folder . '/' . str_replace(" ", '-', $_FILES["file"]["name"]));

    if( $moved ) {
        $image_src = $image_folder . '/' . str_replace(" ", '-', $_FILES["file"]["name"]);
    }
}

if(empty($_FILES['file']["tmp_name"]) && $faq_theme) {
    $image_src = __DIR__ . "/ai" . '/' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";"], "-", $faq_theme))).'.jpg';
}

if( !$_POST["anchor"] || !$_POST["url"] ||
    !$_POST["title"] || !$_POST["meta_title"] || !$_POST["h1title"] ||
    !$_POST["url_descr"] || !$_POST["post_url"] || !$_POST["faq_theme"] ) {
    echo 'false';
    exit();
}

$OPENAI_API_KEY = $part1.$part2.$part3.$part4.$part5;

$file = __DIR__ . '/time_record.txt';

function writeTimeGeneration($path_to_file, $action) {
    file_put_contents($path_to_file, $action);
}

writeTimeGeneration($file, 'start');

function fetch_headers($url) {
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch); 
    curl_close($ch);
    return;
}

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

function generateImgWithTitle($title, $image_src, $isAi = false) {
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

    if(!$isAi) {
        $capture = imagerotate($capture, $degrees, $bgColor);
        $capture = imagecrop($capture, ['x' => $transformX, 'y' => $transformY, 'width' => $width, 'height' => $height]);
    }

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

function generateImageDall3($title, $OPENAI_API_KEY) {

    $data = array(
        'model' => 'dall-e-3',
        'prompt' => "Create an image in a simple 3D cartoon style that represents the concept of $title. The image should visually depict the idea without any use of text or captions. It could include elements  and visual metaphors for $title, but should not contain any written words",
        'n' => 1,
        'quality' => 'hd',
        'size' => "1792x1024",
    );

    $post_json = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
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

function saveAiImage($url, $path) {
    $image = imagecreatefrompng($url);
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));

    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    $w = imagesx($image);
    $h = imagesy($image);

    imagedestroy($image);

    $w_div_h = $w / $h;
    $w_new = 1280;
    $h_new = $w_new / $w_div_h;

    $capture33 = imagecreatetruecolor($w_new, $h_new);
    imagecopyresampled($capture33, $bg, 0, 0, 0, 0, $w_new, $h_new, $w, $h);

    $quality = 70;
    imagejpeg($capture33, $path, $quality);
    imagedestroy($capture33);
}

function getInfoTitle($title, $anchor_url, $anchor_title, $url_description, $apps_links, $OPENAI_API_KEY) {
    
    if($apps_links == 'true') {
        $stepString1 = '<h2><a href="url to soft/app" rel="nofollow" target="_blank">Soft/Application Title1</a></h2><p>Soft/Application Description1 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString2 = '<h2><a href="url to soft/app" rel="nofollow" target="_blank">Soft/Application Title2</a></h2><p>Soft/Application Description2 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString3 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title3</a></h2><p>Soft/Application Description3 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString4 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title4</a></h2><p>Soft/Application Description4 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString5 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title5</a></h2><p>Soft/Application Description5 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString6 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title6</a></h2><p>Soft/Application Description6 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString7 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title7</a></h2><p>Soft/Application Description7 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString8 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title8</a></h2><p>Soft/Application Description8 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString9 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title9</a></h2><p>Soft/Application Description9 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString10 = '<h2><a href="url to web/app" rel="nofollow" target="_blank">Soft/Application Title10</a></h2><p>Soft/Application Description10 80-120 words</p><a href="url to soft/app" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $information_string = "Add valuable information, around 500 words with html formats in <p> tags; Add <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a> and add additional relevant links. These should be nofollow links in the format: <a href='[URL]' rel='nofollow' target='_blank'>[URL]</a>";
        $prompt = "Write a 1500-word blog article about $title from a personal perspective, as if you have firsthand experience with them. The article should:
                • Present balanced opinions, avoiding overt promotion of the apps.
                • Be optimized for search engines, engaging for readers, and written in American English.
                • Be original and unique, ensuring it won't be flagged as copied or similar by plagiarism detection tools.
                Include specific instructions for hyperlinking within the article:
                • Add a dofollow link to a specified URL ($anchor_url) using the exact text of $anchor_title in the format: <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a>. Apply this rule just once for each article.
                • Integrate this link naturally into the content, accompanied by a relevant description ($url_description).
                • Do not refer to the link URL ($anchor_url) by any other name.
                • Add additional relevant links. These should be nofollow links in the format: <a href='[URL]' rel='nofollow' target='_blank'>[anchor/title]</a>.
                • Include a hyperlink (<a href>) to relevant websites or applications at each step of the methods described.
                • Emphasize key topics and keywords by enclosing them in bold tags ('<b></b>').
                • Avoid using personal or channel names.
                • Do not include links to images.
                • When mentioning URLs, format them as clickable links (<a href>).";
    } else {
        $stepString1 = 'Step-by-Step Guide with HTML formats. <h3>Method 1: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString2 = 'Step-by-Step Guide with HTML formats. <h3>Method 2: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString3 = 'Step-by-Step Guide with HTML formats. <h3>Method 3: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString4 = 'Step-by-Step Guide with HTML formats. <h3>Method 4: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString5 = 'Step-by-Step Guide with HTML formats. <h3>Method 5: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString6 = 'Step-by-Step Guide with HTML formats. <h3>Method 6: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString7 = 'Step-by-Step Guide with HTML formats. <h3>Method 7: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString8 = 'Step-by-Step Guide with HTML formats. <h3>Method 8: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString9 = 'Step-by-Step Guide with HTML formats. <h3>Method 9: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString10 = 'Step-by-Step Guide with HTML formats. <h3>Method 10: [Name of the Method] with 1 emoji</h3> <ul>3-5 steps inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $information_string = "Add valuable information, around 500 words with html formats in <p> tags; Add <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a> and add additional relevant links. These should be nofollow links in the format: <a href='[URL]' rel='nofollow' target='_blank'>[URL]</a>";
        $prompt = "Write a 1500-word detailed, how-to style article about a specific topic (referred to as $title).
                    The article should read as though it's written from a personal experience, detailing various methods and steps you've supposedly used.
                    Ensure the content is original, unique, and SEO-friendly, using American English.
                    Incorporate the provided link ($anchor_url) with its exact title ($anchor_title) in a natural, contextual manner within the article, accompanied by a relevant description ($url_description).
                    The link should be formatted as <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a>, and should blend seamlessly into the content. Apply this rule just once for each article.
                    Avoid using alternative names for this link. Additionally must include other relevant high-authority links as needed, with the format <a href='[URL]' rel='nofollow' target='_blank'>[anchor/title]</a>.
                    For each method described, insert a corresponding link where appropriate. Emphasize key topics and keywords using bold text (<b></b>).
                    Refrain from using personal or channel names, and do not include links to images. When referencing URLs, always use the <a href> format.";
    }
    // use reddit style posts and american english model;
    $data = array(
        'model' => 'gpt-4-1106-preview',
        'messages' => [
            [
                "role" => "system",
                "content" => "MUST use JSON format response. $prompt",
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
                            "description" => "Scenario1 with HTML formats.<h3>Common Scenario Description with 1 emoji</h3>. <ul>2-5 Brief overviews of the solution inside <li></li></ul>. Don't use scenario word",
                        ],
                        "scenario2" => [
                            "type" => "string",
                            "description" => "Scenario2 with HTML formats.<h3>Common Scenario Description with 1 emoji</h3>. <ul>2-5 Brief overviews of the solution inside <li></li></ul>. Don't use scenario word",
                        ],
                        "scenario3" => [
                            "type" => "string",
                            "description" => "Scenario3 with HTML formats.<h3>Common Scenario Description with 1 emoji</h3>. <ul>2-5 Brief overviews of the solution inside <li></li></ul>. Don't use scenario word",
                        ],
                        "step1" => [
                            "type" => "string",
                            "description" => $stepString1,
                        ],
                        "step2" => [
                            "type" => "string",
                            "description" => $stepString2,
                        ],
                        "step3" => [
                            "type" => "string",
                            "description" => $stepString3,
                        ],
                        "step4" => [
                            "type" => "string",
                            "description" => $stepString4,
                        ],
                        "step5" => [
                            "type" => "string",
                            "description" => $stepString5,
                        ],
                        "step6" => [
                            "type" => "string",
                            "description" => $stepString6,
                        ],
                        "step7" => [
                            "type" => "string",
                            "description" => $stepString7,
                        ],
                        "step8" => [
                            "type" => "string",
                            "description" => $stepString8,
                        ],
                        "step9" => [
                            "type" => "string",
                            "description" => $stepString9,
                        ],
                        "step10" => [
                            "type" => "string",
                            "description" => $stepString10,
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
                            "description" => $information_string,
                        ],
                        "conclusion" => [
                            "type" => "string",
                            "description" => "Optimized conclusion of the article. Important provide external hyperlinks for convenient user navigation",
                        ],
                    ],
                    'required' => ["intro", "scenario1", "scenario2", "scenario3", "step1", "step2", "step3", "step4", "step5", "tips", "infotitle", "information", "conclusion"],
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
    if($emoji == 3 || $emoji == 2) {
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

    $b = null;

    do {
        $a = getInfoTitle($theme_title, $anchor_url, $anchor_title, $url_description, $apps_links, $OPENAI_API_KEY);
        if( isset($a->choices[0]->message->function_call) ) {
            $b = json_decode($a->choices[0]->message->function_call->arguments);
        }
    } while ( is_null($b) || is_null($b->step1) );

    writeTimeGeneration($file, 'faq');

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
    $tips = isset($b->tips) ? $b->tips : '';
    // ================
    $page_conclusion = preg_replace_callback(
        $emoji_regex,
        function($a) { return emoji_to_entity($a[0]); },
        $b->conclusion
    );
    
    $pageContent = '';
    $mainString = '<section class="faq" itemscope="" itemtype="https://schema.org/FAQPage"><h2>FAQ</h2>';
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

    if(empty($_FILES['file']["tmp_name"])) {
        $gen_image_src = null;

        do {
            $gen_image = generateImageDall3($faq_theme, $OPENAI_API_KEY);
            if( isset($gen_image->data[0]->url) ) {
                $gen_image_src = $gen_image->data[0]->url;
            }
        } while ( is_null($gen_image_src) );

        saveAiImage($gen_image_src, $image_src);
        generateImgWithTitle($h1title, $image_src, true);
    }

    if($moved) {
        generateImgWithTitle($h1title, $image_src);
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
            $videoString = '<section><h2>Youtube video to watch</h2><div class="nonp iframe">' . 
            preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $youtube_url)
                . '</div></section>';
        }

        $contentString .= '<article><section><h1>'.$page_title.'</h1><div class="inbrief"><div>
            <img src="' . $domain_url . '/wp-content/uploads/ai/'.$image_title.'.jpg" alt="'.$h1title.'" title="'.$h1title.'" width="1280" height="720">
            <p>'.$page_intro.'</p></div></div></section>
            <section><div class="medal"><div>&#129351;</div></div>'.$page_step1.'</section>
            <section><div class="medal"><div>&#129352;</div></div>'.$page_step2.'</section>
            <section><div class="medal"><div>&#129353;</div></div>'.$page_step3.'</section>'
            . $st4 . $st5 . $st6 . $st7 . $st8 . $st9 . $st10 . $videoString . '
            <section><h2>Conclusion:</h2><div class="nonp">'.$page_infomation.'</div></section>';

    } else {

        if(empty($youtube_url)) {
            $string = '<section><div><div>3</div><h2>Precautions and Tips:</h2></div><ol>'.$tipsString.'</ol></section>
                        <section><div><div>4</div><h2>'.$page_infotitle.'</h2></div>'.$page_infomation.'</section>
                        <section><h2>Conclusion:</h2><div class="nonp">'.$page_conclusion.'</div></section>';
        } else {
            $string = '<section><div><div>3</div><h2>Youtube video to watch</h2></div><div class="iframe">' . 
                            preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $youtube_url)
                        . '</div></section>
                        <section><div><div>4</div><h2>Precautions and Tips:</h2></div><ol>'.$tipsString.'</ol></section>
                        <section><div><div>5</div><h2>'.$page_infotitle.'</h2></div>'.$page_infomation.'</section>
                        <section><h2>Conclusion:</h2><div class="nonp">'.$page_conclusion.'</div></section>';
        }
    
        $contentString .= '<article><section><h1>'.$page_title.'</h1><div class="inbrief"><div>
            <img src="' . $domain_url . '/wp-content/uploads/ai/'.$image_title.'.jpg" alt="'.$h1title.'" title="'.$h1title.'" width="1280" height="720">
            <p>'.$page_intro.'</p></div></div></section>
            <section><div><div>1</div><h2>Common Scenarios:</h2></div>'.$scenarious.'</section>
            <section class="stepByStep"><div><div>2</div><h2>Step-by-Step Guide:</h2></div>'.$steps.'</section>' . $string;

    }

    $page_faq = null;

    do {
        $faq = getInfoFaq($faq_theme, 10, $OPENAI_API_KEY);
        if( isset($faq->choices[0]->message) ) {
            $page_faq = $faq->choices[0]->message->content;
        }
    } while ( is_null($page_faq) );

    writeTimeGeneration($file, 'import');
    
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

    if(!empty($aArticles["page"]) && count($aArticles["page"]) > 0) {
        if(empty($aArticles["page"][1]) && empty($aArticles["page"][2]) &&
            (!empty($aArticles["page"]["title"]) && $theme_title != $aArticles["page"]["title"])) {
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

        if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][1]["title"])) {
            for($i = 0; $i < count($aArticles["page"]); $i++ ) {

                if($theme_title === $aArticles["page"][$i]["title"]) {
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
$dom->save(__DIR__ . '/wpallimport/files/generated-post.xml');

unlink($image_src);

fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger');
fetch_headers('https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing');

exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=trigger' );
exec( 'wget -q -O - https://www.ping.fm/data-recovery-software/wp-load.php?import_key=G7p0uoGRK&import_id=4&action=processing' );

writeTimeGeneration($file, 'done');