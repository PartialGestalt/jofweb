<?php
// Contact info
$contact = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/core/block-contact.php","Contact Us");
$contact->emit();
// Sermons
$ser = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-sermon-list.php","Sermons");
//$ser->setTitleLink("/sermon.php","Sermon Archive");
$ser->emit();
// Staff blogs
//$blogs = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-staff-blog.php","Staff Blogs");
//$blogs->emit();
// Upcoming events 
$events = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-events-list.php","Looking Forward");
$events->emit();
// Calendar
//$calendar = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/core/block-calendar.php");
//$calendar->title = "Calendar";
//$calendar->emit();
?>
