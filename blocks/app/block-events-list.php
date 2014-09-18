<div id="block-event-list" class="block-content-addmargin">
<?php
require_once("library/core/class-book.php");
require_once("library/core/util-db.php");
// Event list block (sidebar/multibar)
//
// Required parameters:
// ---------------------
//
// Optional parameters:
// ---------------------
// event_list_size Max # of events to retrieve/display
// 

// Step 1: Pull parameters
$default_book = BOOK_ID_CALENDAR; // Default events book...
$default_count = 8; // Default # to list
$bookid = block_getParameter('event_book',$default_book);
$count = block_getParameter('event_count',$default_count);

// Step 2: Generate list from DB
$eventList = db_eventsUpcoming($bookid,$count);

// Step 3: Build a story, with a date header whenever it changes...
$lastDate='';
$st = new akStory();
if (null == $eventList)
{
  $st->createSimpleChunk(STORY_CHUNK_TEXT,"No upcoming events posted.");
} else foreach ($eventList as $event) {
  // Step 3.1: Emit a series header if it changes....
  $eventDate = new DateTime($event->startdate);
  if ($lastDate != $eventDate->format("F")) {
    $lastDate = $eventDate->format("F");
    $st->createSimpleChunk(STORY_CHUNK_SUBGROUP,$lastDate);
    //$st->lastSimpleChunk->url = "/event.php?date=".$eventDate->format("Y-m");
  }
  // Step 3.2: Emit the event header
  $st->createSimpleChunk(STORY_CHUNK_TEXT,$eventDate->format("m-d").': '.$event->title);
  $st->lastSimpleChunk->url = "/event.php?id=".$event->id;
}

//$st->createSimpleChunk(STORY_CHUNK_META,'<a href="/event.php" title="Event Archive" class="story-link">(more on the big calendar...)</a>');


// Step 4: emit the story
$st->emit();

?>
</div>
