<div id="block-sermon-list" class="block-content-addmargin">
<?php
// Sermon list block (sidebar/multibar)
//
// Required parameters:
// ---------------------
//
// Optional parameters:
// ---------------------
// sermon_start_date -- Earliest sermon to list
// sermon_end_date -- Latest sermon to list
// 

// Step 1: Pull parameters
  // Get current time in seconds
$timenow=time();
  // Start is about 5 weeks ago (5 * 7 * 24 * 3600 secs ago)
$timestart=$timenow-3024000;
  // End is 2 weeks from now (2 * 7 * 24 * 3600 secs into the future)
$timeend=$timenow+1209600;

$default_start = date('Y-m-d',$timestart);
$default_end = date('Y-m-d',$timeend);

$default_book = 103; // Default sermon book...
$start_date = block_getParameter('sermon_start_date',$default_start);
$end_date = block_getParameter('sermon_end_date',$default_end);
$bookid = block_getParameter('sermon_book',$default_book);



// Step 2: Generate list from DB
$sermonList = db_sermonsByDateRange($bookid,$start_date,$end_date);

// Step 3: Build a story, with a series header whenever it changes...
$lastSeries='';
$st = new akStory();
foreach (array_reverse($sermonList,true) as $sermon)
{
  // Step 3.1: Emit a series header if it changes....
  if ($lastSeries != $sermon->series) {
    $st->createSimpleChunk(STORY_CHUNK_SUBGROUP,$sermon->series);
    $st->lastSimpleChunk->url = "/sermon.php?date=".$sermon->deliver_date;
    $lastSeries = $sermon->series;
  }
  // Step 3.2: Emit the sermon header
  $st->createSimpleChunk(STORY_CHUNK_TEXT,substr($sermon->deliver_date,5).": ".$sermon->title);
  $st->lastSimpleChunk->url = "/sermon.php?date=".$sermon->deliver_date;
}

$st->createSimpleChunk(STORY_CHUNK_META,'<a href="/sermon.php" title="Sermon Archive" class="story-link">(more in the archive...)</a>');

// Step 4: emit the story
$st->emit();

?>
</div>
