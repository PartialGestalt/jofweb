<?php
// Sermons
$ser = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/app/block-sermon-list.php","Sermons");
$ser->emit();
// Upcoming events 
$events = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/app/block-events-list.php","Looking Ahead");
$events->emit();
// Add the twitter widget (with parameters)
$twidget = new akBlock(BLOCK_TYPE_RAW,"blocks/app/block-twitter-widget.php");
$twidget->setParameter('tweet_shell_bg','#C82020');
$twidget->setParameter('tweet_shell_fg','#FFFFFF');
$twidget->setParameter('tweet_fg','#000000');
$twidget->setParameter('tweet_bg','#FFFFFF');
$twidget->setParameter('tweet_width',config_getParameter('skin_width_multibar'));
$twidget->emit();
?>
