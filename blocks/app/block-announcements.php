<div class="block-announcements">
<?php
global $auth_user;
//
// REQUIRED PARAMETERS:
// <none>
//
// OPTIONAL PARAMETERS
// 'max_count' -- Max # of entries to load
// 'max_chars' -- Max # of characters from text to load
//        

/* Step 1: Get parameters */
$max_count = block_getParameter('max_count',5);
$max_chars = block_getParameter('max_chars',150);

/* Step 2: Get list of announcements */
$list = db_announcementList($max_count);

/* Step 3: Start the story */
$st = new akStory();

/* Step 3: Build the HTML output */
if ($list == null)
{
  $st->createSimpleChunk(STORY_CHUNK_META,"(no current announcements)");
}
else
{
  foreach ($list as $a) 
  {
    $st->createSimpleChunk(STORY_CHUNK_TEXT,"<br/>");
    $st->createSimpleChunk(STORY_CHUNK_SUBGROUP,$a->title);
    $st->createSimpleChunk(STORY_CHUNK_FILE,$a->text);
    $st->createSimpleChunk(STORY_CHUNK_META,"Posted: ".$auth_user->format_date($a->edit_time));
  }
}

$st->emit();
?>
</div>
