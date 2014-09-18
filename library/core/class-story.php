<?php
/****************************************************************************
 * class-story.php -- Define object classes for stories
 ***************************************************************************/
/*
 * INCLUDES
 */
require_once("library/core/util-db.php");

/*
 * CONSTANTS
 */
  /* Story chunk types */
  /* Story headline (h1 element) */
define("STORY_CHUNK_HEADLINE","headline");
  /* Story section head (h2 element) */
define("STORY_CHUNK_SECTION","section");
  /* Story grouping head (h3 element) */
define("STORY_CHUNK_GROUP","group");
  /* Story sub-grouping head (h4 element) */
define("STORY_CHUNK_SUBGROUP","subgroup");
  /* Lede image */
define("STORY_CHUNK_LEDE","lede");
  /* Banner image */
define("STORY_CHUNK_BANNER","banner");
  /* Inline plain text (may contain HTML) */
define("STORY_CHUNK_TEXT","text");
  /* Inline error text (may contain HTML) */
define("STORY_CHUNK_ERROR","error");
  /* Inline warning text (may contain HTML) */
define("STORY_CHUNK_WARNING","warning");
  /* Text from file (may contain HTML/PHP) */
define("STORY_CHUNK_FILE","file");
  /* Metadata (comment link, post date, etc) */
define("STORY_CHUNK_META","meta");
  /* Emittable object */
define("STORY_CHUNK_OBJECT","object");

/*
 * akStoryChunk -- Individual story element, union of all chunk types
 */
class akStoryChunk
{
  /* Basic public members */
  public $id;    // DOM element ID
  public $type;  // Chunk type
  public $value; // Currently-assigned value; link/text/filename
  public $object; // If object type, the object reference 
  public $url;   // If this item is a link anchor 
  public $alt;   // For images/links, the ALT or 

  /* Event handlers */
      /* CLEAN: Add this */

  /* Constructor */
  function __construct($type=null, $value=null, &$object=null)
  {
    /* Step 1: Load basic member info */
      /* CLEAN: Throw exception on bad inits? */
    $this->type = $type;
    $this->value = $value;
    $this->object = $object;
    $this->id = null;
    $this->url = null;
    $this->alt = null;
  }

  /* Method to emit the HTML for this chunk */
  function emit()
  {
    /* If there's a link, wrap it */
    if (null != $this->url) {
      print '<a ';
      print '  class="story-link" ';
      print '  href="'.$this->url.'" ';
      if (null != $this->alt)
         print '  title="'.$this->alt.'" ';
      print '>';
    }
    /* Switch on type */
    switch($this->type) {
      case STORY_CHUNK_HEADLINE: {
        print '<h1 ';
        print '  class="story-headline" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print '>'.$this->value.'</h1>';
        break;
      }   
      case STORY_CHUNK_SECTION: {
        print '<div class="closure_div"></div>';
        print '<h2 ';
        print '  class="story-headline" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print '>'.$this->value.'</h2>';
        break;
      }   
      case STORY_CHUNK_GROUP: {
        print '<div class="closure_div"></div>';
        print '<h3 ';
        print '  class="story-headline" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print '>'.$this->value.'</h3>';
        break;
      }   
      case STORY_CHUNK_SUBGROUP: {
        print '<h4 ';
        print '  class="story-headline" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print '>'.$this->value.'</h4>';
        break;
      }   
      case STORY_CHUNK_LEDE: {
        print '<img ';
        print '  class="story-lede" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        if (null != $this->alt)
           print '  alt="'.$this->alt.'" ';
        print '  src="'.$this->value.'" ';
        print '/>';
        break;
      }   
      case STORY_CHUNK_BANNER: {
        print '<img ';
        print '  class="story-banner" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        if (null != $this->alt)
           print '  alt="'.$this->alt.'" ';
        print '  src="'.$this->value.'" ';
        print '/>';
        break;
      }   
      case STORY_CHUNK_TEXT: {
        print '<span ';
        print '  class="story-span" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print ">\n";
        print $this->value;
        print "\n</span>";
        break;
      }   
      case STORY_CHUNK_ERROR: {
        print '<span ';
        print '  class="story-error" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print ">\n";
        print $this->value;
        print "\n</span>";
        break;
      }   
      case STORY_CHUNK_WARNING: {
        print '<span ';
        print '  class="story-warning" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print ">\n";
        print $this->value;
        print "\n</span>";
        break;
      }   
      case STORY_CHUNK_META: {
        print '<span ';
        print '  class="story-meta" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print ">\n";
        print $this->value;
        print "\n</span>";
        break;
      }   
      case STORY_CHUNK_FILE: {
        /* If the user is logged in, add an editor button */
        if (is_authenticated() && is_editor()) {
          print '<a ';
          print '  href="/edit-story-file.php?file='.$this->value.'" ';
          print '  class="story-trail" ';
          print '  title="Edit this text" ';
          print '>';
          skin_img("edit_icon.png","Edit this text","story-trail");
          print '</a>';
        }
        print '<span ';
        print '  class="story-span" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print ">\n";
        /* Verify that we can read it */
        if (!is_readable($this->value)) {
          print "[[I can't import text from \"".$this->value."\", because I can't find it (or can't read it).  Sorry 'bout that.]]";
        } else {
          @include($this->value);
        }
        print "\n</span>";
        break;
      }   
      case STORY_CHUNK_OBJECT: {
        print '<div ';
        print '  class="story-object" ';
        if (null != $this->id)
           print '  id="'.$this->id.'" ';
        print '>';
        $this->object->emit();
        print '</div>';
      }
    }
    /* Finish the link (if any) */
    if (null != $this->url) print "</a>";
    /* Add the newline */
      print "\n";
  }
}

/*
 * akStory -- HTML Structural story object
 */
class akStory
{
  /* Basic public members */
  public $id;   // Story identifier (DOM)
  public $lastSimpleChunk; // Reference to last chunk added with createSimpleChunk

  /* Public metadata */
  public $book = null; // Book this story belongs to, if any
  public $author=null;  // Author of this story
  public $create_time=null; // When was the story created?
  public $edit_time=null; // Time of last edit
  public $deliver_date=null; // Date of presentation/delivery
  public $is_frozen=true; // By default, all stories are frozen
  public $keywords=''; // No keywords
  public $series=null; // If part of a series, the series title

  /* Private members */
  private $chunks; // Array of story chunks 

  /* Event handlers */
      /* CLEAN: Add this */

  /* Constructor */
  function __construct($id=null)
  {
    /* Step 1: Load basic member info */
    $this->id = $id;
  }

  /* Method to adopt a chunk into a story */
  function adopt($chunk=null)
  {
    if ($chunk == null) return; // CLEAN: throw a warning here?
    /* Add to array and bump counter */
    $this->chunks[] = $chunk;
  }

  /* Method to emit the HTML for this story */
  function emit()
  {
    print '<div ';
    if (null != $this->id)
      print '  id="'.$this->id.'" ';
    print '  class="story-div" ';
    print ">\n";
    /* Loop over all chunks */
    foreach ($this->chunks as $chunk) $chunk->emit();
    print "</div>\n";
  }

  /* Method to create and add a chunk */
  function createSimpleChunk($type=STORY_CHUNK_TEXT,$value="",&$object=null) {
    $chunk = new akStoryChunk($type,$value,$object);
    $this->adopt($chunk);
    $this->lastSimpleChunk = &$chunk;
  }
}

/*
 * akStoryEntry -- DB representation of a story start
 *
 * NOTE: This is a representation of a story entry as
 * it exists in the DB, which is slightly different than
 * the story object above, in that several chunks are 
 * possibly combined into one entry, with additional chunks
 * stored as secondary DB entries in the chunk table.
 * That is, to recreate a full story from the DB requires a
 * single akStoryEntry object from the story table, combined with
 * zero or more chunk objects from the chunks table.
 *
 */
class akStoryEntry
{
  /* Public members that reflect DB columns */
  public $id=0;   // Story id number from DB
  public $book=0; // Book ID that owns this story
  public $author=0; // UID of author of this story
  public $create_time=0; // Timestamp of initial story creation
  public $edit_time=0;  // Timestamp of last edit or comment
  public $deliver_date=''; // Presentation date of this story (for lessons/sermons/etc)
  public $is_frozen=0; // Are comments allowed?
  public $keywords=''; // Comma-separated list of associated keywords (tags)
  public $series=null; // Title of series to which this story belongs
  public $title=null; // Story title (h2)
  public $section_title=null; // Title of this section of the story
  public $lede=null; // Path to lede image (if any)
  public $banner=null; // Path to banner image (if any)
  public $text=null; // Path to text asset containing this story entry's text

  /* Private members */
  private $create_tag=null;  // User-assigned tag on create
  private $story=null;       // story object representing this entry

  /* Constructor */
  function __construct($create_tag=null)
  {
    /* Step 1: Load base info */
    $this->id = 0; // Zero means it isn't from DB 
    $this->create_tag = $create_tag; /* For identification */

    /* Step 2: Init any other required fields */
  }

  /* Method to generate a story object from this entry */
  private function cook()
  {
    /* Create base object */
    $s = new akStory("story-id-".$this->id);
    /* Set metadata */
    $s->book = $this->book;
    $s->author = $this->author;
    $s->create_time = $this->create_time;
    $s->edit_time = $this->edit_time;
    $s->deliver_date = $this->deliver_date;
    $s->is_frozen = $this->is_frozen;
    $s->keywords = $this->keywords;
    $s->series = $this->series;
    /* Create chunks from entries, in priority order */
    if ($this->title) $s->createSimpleChunk(STORY_CHUNK_HEADLINE,$this->title);
    if ($this->section_title) $s->createSimpleChunk(STORY_CHUNK_SECTION,$this->section_title);
    if ($this->banner) $s->createSimpleChunk(STORY_CHUNK_BANNER,$this->banner);
    if ($this->lede) $s->createSimpleChunk(STORY_CHUNK_LEDE,$this->lede);
    if ($this->text) $s->createSimpleChunk(STORY_CHUNK_FILE,$this->text);
    /* Set it to our private member */
    $this->story = $s;
  }

  /* Method to emit the HTML for this story */
  function emit()
  {
    $this->cook();
    $this->story->emit();
  }

  /* Method to save this story entry in the DB */
  function save()
  {
    db_saveStory($this);
  }

  /* Method to get the story object from this entry */
  function getCooked()
  {
    $this->cook();
    return $this->story;
  }
}

/*
 * akSeries -- Series (lectures, sermons, classes) object
 *         A 'series' is a grouping for stories in a book.
 */
class akSeries
{
  /* Basic public members */
  public $id;   // id from db
  public $title; // Series title
  public $subtitle; // Series subtitle
  public $description; // Human-readable description
  public $book; // Book containing stories in this series

  /* Private members */


  /* Event handlers */

  /* Constructor */
  function __construct($id=null,$title=null,$subtitle=null,$description=null,$book=null)
  {
    /* Step 1: Load basic member info */
    $this->id = $id;
    $this->title = $title;
    $this->subtitle = $subtitle;
    $this->description = $description;
    $this->book = $book;
  }

}

?>
