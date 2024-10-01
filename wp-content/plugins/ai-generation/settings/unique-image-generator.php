<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 1500);
set_time_limit(1500);
ini_set('display_errors',1);
error_reporting(E_ALL);

function drawDiamond($im, $x, $y, $width, $color, $filled) {
    // here we work out the four points of the diamond
    $p1_x = $x;
    $p1_y = $y+($width/2);

    $p2_x = $x+($width/2);
    $p2_y = $y;

    $p3_x = $x+$width;
    $p3_y = $y+($width/2);

    $p4_x = $x+($width/2);
    $p4_y = $y+$width;

    // now create an array of points to store these four points
    $points = array($p1_x, $p1_y, $p2_x, $p2_y, $p3_x, $p3_y, $p4_x, $p4_y);

    // the number of vertices for our polygon (four as it is a diamond
    $num_of_points = 4;

    if ($filled) {
        // now draw out the filled polygon
        ImageFilledPolygon($im, $points, $num_of_points, $color);
    }else{
        // draw out an empty polygon
        ImagePolygon($im, $points, $num_of_points, $color);
    }
}

function randowWidthAndHeight() {
    return rand(1, 300);
}

function gradient($w=100, $h=100, $c = array('#FFFFFF','#FF0000','#00FF00','#0000FF'), $hex = true) {

    $im = imagecreatetruecolor((int)$w, (int)$h);

    if($hex) {  // convert hex-values to rgb
        for($i=0;$i<=3;$i++) {
            $c[$i]=hex2rgb($c[$i]);
        }
    }

    $rgb=$c[0]; // start with top left color
    for($x=0;$x<=$w;$x++) { // loop columns
        for($y=0;$y<=$h;$y++) { // loop rows
            // set pixel color 
            $col=imagecolorallocate($im,(int)$rgb[0],(int)$rgb[1],(int)$rgb[2]);
            imagesetpixel($im,$x-1,$y-1,$col);
            // calculate new color  
            for($i=0;$i<=2;$i++) {
                $rgb[$i]=
                    $c[0][$i]*(($w-$x)*($h-$y)/($w*$h)) +
                    $c[1][$i]*($x     *($h-$y)/($w*$h)) +
                    $c[2][$i]*(($w-$x)*$y     /($w*$h)) +
                    $c[3][$i]*($x     *$y     /($w*$h));
            }
        }
    }
    return $im;
}

function hex2rgb($hex) {
    $rgb[0]=hexdec(substr($hex,1,2));
    $rgb[1]=hexdec(substr($hex,3,2));
    $rgb[2]=hexdec(substr($hex,5,2));
    return($rgb);
}

function generateUniqImage($screen_path = '') {

    if($screen_path == '') {
        $save_path = __DIR__ . '/assets/images/Disk-Drill.png';
        $directory = __DIR__ . '/assets/images/DD-screenshots';

        $screens = array_values(array_diff(scandir($directory), array('..', '.')));

        $image = imagecreatefrompng($directory . '/' . $screens[rand(0, count($screens) - 1)]);
    } else {
        $save_path = $screen_path;

        if(exif_imagetype($screen_path) != IMAGETYPE_JPEG){
            $image = imagecreatefrompng($screen_path);
        } else {
            $image = imagecreatefromjpeg($screen_path);
        }
    }

    if(is_bool($image)) {
        die;
    }

    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));

    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    $w = imagesx($image);
    $h = imagesy($image);

    imagedestroy($image);

    $w_div_h = $w / $h;

    $width = 1536;
    $height = $width / $w_div_h;
    $im = gradient($width, $height, array(array(rand(0, 255), rand(0, 255), rand(0, 255)), array(rand(0, 255), rand(0, 255), rand(0, 255)), array(rand(0, 255), rand(0, 255), rand(0, 255)), array(rand(0, 255), rand(0, 255), rand(0, 255))), false);

    $w_new = 1280;
    $h_new = $w_new / $w_div_h;

    $capture = imagecreatetruecolor((int)$w_new, (int)$h_new);
    imagecopyresampled($capture, $bg, 0, 0, 0, 0, (int)$w_new, (int)$h_new, (int)$w, (int)$h); 

    for($i = 0; $i < rand(1, 30); $i++ ) {
        $wh = randowWidthAndHeight();
        ImageFilledEllipse($im, rand(1, $width), rand(1, $width), $wh, $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 10));
        ImageEllipse($im, rand(1, $width), rand(1, $width), $wh, $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 10));
    }

    for($i = 0; $i < rand(1, 30); $i++ ) {
        $wh = randowWidthAndHeight();
        drawDiamond($im, rand(1, $width), rand(1, $width), $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), rand(0, 100)), true);
    }

    for($i = 0; $i < rand(1, 30); $i++ ) {
        $wh = randowWidthAndHeight();
        ImageFilledEllipse($im, rand(1, $width), rand(1, $width), $wh, $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 10));
    }

    for($i = 0; $i < rand(1, 30); $i++ ) {
        $wh = randowWidthAndHeight();
        ImageRectangle($im, rand(1, $width), rand(1, $width), $wh, $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 10));
    }

    for($i = 0; $i < rand(1, 30); $i++ ) {
        $wh = randowWidthAndHeight();
        ImageLine($im, rand(1, $width), rand(1, $width), $wh, $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 10));
        ImageDashedLine($im, rand(1, $width), rand(1, $width), $wh, $wh, imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 10));
    }
    
    imagecopy($im, $capture, (int)(((int)$width - (int)$w_new) / 2), (int)(((int)$height - (int)$h_new) / 2), 0, 0, (int)imagesx($capture), (int)imagesy($capture));
    ImagePNG($im, $save_path, 9); 

    ImageDestroy($im);

    return true;
}