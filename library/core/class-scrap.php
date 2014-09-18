<?php
/****************************************************************************
 * class-scrap.php -- Define object classes for scraps (for album books)
 *
 * Most scraps will be pictures from photo albums, but we allow any object
 * here.
 * A scrap object is stored in the story table in the DB, but we repurpose some of 
 * the fields (and don't consider secondary chunks).
 ***************************************************************************/
/*
 * INCLUDES
 */
require_once("library/core/class-story.php");

/*
 * CONSTANTS
 */

/*
 * akScrap -- Internal representation of a scrap
 */
class akScrap
{
  /* Public members */
  public $id=0;   // Story id number from DB
  public $book=0; // Book ID that owns this scrap
  public $author=0; // UID of author (uploader) of this scrap
  public $create_time=0; // Timestamp of initial scrap creation
  public $edit_time=0;  // Timestamp of last edit or comment
  public $reference_date=''; // Date of capture or other reference
  public $is_frozen=0; // Are comments allowed?
  public $tags = array(); // Array of tags (built from 'keywords' DB column)
  public $series=null; // Title of series to which this story belongs
  public $caption=null; // Scrap caption (title)
  public $content_type=null; // Content-Type of object (e.g. image/jpeg)
  public $thumb=null; // Path to thumbnail image (if any) (from 'lede' DB column)
  //public $altimg=null; //  Path to altered/?? image (from 'banner' DB column)
  public $path=null; // Path to scrap object

  /* Constructor */
    /* There are 3 ways to call the constructor:
       (1) DB data ($seed is an akStoryEntry instance)
       (2) file path ($seed is a string)
       (3) id ($seed is an integer)
      */
  function __construct($seed)
  {
    /* Handle simple type cases */
    if (!($seed instanceof akStoryEntry)) {
      if ('string' == gettype($seed)) {
        $this->path=$seed;
      } else {
        /* Not a string, treat as integer */
        $this->id = $seed;
      }
    }
    else
    {
      /* Copy from an akStoryEntry (DB entry) */
      $this->id = $seed->id;
      $this->book = $seed->book;
      $this->author = $seed->author;
      $this->create_time = $seed->create_time;
      $this->edit_time = $seed->edit_time;
      $this->reference_date = $seed->deliver_date;
      $this->is_frozen = $seed->is_frozen;
      $this->tags = explode(',', $seed->keywords);
      $this->series = $seed->series;
      $this->caption = $seed->title;
      $this->content_type = $seed->section_title;
      $this->thumb = $seed->lede;
      $this->path = $seed->text;
    }
  }

  /* Method to emit HTML for this scrap */
    /* NOTE: Because scraps are (usually) binary objects and not HTML
     * objects, we support multiple emit() methods.  The "default" emit
     * method just calls out to one of the others, based on content type.
     * See the other emit_xxx() methods for descriptions.
     */
  function emit()
  {
    /* Step 1: Get major/minor content type breakdown */
    list($major,$minor) = explode('/', $this->content_type);

    /* Step 2: Switch on major type */
    switch ($major) {
      case 'image': {
        /* For images, 'normal' is full size inline, constrained by the skin's
         * hints.  We reference the original file, though.
         */
        print akimg_html_constrained($this->path);
        break;
      }
      default: {
        /* For everything else, 'normal' is an icon representing the 
         * file type.
         */
        print akimg_html_constrained($this->path);
      }
    }
  }

  /* Method to save scrap into DB */
  function save()
  {
    /* First, convert to akStoryEntry */
    $st = new akStoryEntry();
    $st->book = $this->book;
    $st->author = $this->author;
    $st->create_time = $this->create_time;
    $st->edit_time = $this->edit_time;
    $st->deliver_date = $this->reference_date;
    $st->is_frozen = $this->is_frozen;
    $st->keywords = implode(',',$this->tags);
    $st->series = $this->series;
    $st->title = $this->caption;
    $st->section_title = $this->content_type;
    $st->lede = $this->thumb;
    $st->banner = null; // $this->altimg;
    $st->text = $this->path;

    /* Now, save it */
    $st->save();
  }
}
?>
