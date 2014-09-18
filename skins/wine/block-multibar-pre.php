<?php
// Add a calendar...
//require_once("library/core/class-date.php");
//$calendar = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/core/block-calendar.php");
//$calendar->title = "Calendar";
//$calendar->emit();
// Staff blogs
$blogs = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/app/block-staff-blog.php","Staff Blogs");
$blogs->emit();
// Add partner nav
$partner = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/core/block-partner-nav.php","Beyond JoF");
$partner->emit();
// Random picture...
$pic = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-random-image.php","Snapshots from the Journey");
$pic->setParameter('random_image_path','assets/random_pool');
$pic->setParameter('random_image_width',172);
$pic->emit();
// Add the twitter widget (with parameters)
$twidget = new akBlock(BLOCK_TYPE_RAW,"blocks/app/block-twitter-widget.php");
$twidget->setParameter('tweet_shell_bg','#66273B');
$twidget->setParameter('tweet_bg','#B85B79');
$twidget->setParameter('tweet_fg','#FFFFFF');
$twidget->setParameter('tweet_width','172');
$twidget->emit();
?>
<div class="block-wrapper-div"></div>
