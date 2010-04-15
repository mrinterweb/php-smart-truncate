<?php
$width = 185;
$font = './Arial.ttf';
$num_tests = 1;

$tests = array(
  "Clinics/Externship/Simulation Courses are flippin",
  "This is a test of a long string that has no real purpose that I am just typing.",
  "iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii iiiii",
  "MMMMMMMMMMMMMMMMM MMM",
  "WWWWWWWWWWWWWWWWW WWW",
  "WWWWWWWWWWWWWWWWW WWWWWWWWWWWWWWiiiiiiiiiii"
);



require './smart_truncate.php';
$st = new SmartTruncate($font, 7.5, $width);

function output($input) {
  global $num_tests;
  if($num_tests == 1) {
    if(is_array($input)) {
      print_r($input);
    } else {
      echo $input;
    }
  }
}

$start_time = microtime(true);

for($i = 0; $i < $num_tests; $i++) {
  #$dimensions = imagettfbbox(7.5, 0, $font, $tests[0]);
  
  foreach($tests as $test_string) {
    output("Testing:\n$test_string\n");
    #output($st->get_width($test_string));
    $truncated = $st->truncate($test_string, '...');
    output($truncated."\n------------------------------\n");
  }
  
}

$end_time = microtime(true);
echo "processed ".(count($tests) * $num_tests)." strings.\n";
echo "miliseconds Taken to run $num_tests times: ".($end_time - $start_time)."\n";
?>