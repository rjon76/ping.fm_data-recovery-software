<?php

$file = __DIR__ . '/time_record.txt';

$step = file_get_contents($file);
echo $step;