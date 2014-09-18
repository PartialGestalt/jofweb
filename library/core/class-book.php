<?php
/****************************************************************************
 * class-book.php -- Book(shelf) object definitions
 *
 ***************************************************************************/
  // Book types
define("BOOK_TYPE_STORY","story");      // Book of stories 
define("BOOK_TYPE_SERMON","sermon");    // Book of sermons
define("BOOK_TYPE_BLOG","blog");        // Book of blog entries 
define("BOOK_TYPE_CALENDAR","event");   // Book of events
define("BOOK_TYPE_USERS","users");      // Book of users
define("BOOK_TYPE_PROFILE","profile");  // Book of profile data
define("BOOK_TYPE_ALBUM","album");      // Book of pictures
define("BOOK_TYPE_BOOK","book");        // Book of books
define("BOOK_TYPE_PAGE","page");        // Book of page content 
define("BOOK_TYPE_DIVIDER","divider");  // Separates groups on shelf

 // Custom book IDs (special IDs are less than 1000 )
define("BOOK_ID_NEW",100);  // Custom editor for creating a new book
define("BOOK_ID_PROFILE",101); // Custom editor for profile
define("BOOK_ID_ANNOUNCEMENTS",102); // Front page announcements
define("BOOK_ID_SERMONS",103); // Sermon series
define("BOOK_ID_MUSIC",104); // Worship songs
define("BOOK_ID_CALENDAR",105); // Calendar/events
define("BOOK_ID_BULLETINS",106); // Bulletins album



/*
 * akBook -- Book object
 *
 * A 'book' is a container for other editable types; stories, users, etc.
 */
class akBook
{
  /* Basic public members */
    /* Required (from DB) */
  public $title;   // Book title
  public $type;    // Type of contents
  public $author;  // Who wrote this book? (DB people ID)
  public $id;      // DB id 
  public $hint;    // Description/info about book
    /* Optional */
  public $dom_id; // ID of DOM object

  /* Basic private members */
  private $contents; // Bits contained in this book

  /* Constructor */
  function __construct($title=null,$type=null)
  {
    /* Step 1: Copy elements from args */
    if (null == $title) $this->title='Book Title';
    else $this->title = $title;
    if (null == $type) $this->type = BOOK_TYPE_STORY;
    else $this->type = $type;

    /* Step 2: Generic init */
    $this->author = 0;
    $this->id = 0; 
    $this->dom_id = NULL;

    /* Step 3: Init private members */
    $this->contents=null;
  }

  /* Method to set the title of a book */
  function setTitle($title=null) 
  {
      $this->title = $title;
  }
  /* Method to set the description of a book */
  function setHint($hint=null) 
  {
      $this->hint = $hint;
  }
  /* Method to set the author of a book */
  function setAuthor($author=null)
  {
     global $auth_user; 
     if (null==$author) $author=$auth_user->uid;
     $this->author = $author;
  }

  /* Method to emit the HTML for this object */
    /* NOTE: The mouseover/mouseout functions are provided by the skin */
  function emit()
  {
    switch($this->type) {
      case BOOK_TYPE_DIVIDER: {
        print '<div class="book-divider"> '."\n";
        print '<span ';
        print   'class="book-span-divider" ';
        print '>'."\n";
        print $this->title;
        print '</span>'."\n";
        print '</div>'."\n";
        break;
      }
      default: {
        print '<a ';
        //print   'href="javascript:return false;" ';
        if (null != $this->hint)
          print 'title="'.$this->hint.'" ';
        print '>';
        print '<span ';
        print   'class="book-span book-span-'.$this->type.'" '; 
        //print   'onmouseover="skin_bookEnter(this);" ';
        //print   'onmouseout="skin_bookExit(this);" ';
        print   'onclick="svc_loadDiv(\'bookshelf-canvas-container\',\'edit-book\',\'book='.$this->id.'&format=html\');" ';
        if (null != $this->dom_id) 
          print 'id="'.$this->dom_id.'" ';
        print '>';
        print "\n";
        print $this->title;
        print "\n";
        print '</span></a>';
        print "\n";
        break;
      }
    }
  }

  /* Method to create a new asset path */
  function createAsset($type='html',$ref_date=null,$qualifier=null)
  {
    /* Step 1: make sure we have a date */
    if (null == $ref_date) $ref_date = new DateTime();
    /* Step 2: Get base dir and filename */
    switch($this->type) {
      case BOOK_TYPE_SERMON: {
          /* Sermons assets are very rare; the path is a year-based
           * directory, with month-day naming.
           */
        $asset_dir = config_getBookDir($this->id)."/".$ref_date->format("Y");
        $asset_root = $ref_date->format("m-d");
        break;
      }
      case BOOK_TYPE_CALENDAR: {
          /* Calendars are organized by month, with a root element
           * for each day.
           */
        $asset_dir = config_getBookDir($this->id)."/".$ref_date->format("Y")."/".$ref_date->format("m");
        $asset_root = $ref_date->format("m-d");
        break;
      }
      case BOOK_TYPE_ALBUM: {
          /* Photos will be updated sporadically, but may be a lot
           * at a time. Also, we allow for different versions of 
           * the photos by creating assets in the "originals" subdir.
           */
        $asset_dir = config_getBookDir($this->id)."/".$ref_date->format("Y")."/".$ref_date("m-d")."/originals";
        $asset_root = $ref_date->format("Hi");
        break;
      }
      default : {
          /* For everything else, we expect from zero to a few assets
           * per day; create a month directory with daily roots.
           */
        $asset_dir = config_getBookDir($this->id)."/".$ref_date->format("Y/m");
        $asset_root = $ref_date->format("d");
        break;
      }
    } /* End switch */

    /* Step 3: If necessary, create the asset directory */
    if (!is_dir($asset_dir)) {
      if (FALSE == mkdir($asset_dir,0755,true)) return null;
    }

    /* Step 4: Check for simple case -- simple file create */
    $asset_file = $asset_dir."/".$asset_root.".".$type;
    if (!file_exists($asset_file)) {
      if (TRUE == touch($asset_file)) return $asset_file;
      /* If we're here, something has gone crazy wrong. */
      return null;
    }

    /* Step 5: simple case failed; tack on a counter suffix */
    $file_iter = 1;
    do {
      $asset_file = sprintf("%s/%s_%03d.%s",$asset_dir,$asset_root,$file_iter,$type);
      $file_iter++;
    } while (file_exists($asset_file) && ($file_iter < 1000));
    if ($file_iter >= 1000) return null; // All used up; should never happen
    if (FALSE == touch($asset_file)) {
      return null; // Available, but something failed.
    }
    // All good!
    return $asset_file;
  }
}

/*
 * akBookshelf -- Bookshelf object
 */
class akBookshelf
{
  /* Basic public members */
    /* Required */
  public $dom_class; // Class of DOM object(s)
  
    /* Optional */
  public $dom_id; // ID of DOM object
  public $title;  // Bookshelf lable/title

  /* Basic private members */
  private $books; // What's on this shelf?

  /* Constructor */
  function __construct($dom_class="book-shelf",$dom_id=null)
  {
    /* Step 1: Copy elements from args */
    $this->dom_class=$dom_class;
    $this->dom_id=$dom_id;
    $this->title="Shelf";

    /* Step 2: Init private members */
    $this->books=array(); /* Create empty array */
  }

  /* Method to emit the HTML for this object */
  function emit()
  {
    /* Open the DIV and UL elements */
    print '<div ';
    print   'class="'.$this->dom_class.'" '; 
    if (null != $this->dom_id) 
      print 'id="'.$this->dom_id.'" ';
    print '>';
    print "\n";
    /* If we have a title, emit it as an item with title class */
    if (null != $this->title) {
      print '<div ';
      print   'class="'.$this->dom_class.'-title" ';
      print   '>';
      print   $this->title;
      print '</div>';
      print "\n";
    }
    /* Emit all children */
    foreach ($this->books as $book) {
      $book->emit();
    }
    /* Close all elements */
    print '</div>';
    print "\n";
  }

  /* Method to add a book to the shelf */
  function adopt($book) 
  {
      $this->books[]=$book;
  }

  /* Method to add a title to the menu */
  function setTitle($title=null)
  {
    $this->title = $title;
  }
}

/* 
 * Function to create a new file for storing a book-specific asset 
 *
 * @param book_id -- ID of book whose asset we're creating
 * @param asset_type -- Asset type (file suffix, generally)
 * @param ref_date -- Reference date (DateTime object)
 * @param qualifier -- Any book-specific qualifier
 * @param contents -- If non-null, contents to write to asset file
 *
 * @return Fully-qualified path for new asset on success, null on failure.
 *
 * This function chooses and creates the file, creating intermediate
 * directories if needed.  The call will fail if the user does not
 * have at least PERM_WRITE (create/delete stories) access.  If the 
 * 'contents' parameter is non-null and the write fails, the file will
 * be deleted and an error returned.
 *
 * Different books types have different asset storage models, so
 * we must look up the book in the DB to get its type.  The common 
 * feature of most models, however, is a reference date; for some 
 * books, the date is _NOW_, but for some (e.g. calendars) the date 
 * is the event date.
 */
function book_createAsset($book_id=0,$asset_type="html",$ref_date=null,$qualifier=null,$contents=null)
{
  /* Step 0: Validation */
  if (0 == $book_id) {
    config_setLastError('Bad book id');
    return null;
  }

  /* Step 1: Get book info from DB */
  $book = null;
  $booklist = db_booklist(null,PERM_WRITE,$book_id);
  if ($booklist) $book = $booklist[$book_id];
  if (null == $book) {
    config_setLastError('Book not found or insufficient permissions.');
    return null; // Not found or no access
  }

  /* Step 2: Call object method to create file */
  $asset=$book->createAsset($asset_type,$ref_date,$qualifier);
  if (null == $asset) {
    config_setLastError('Asset creation failed.');
    return null;
  }

  /* Step 3: If we have contents, push them now */
  if (null != $contents) {
    if (FALSE == file_put_contents($asset,$contents)) {
      /* Write failed; delete the asset and return */
      unlink($asset);
      config_setLastError('Asset contents could not be stored.');
      return null;
    }
  }

  /* Step 4: We're all good here, just return the new path */
  return $asset;
}

?>
