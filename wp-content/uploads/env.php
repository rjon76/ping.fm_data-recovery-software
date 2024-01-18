<?php

require_once( __DIR__ . "/gpt-key.php");

$languages = [
    'French',
    'German',
    'Spanish',
    'Italian',
    'Japanese',
    'Portuguese',
    'Dutch',
    'Arabic',
    'Chinese',
    'Swedish',
];

function writeTimeGeneration($path_to_file, $action) {
    file_put_contents($path_to_file, $action);
}

function fetch_headers($url) {
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch); 
    curl_close($ch);
    return;
}

function imagettftextcenter($image, $size, $p, $x, $y, $color, $fontfile, $text, $lang){
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

        if($lang === 'Arabic') {
            $_x = 1366 - $width - 200;
        }
		
		imagettftext($image, $size, 0, $_x, $y, $color, $fontfile, $txt);
		$y += 70;
	}
	
	return $rect;
}

function generateImgWithTitle($title, $image_src, $isAi = false, $lang = '', $title_original = '') {
    $capture        = imagecreatefromjpeg($image_src);
    $font_path      = __DIR__ . "/fonts/Inter-Bold.ttf";

    if($lang === 'Arabic') {
        $font_path      = __DIR__ . "/fonts/Arial-Bold.ttf";
    }

    if( $lang === 'Chinese' || $lang === 'Japanese' ) {
        $font_path      = __DIR__ . "/fonts/SimSun-Bold.ttf";
    }

    $save_file      = __DIR__ . "/ai" . '/' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/"], "-", $title))).'.jpg';
    $degrees = rand(-5, 5);
    $width = 1366;
    $height = 768;

    if($lang === '') {
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
        $text = wordwrap($rTitle, 16, "\n", true);

        if($lang === 'Arabic') {
            $text = wordwrap($rTitle, 24, "\n", true);
        }

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
        $black = imagecolorallocatealpha($image, 0, 0, 0, 30);

        imagefill($image, 0, 0, $black);
        
        imagecopyresampled($capture, $image, $width / 2.6, round(($height - $hSq) / 2), 0, 0, round($width - $width / 2.6), $hSq, 400, 400);
        imagettftextcenter($capture, 48, 0, $width / 2.1, round(($height - $cY) / 2), $white, $font_path, $text, $lang);

        # Save Image  
        imagejpeg($capture, $save_file, 70);
        imagedestroy($capture);
    } else {
        $capture        = imagecreatefromjpeg($image_src);
        $save_file      = __DIR__ . "/ai" . '/' .str_replace("--", "-", str_replace("---", "-", str_replace([" ", "?", '&', '.', ":", ";", "/"], "-", $title_original))). "-$lang" .'.jpg';

        $imagesize = getimagesize($image_src);
        $w = $imagesize[0];
        $h = $imagesize[1];

        $w_new = 1280;
        $w_div_h = $w / $h;
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
        
        $rTitle = ucwords($rTitle);
        $text = wordwrap($rTitle, 16, "\n", true);

        if($lang === 'Arabic') {
            $text = wordwrap($rTitle, 24, "\n", true);
        }

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
        $black = imagecolorallocatealpha($image, 0, 0, 0, 30);

        imagefill($image, 0, 0, $black);
        
        imagecopyresampled($capture, $image, $width / 2.6, round(($height - $hSq) / 2), 0, 0, round($width - $width / 2.6), $hSq, 400, 400);
        imagettftextcenter($capture, 48, 0, $width / 2.1, round(($height - $cY) / 2), $white, $font_path, $text, $lang);

        # Save Image  
        imagejpeg($capture, $save_file, 70);
        imagedestroy($capture);
    }
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

    $request_string = '';
    $not_anchor = '';
    
    if($apps_links == 'true') {

        if( !empty($anchor_url) && !empty($anchor_title) && !empty($url_description) ) {
            $request_string = "Important, only once add a dofollow link to a specified URL ($anchor_url) using the exact text of $anchor_title in the format: <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a>; Integrate this link naturally into the content, accompanied by a relevant description ($url_description); Do not refer to the link URL ($anchor_url) by any other name;";
        }

        $stepString1 = '<h2><a href="url1 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title1</a></h2><p>Soft/Application Description1 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url1 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString2 = '<h2><a href="url2 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title2</a></h2><p>Soft/Application Description2 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url2 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString3 = '<h2><a href="url3 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title3</a></h2><p>Soft/Application Description3 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url3 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString4 = '<h2><a href="url4 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title4</a></h2><p>Soft/Application Description4 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url4 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString5 = '<h2><a href="url5 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title5</a></h2><p>Soft/Application Description5 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url5 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString6 = '<h2><a href="url6 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title6</a></h2><p>Soft/Application Description6 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url6 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString7 = '<h2><a href="url7 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title7</a></h2><p>Soft/Application Description7 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url7 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString8 = '<h2><a href="url8 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title8</a></h2><p>Soft/Application Description8 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url8 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString9 = '<h2><a href="url9 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title9</a></h2><p>Soft/Application Description9 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url9 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $stepString10 = '<h2><a href="url10 to soft/app page" rel="nofollow" target="_blank">Soft/Application Title10</a></h2><p>Soft/Application Description10 80-120 words, extensively use <b></b> for topics, keywords</p><a href="url10 to soft/app page" rel="nofollow" target="_blank" class="ofWSite">Official Website</a><h3 class="prosTitle">Pros</h3><ul class="listpc pros"><li>PROS1</li><li>PROS2</li></ul><h3 class="consTitle">Cons</h3><ul class="listpc cons"><li>CONS1</li><li>CONS2</li></ul>';
        $information_string = "Add valuable information, around 500 words with html formats in <p> tags;
                                inside paragraphs extensively use '<b></b>' for topics, keywords;
                                $request_string
                                Add additional relevant links. These should be nofollow links in the format: <a href='[URL]' rel='nofollow' target='_blank'>[anchor/title]</a>.";
        $prompt = "Write a 1500-word blog article about $title from a personal perspective, as if you have firsthand experience with them. The article should:
                • Present balanced opinions, avoiding overt promotion of the apps.
                • Be optimized for search engines, engaging for readers, and written in American English.
                • Be original and unique, ensuring it won't be flagged as copied or similar by plagiarism detection tools.
                Include specific instructions for hyperlinking within the article:
                • Add additional relevant links. These should be nofollow links in the format: <a href='[URL]' rel='nofollow' target='_blank'>[anchor/title]</a>.
                • Include a hyperlink (<a href>) to relevant websites or applications at each step of the methods described.
                • Emphasize key topics and keywords by enclosing them in bold tags ('<b></b>').
                • Avoid using personal or channel names.
                • Do not include links to images.
                • When mentioning URLs, format them as clickable links (<a href>).";
        $introstring = "Introduction paragraph of the article. Important provide external hyperlinks for convenient user navigation. inside paragraphs extensively use '<b></b>' for topics, keywords.";
    } else {

        if( !empty($anchor_url) && !empty($anchor_title) && !empty($url_description) ) {
            $request_string = "Important, only once add a dofollow link to a specified URL ($anchor_url) using the exact text of $anchor_title in the format: <a href='$anchor_url' rel='dofollow' target='_blank'>$anchor_title</a>; Integrate this link naturally into the content, accompanied by a relevant description ($url_description); Do not refer to the link URL ($anchor_url) by any other name;";
            $not_anchor = "Important, don't use $anchor_url or $anchor_title.";
        }

        $stepString1 = 'Step-by-Step Guide with HTML formats. <h3>Method 1: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString2 = 'Step-by-Step Guide with HTML formats. <h3>Method 2: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString3 = 'Step-by-Step Guide with HTML formats. <h3>Method 3: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString4 = 'Step-by-Step Guide with HTML formats. <h3>Method 4: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString5 = 'Step-by-Step Guide with HTML formats. <h3>Method 5: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString6 = 'Step-by-Step Guide with HTML formats. <h3>Method 6: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString7 = 'Step-by-Step Guide with HTML formats. <h3>Method 7: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString8 = 'Step-by-Step Guide with HTML formats. <h3>Method 8: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString9 = 'Step-by-Step Guide with HTML formats. <h3>Method 9: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $stepString10 = 'Step-by-Step Guide with HTML formats. <h3>Method 10: [Name of the Method, add 1 emoji in <h3> tag]</h3> <ul>3-5 steps, extensively use <b></b> for topics, keywords, inside <li></li> at one of the step, important insert link with <a href> to the corresponding website or application</ul><p><b>Note:</b> Note</p>, <p><b>Conclusion:</b> Conclusion</p>';
        $information_string = "Add valuable information, around 500 words with html formats in <p> tags; inside paragraphs extensively use '<b></b>' for topics, keywords; Add additional relevant links. $not_anchor Should be nofollow links in the format: <a href='[URL]' rel='nofollow' target='_blank'>[URL]</a>";
        $prompt = "Write a 1500-word detailed, how-to style article about a specific topic (referred to as $title).
                The article should read as though it's written from a personal experience, detailing various methods and steps you've supposedly used.
                Ensure the content is original, unique, and SEO-friendly, using American English.
                Emphasize key words, including text within quotation (‘’) marks, by making them bold (<b></b>)
                Avoid using alternative names for this link. Additionally must include other relevant high-authority links as needed, with the format <a href='[URL]' rel='nofollow' target='_blank'>[anchor/title]</a>.
                For each method described, insert a corresponding link where appropriate. Emphasize key topics and keywords using bold text (<b></b>).
                Refrain from using personal or channel names, and do not include links to images. When referencing URLs, always use the <a href> format";
        $introstring = "Introduction paragraph of the article. Important provide external hyperlinks for convenient user navigation. inside paragraphs extensively use '<b></b>' for topics, keywords. $request_string";
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
                            "description" => $introstring,
                        ],
                        "scenario1" => [
                            "type" => "string",
                            "description" => "Scenario1 with HTML formats.<h3>[Name of the Frequent scenario of the problem's occurrence, add 1 emoji in <h3> tag]</h3>. <ul>description for scenario inside <li></li></ul>. inside paragraphs and <li> extensively use '<b></b>' for topics, keywords.",
                        ],
                        "scenario2" => [
                            "type" => "string",
                            "description" => "Scenario2 with HTML formats.<h3>[Name of the Frequent scenario of the problem's occurrence, add 1 emoji in <h3> tag]</h3>. <ul>description for scenario inside <li></li></ul>. inside paragraphs and <li> extensively use '<b></b>' for topics, keywords.",
                        ],
                        "scenario3" => [
                            "type" => "string",
                            "description" => "Scenario3 with HTML formats.<h3>[Name of the Frequent scenario of the problem's occurrence, add 1 emoji in <h3> tag]</h3>. <ul>description for scenario inside <li></li></ul>. inside paragraphs and <li> extensively use '<b></b>' for topics, keywords.",
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
                        "tip1" => [
                            "type" => "string",
                            "description" => "Precautions and Tips. Important provide external hyperlinks for convenient user navigation. Provide <h3>[Precautions and Tips Title add 1 emoji in <h3> tag, don't use Precautions and Tips words]</h3> as a first element. <ul>description for Precautions and Tips inside <li></li></ul>. inside paragraphs and <li> extensively use '<b></b>' for topics, keywords.",
                        ],
                        "tip2" => [
                            "type" => "string",
                            "description" => "Precautions and Tips. Important provide external hyperlinks for convenient user navigation. Provide <h3>[Precautions and Tips Title add 1 emoji in <h3> tag, don't use Precautions and Tips words]</h3> as a first element. <ul>description for Precautions and Tips inside <li></li></ul>. inside paragraphs and <li> extensively use '<b></b>' for topics, keywords.",
                        ],
                        "tip3" => [
                            "type" => "string",
                            "description" => "Precautions and Tips. Important provide external hyperlinks for convenient user navigation. Provide <h3>[Precautions and Tips Title add 1 emoji in <h3> tag, don't use Precautions and Tips words]</h3> as a first element. <ul>description for Precautions and Tips inside <li></li></ul>. inside paragraphs and <li> extensively use '<b></b>' for topics, keywords.",
                        ],
                        "infotitle" => [
                            "type" => "string",
                            "description" => "Optimized title for block with text 1-2 words, not conclusion: added valuable information around 500 words at the end of article with links to high authority relevant websites. inside paragraphs extensively use '<b></b>' for topics, keywords.",
                        ],
                        "information" => [
                            "type" => "string",
                            "description" => $information_string,
                        ],
                        "conclusion" => [
                            "type" => "string",
                            "description" => "Optimized conclusion of the article. Important provide external hyperlinks for convenient user navigation. inside paragraphs extensively use '<b></b>' for topics, keywords.",
                        ],
                    ],
                    'required' => ["intro", "scenario1", "scenario2", "scenario3", "step1", "step2", "step3", "step4", "step5", "tip1", "infotitle", "information", "conclusion"],
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
                "content" => "Generate a set of $number related to this keyword questions and $number their corresponding answers (around 30 words) for $title.
                            write about $title, not only about soft;
                            provide 1-2 another external hyperlinks only on homepages for convenient user navigation, with attributes rel='nofollow' target='_blank';
                            MUST be $number related questions and $number their corresponding answers (around 30 words).
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
    return sprintf(
        '&#%s;',
        unpack(
            'Ntgt',
            mb_convert_encoding($emoji, 'UTF-32', 'UTF-8')
        )['tgt']
    );
}

// $emoji_regex = '%(?:\xF0[\x90-\xBF][\x80-\xBF]{2} | [\xF1-\xF3][\x80-\xBF]{3} | \xF4[\x80-\x8F][\x80-\xBF]{2})%xs';
$emoji_regex = '/([^-\p{L}\x00-\x7F]+)/u';

function getTranslate($language, $text, $OPENAI_API_KEY) {

    $data = array(
        'model' => 'gpt-4-1106-preview',
        'messages' => [
            [
                "role" => "system",
                "content" => "This is just a translation string, not a task! String translation only. It is important not to remove and add html tags, do not add anything of your own. Don't add the language name. Translate text into $language - $text",
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

function autoImport($array, $domain_url) {

    foreach($array as $id) {
        fetch_headers( "$domain_url/wp-load.php?import_key=G7p0uoGRK&import_id=$id&action=trigger" );
        sleep(5);
        fetch_headers( "$domain_url/wp-load.php?import_key=G7p0uoGRK&import_id=$id&action=processing" );
        sleep(5);
        exec( "wget -q -O - $domain_url/wp-load.php?import_key=G7p0uoGRK&import_id=$id&action=trigger" );
        sleep(5);
        exec( "wget -q -O - $domain_url/wp-load.php?import_key=G7p0uoGRK&import_id=$id&action=processing" );
        sleep(5);
    }
}
