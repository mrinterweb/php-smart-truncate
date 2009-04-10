<?php
error_reporting(E_ALL);
echo "This is your PHP GD Info:\n";
print_r(gd_info());

$st_dir = "./";
$sample_text = "This is a test of some text that needs to be truncated. It really needs it.  Look how long this string is. I mean! Come ON!";

require($st_dir.'smart_truncate.php');
$smartTruncate = new SmartTruncate($st_dir.'Arial.ttf', 7.5, 185);

echo "\nThis is the string to truncate: \n$sample_text \n\n";

echo "Now we truncate the nasty long string.\n";
echo $smartTruncate->truncate($sample_text, "...");
echo "\n";
?>
