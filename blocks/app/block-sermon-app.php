<?php
// Sermon app block (center app area)
//
// Required parameters:
// ---------------------
// sermon-date Date of delivery of the sermon to display
//
// Optional parameters:
// ---------------------

// Step 1: Get target date from parameters
$sermon_date = block_getParameter('sermon-date',date('Y-m-d'));

// Step 2: Generate entry(-ies) from DB
$sermonList = db_sermonsByDateRange(103,$sermon_date,$sermon_date);

// Step 3: Check for failure 
if (null == $sermonList)
{
  $st = new akStory();
  $st->createSimpleChunk(STORY_CHUNK_ERROR,"No sermon found for ".$sermon_date);
  $st->emit();
  return;
}

function file_size($size)
{
      $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
}

// Step 4: Got (at least) one
$sermon = $sermonList[$sermon_date];
$st = new akStory();
$st->createSimpleChunk(STORY_CHUNK_HEADLINE,"Sermon: ".$sermon_date);
//$st->createSimpleChunk(STORY_CHUNK_SECTION,$sermon->series);
// Step 4.1: Title emit
$st->createSimpleChunk(STORY_CHUNK_GROUP,$sermon->title);
// Step 4.2: Notes/synopsis
$st->createSimpleChunk(STORY_CHUNK_FILE,$sermon->text);
// Step 4.3: Audio?
$sermonPath = dirname($sermon->text);
$audioFile = $sermonPath.'/'.$sermon_date.'.mp3';
if (is_readable($audioFile))
{
  /* Found one -- make a link */
  $st->createSimpleChunk(STORY_CHUNK_TEXT,'<a href="'.$audioFile.'" title="Sermon Audio File (MP3)">Sermon Audio</a> (MP3 file, '.file_size(filesize($audioFile)).')');
}
else
{
  /* No Audio */
  $st->createSimpleChunk(STORY_CHUNK_META,"No audio available");
}

// Step 5: Add decoration?  Next/Prev?  Others in this series?

// Step 6: Emit that bad boy.
$st->emit();

?>
