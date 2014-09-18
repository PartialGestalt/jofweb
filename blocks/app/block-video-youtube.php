<div class="block-video block-video-div block-video-youtube">
<?php
//
// REQUIRED PARAMETERS:
// @param $youtube_video_id YouTube video identifier
//        

$video_id = block_getParameter('youtube_video_id','');
$video_width = floor(0.95 * config_getParameter('skin_width_app',400));
$video_height = floor($video_width * 0.80);

$youtube_prefix="http://www.youtube.com/embed/";

print '<iframe class="youtube-player" type="text/html" width="'.$video_width.'" height="'.$video_height.'" src="'.$youtube_prefix.$video_id.'" frameborder="0"></iframe>';
?>
</div>
