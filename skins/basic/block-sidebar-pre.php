<?php
// Basic contact info
// Contact info
$contact = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/core/block-contact.php","Contact Us");
$contact->emit();
// Staff blogs
$blogs = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-staff-blog.php","Staff Blogs");
$blogs->emit();
// Random picture...
$pic = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-random-image.php","JoF in action");
$pic->setParameter('random_image_path','assets/random_pool');
$pic->setParameter('random_image_width',198);
$pic->emit();
// Add an Upper Room devotional link
$ur = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-upper-room.php","The Upper Room&reg; Daily Devotional");
$ur->emit();
// Calendar
//require_once("library/core/class-date.php");
//$calendar = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/core/block-calendar.php");
//$calendar->title = "Calendar";
//$calendar->emit();
?>
