<?php
// Contact info
$contact = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/core/block-contact.php","Contact Us");
$contact->emit();
// Sermons
$ser = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-sermon-list.php","Sermons");
$ser->emit();
// Add an Upper Room devotional link
$ur = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-upper-room.php","The Upper Room&reg; Daily Devotional");
$ur->emit();
// Add an upcoming events block
$ev = new akBlock(BLOCK_TYPE_SIDEBAR,"blocks/app/block-events-list.php","Upcoming Events");
$ev->emit();
?>
