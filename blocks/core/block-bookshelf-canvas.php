<div id="bookshelf-canvas-div">
<?php
// If the skin has pre-content setup, include it here.
require_once("library/core/util-skin.php");
skin_include("block-dashboard-canvas-pre.php");
?>
<div id="bookshelf-canvas-container">
<?php
require_once("library/core/class-story.php");
$cStory = new akStory();
$cStory->createSimpleChunk(STORY_CHUNK_SECTION,"Welcome");
$cStory->createSimpleChunk(STORY_CHUNK_FILE,"assets/blocks/bookshelf-canvas/welcome.html");
// CLEAN: Add intro text here? 
$cStory->emit();

?>
</div><!-- Close bookshelf-canvas-container -->
<?php
// If the skin has post-content setup, include it here.
skin_include("block-dashboard-canvas-post.php");
?>
</div>
