<?php
// Before including this file, the caller must set the following
//    parameters:
//
// Required parameters:
// ---------------------
// $tweet_bg
// $tweet_fg
//
// Optional parameters:
// ---------------------
// $tweet_shell_bg
// $tweet_shell_fg
// $tweet_link_fg
//
// Pull any parameters
$tweet_fg = block_getParameter('tweet_fg',"#000000");
$tweet_bg = block_getParameter('tweet_bg',"#FFFFFF");
$tweet_shell_bg = block_getParameter('tweet_shell_bg',$tweet_bg);
$tweet_shell_fg = block_getParameter('tweet_shell_fg',$tweet_fg);
$tweet_link_fg = block_getParameter('tweet_link_fg',$tweet_fg);
$tweet_width = block_getParameter('tweet_width',200);
$tweet_height = block_getParameter('tweet_height',300);
?>
<div id="block-twitter-widget">
<script src="http://widgets.twimg.com/j/2/widget.js" type="text/javascript"></script>
<script type="text/javascript">
new TWTR.Widget({
  version: 2, type: 'profile', rpp: 4, interval: 6000, 
<?php
  print 'width: '.$tweet_width.', height: '.$tweet_height.',';
?>
  theme: {
<?php
  print '  shell: { background: \''.$tweet_shell_bg.'\', color: \''.$tweet_shell_fg.'\' },'."\n";
  print '  tweets: { background: \''.$tweet_bg.'\', color: \''.$tweet_fg.'\', links: \''.$tweet_link_fg.'\' }'."\n";
?>
  },
  features: { 
    scrollbar: true, loop: false, live: true, hashtags: true, timestamp: true, avatars: false, behavior: 'all' 
  }
}).render().setUser('JOFUMC').start();
</script>
</div>
