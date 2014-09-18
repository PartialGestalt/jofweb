<div id="block-staff-blog" class="block-content-addmargin">
<?php
// Staff blog block (sidebar/multibar)
// 
// Incorportate latest entries from all staff members into
// a single blogblock
//
// Required parameters:
// ---------------------
//
// Optional parameters:
// ---------------------

// Step 1: Pull parameters

// Step 2: Generate list from blog books

// Step 3: Build a story, with a series header whenever it changes...
$st = new akStory();
$st->createSimpleChunk(STORY_CHUNK_META,"Coming &quot;soon&quot;...");

// Step 4: emit the story
$st->emit();

?>
</div>
