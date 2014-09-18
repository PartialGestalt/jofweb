<?php
/****************************************************************************
 * class-table.php -- Define a dynamic spreadsheet-style table.
 ***************************************************************************/
/*
 * INCLUDES
 */
require_once("library/core/util-auth.php");
require_once("library/core/util-file.php");

  /* Simple rendering of the existing data */
define("TABLE_OPERATION_DISPLAY","display");
  /* Edit existing table data.  Error if table can't be found */
define("TABLE_OPERATION_EDIT","edit");
  /* Create new table */
define("TABLE_OPERATION_CREATE","create");

  /* TABLE file signature */
define("TABLE_FILE_SIGNATURE","AKTBL");

/*
 * akTableCell -- Table cell object
 */
class akTableCell
{
  public $col; /* Which (zero-based) column this cell is in */
  public $row; /* Which (zero-based) row this cell is in */
    /* There are two levels of formatting: class-based (from skin css), and
       simple (common, predefined formatting tags).
     */
  public $format_class; /* Which (skin-provided) display class to apply */
  public $format_simple; /* Array of simple formatting tags to apply, regardless of class */
  public $contents; /* Actual contents of cell */

  public function __construct($contentString=null) {
       /* Initialize to base values */
      $this->col = 0;
      $this->row = 0;
      $this->format_class = null;
      $this->format_simple = null;
      $this->contents = '';
      if ($contentString != null) {
        list($params,$this->contents) = explode('=',$contentString,2);
        list($this->col,$this->row,$this->format_simple,$this->format_class) = explode(',',$params,4);
      }
  }

  public static function createEmptyCell($col, $row) {
    $instance = new self(null);
    $instance->col = $col;
    $instance->row = $row;
    return $instance;
  }

  /* Emit the HTML for this cell */
  public function emit() {
    /* Opening tag */
    if (null == $this->format_class) {
      print '<td>';
    } else {
      print '<td class="'.$this->format_class.'">';
    }
    /* "Simple" formatting */
    $simple_open = "";
    $simple_close = "";
    foreach (str_split($this->format_simple) as $formatChar) {
       switch($formatChar) {
         case 'b': {
           $simple_start = "<b>";
           $simple_end = "</b>";
           break;
         }
         case 'c': {
           $simple_start = "<center>";
           $simple_end = "</center>";
           break;
         }
         case 'i': {
           $simple_start = "<i>";
           $simple_end = "</i>";
           break;
         }
         default: {
           /* ERROR! What to do here? */
           $simple_start = "";
           $simple_end = "";
         }
       } /* End switch */
       $simple_open = $simple_open.$simple_start;
       $simple_close = $simple_end.$simple_close;
    } /* End simple formatting setup */
    print $simple_open;
    print $this->contents;
    print $simple_close;
    print "</td>\n";
  }

}

/*
 * akTableFile -- subclass the generic text file for our formatting
 */
class akTableFile extends akTextFile {
  /* 
   * Override the line processing.  We strip whitespace, ignore comments, 
   * and decode URL-encoded entities (but not other HTML entities!)
   */
  public function decodeText($line) {
    /* Remove leading/trailing whitespace */
    $line = trim($line);
      /* Ignore empty... */
    if (strlen($line) <= 0) return FALSE;
      /* Ignore comments */
    if (substr($line,0,1) == "#") return FALSE;

    return $line;
  }
}

/*
 * akTable -- Basic table object
 *
 * NOTE: The table cell array may contain values that are outside
 *       the range defined by $rows/$colums, but we control the render
 *       based on the metadata, not the contents.
 */
class akTable 
{
  public $dom_id;   // DOM element ID
  public $operation; // What to do to this table...
  public $title;    // Table title
  public $height;   // Number of table rows
  public $width;    // Number of table columns
  public $backing;  // Backing store (filepath)
  public $editable; // Render as editable? 
  public $cells;    // The array of cells
  public $error;    // Did something go wrong?

  /* Construction */
    /* See the factory methods below, since we can't
       simply overload the constructor (forms are 
       different, but the types of args are the same!)
      */
  public function __construct($newtitle='Table')
  {
    $this->cells = array(array());
    $this->error = null;
    if ($_GET && $_GET["mode"]) {
      $this->operation = $_GET["mode"];
    } else {
      $this->operation = TABLE_OPERATION_DISPLAY;
    }
  }

  public static function fromScratch($newTitle='Table',$colCount='1',$rowCount='1') 
  {
    $instance = new self();
    $instance->title = $newTitle;
    $instance->width = $colCount;
    $instance->height = $rowCount;
    $instance->backing = null;
    return $instance;
  }

  public static function fromFile($backingPath=null) 
  {
    $instance = new self();
    $store = null;

    if (null == $backingPath) {
          $instance->error = "[[Syntax error in fromFile(): no file path given.";
          return;
    }
    switch ($instance->operation) {
      case TABLE_OPERATION_DISPLAY: {
        if (!is_readable($backingPath)) {
          $instance->error = "[[Can't load table data for path ".$backingPath.", giving up.]]";
        } else {
          /* Open, but don't parse yet */
          $store = new akTableFile($backingPath);
        }
        $instance->backing = $backingPath;
        break;
      }
      case TABLE_OPERATION_EDIT: {
        if (!is_readable($backingPath)) {
          $instance->error = "[[Can't load table data for path ".$backingPath.", giving up.]]";
        }
        else if (!is_writable($backingPath)) {
          $instance->error = "[[Table data for ".$backingPath." is read-only.]]";
        } else {
          /* Open, but don't parse yet */
            /* NOTE: Although we may update later, we're only reading right now. */
          $store = new akTableFile($backingPath);
        }
        $instance->backing = $backingPath;
        break;
      }
      case TABLE_OPERATION_CREATE: {
        /* The file doesn't have to exist, but we need to fake some data if it isn't there */
          /* Default is an empty 2x2 table */
        $instance->backing = $backingPath;
        $instance->title = 'Table';
        $instance->width = 2;
        $instance->height = 2;
        $instance->cells = array(array());
        $instance->cells[0][0] = akTableCell::createEmptyCell(0,0);
        $instance->cells[0][1] = akTableCell::createEmptyCell(0,1);
        $instance->cells[1][0] = akTableCell::createEmptyCell(1,0);
        $instance->cells[1][1] = akTableCell::createEmptyCell(1,1);
        break;
      }
      default: {
        $instance->error = "[[Invalid table operation given.]]";
        break;
      }
    } /* End switch */
    /* Bail on any error */
    if (null != $instance->error) return $instance;

    /* Load from file */
    if (null != $store)  {
      /* Get signature */
      if (strcmp("AKTBL",$store->getLine())) {
        $instance->error = "[[Invalid backing store file format.  Can't read ".$backingPath." so I'm quitting.]]";
      } else {
        /* Pull basic metadata */
        $fileVersion = $store->getLine();
        $instance->title = $store->getLine();
        $instance->width = $store->getLine();
        $instance->height = $store->getLine();
        /* Here on down should be cells */
        while (null != ($cellInfo = $store->getLine())) {
          $cell = new akTableCell($cellInfo);
          $instance->cells[$cell->col][$cell->row] = $cell;
        }
      }
      $store->close();
    }
    return $instance;
  }


  /* Method to emit the HTML for a table */
  function emit()
  {
    if ($this->error != null) {
      /* Something went wrong */
      print '<br/><b>TABLE ERROR: '.$this->error.'</b><br/>';
      return;
    }
    print "<center>\n"; /* All tables are centered?*/
    print $this->title."\n";
    print "<table>\n";
    /* Content rows */
    for ($r = 0; $r < $this->height; $r++) {
      print "<tr>\n";
        for ($c =0; $c < $this->width; $c++) {
          if (null != $this->cells[$c][$r]) {
            $this->cells[$c][$r]->emit();
          }
        }
      print "</tr>\n";
    }
    print "</table>\n";
    print "</center>\n";
  }


}


?>
