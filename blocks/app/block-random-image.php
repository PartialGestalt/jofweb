<?php
// Random image block; whoever includes this block
//
// Required parameters:
// ---------------------
// $random_image_path 
// $random_image_width;
//
// Optional parameters:
// ---------------------
// $random_image_caption
// 
// Be careful not to use large images in the random pool,
// as the download and scaling costs may cause resource 
// issues.
//
// CLEAN: Pull from DB instead of globbing the path

// Step 1: Pull parameters
$path = block_getParameter('random_image_path',"/");
$width = block_getParameter('random_image_width',"200");
$caption = block_getParameter('random_image_caption',"");


// Step 2: Choose the image from the path 
$imageset=glob($path."/*.{png,jpg}",GLOB_BRACE);
$r=mt_rand(0,count($imageset)-1);
$image=$imageset[$r];
echo '<div id="block-random-image">';
echo '<a href="viewer-photo.php?gallery=random&amp;image_path='.$image.'" class="frameless">';
echo '<img src="'.$image.'" alt="Random Image" width="'.$width.'" class="block-image frameless"/>';
echo '</a>';
echo '<span class="block-text">'.$caption.'</span>';
echo '</div>';
?>
