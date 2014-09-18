<div id="block-upper-room" class="block-content-addmargin">
<?php
// Upper Room (R) daily devotional
// 
// Required parameters:
// ---------------------
//
// Optional parameters:
// ---------------------

// Step 1: Setup parameters
$devo_maxlen=block_getParameter('devo_maxlen',300);
$text_width=block_getParameter('text_width',200);
$today=time();
$upperurl="http://devotional.upperroom.org/devotionals/" . date('Y-m-d',$today);

// Step 2: Import via URL file open into a DOM object
$upperHTML = file_get_contents($upperurl);
$dom = new DOMDocument;
@$dom->loadHTML($upperHTML);

// Step 3: Parse out the bits we care about
$scripsnip = $dom->getElementById('scripture_snippet');
$scripkids=$scripsnip->childNodes;
$scripref=preg_replace("/\(.*\)/","",$scripsnip->childNodes->item(3)->textContent);
$scripref=str_replace("- ","",$scripref);
$devo = ltrim(rtrim($scripsnip->nextSibling->textContent));

// Step 4: Truncate to a reasonable size, but only on a word boundary...
if (strlen($devo) > $devo_maxlen) {
  $devo=substr($devo,0,$devo_maxlen);
  $devo=substr($devo,0,strrpos($devo," ")) . "...";
}


// Step 3: Create
?>
<?php

$st = new akStory();

$st->createSimpleChunk(STORY_CHUNK_META,$scripsnip->childNodes->item(1)->textContent . "<br/>" . $scripref);
$st->createSimpleChunk(STORY_CHUNK_TEXT,$devo);
$st->createSimpleChunk(STORY_CHUNK_META,"<a href='" . $upperurl . "' title='go to UpperRoom.org'>(read more at UpperRoom.org)</a>");
$st->emit();
?>
</div>
