<?php
// Add partner nav
$partner = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/core/block-partner-nav.php","Beyond jofumc.org");
$partner->emit();
// Random picture...
$pic = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/app/block-random-image.php","JoF in action");
$pic->setParameter('random_image_path','assets/random_pool');
$pic->setParameter('random_image_width',198);
$pic->emit();
// Add the twitter widget (with parameters)
$twidget = new akBlock(BLOCK_TYPE_RAW,"blocks/app/block-twitter-widget.php");
$twidget->setParameter('tweet_shell_bg','#94BBDC');
$twidget->setParameter('tweet_fg','#1B3B55');
$twidget->setParameter('tweet_bg','#E0EBF5');
$twidget->setParameter('tweet_width','200');
$twidget->emit();
// Add the Upper Room link
$upper = new akBlock(BLOCK_TYPE_MULTIBAR,"blocks/app/block-upper-room.php","The Upper Room&reg; Daily Devotional");
$upper->setParameter('text_width','190');
$upper->emit();
?>
