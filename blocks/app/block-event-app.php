<?php
// Event app block (center app area)
//
// Required parameters:
// ---------------------
// event-id DB ID of event to display
//
// Optional parameters:
// ---------------------

// Step 1: Get target date from parameters
$event_id = block_getParameter('event-id','1');

// Step 2: Generate entry from DB
  /* Lookup */
$ev = db_getEventById($event_id);
  /* Failure check */
if (null == $ev)
{
  $st = new akStory();
  $st->createSimpleChunk(STORY_CHUNK_ERROR,"No event found for id=".$event_id);
  $st->emit();
  return;
}

// Step 3: Get event details 
  /* Lookup */
$ev_story = db_getStoryById($ev->story);
  /* Failure check */
if (null == $ev_story)
{
  $st = new akStory();
  $st->createSimpleChunk(STORY_CHUNK_ERROR,"No details found for event id=".$event_id);
  $st->emit();
  return;
}

// Step 4: Add date/time to event details story
if (null == $ev->enddate) {
  // Single-day
  $ev_story->createSimpleChunk(STORY_CHUNK_META,"Scheduled for: ".$ev->startdate." at ".$ev->starttime);
} else {
  // Multi-dat
  $ev_story->createSimpleChunk(STORY_CHUNK_META,"Scheduled from: ".$ev->startdate." at ".$ev->starttime);
  $ev_story->createSimpleChunk(STORY_CHUNK_META," until ".$ev->enddate." at ".$ev->endtime);
}

// Step 5: Add decoration?  Next/Prev?  Others in this series?

// Step 6: Emit that bad boy.
$ev_story->emit();

?>
