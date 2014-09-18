<?php
/****************************************************************************
 * class-notebook.php -- Menu object definitions
 ***************************************************************************/
require_once("library/core/class-string.php");

/*
 * akNotePage -- Notebook page
 *
 * Each page of a notebook is a <DIV> item with generic contents.
 * The visibility of each page is controlled by the parent notebook.
 * The contents of each page are an array of objects that support 
 * an emit() method.
 */
class akNotePage
{
  /* Required public members */
  public $token; // Notebook-unique token identifying this page
  public $label; // Text label to show in tab bar or other display
  public $tooltip;  // Tooltip to show on hover
  public $lastSimpleNote; // Last note added with the simple interface

  /* Optional public members */
  public $dom_class; // Class only -- ID is generated from token

  /* Private members */
  private $notes; // Array of objects for the page

  /* Constructor */
  function __construct($token=null,$label=null,$tooltip=null)
  {
    /* Step 1: Copy elements from args */
    $this->token=$token;
    if ($label == null) $this->label = $this->token;
    else $this->label=$label;
    $this->tooltip=$tooltip;

    /* Step 2: Initialize other elements */
    $this->dom_class = null;

    /* Step 2: Initialize the notes on the page */
    $this->notes = array();

  }

  /* Method to add a child note to a page */
  function adopt($note=null) 
  {
    $this->notes[] = $note;
  }

  /* Method to emit the HTML for this object */
    /* NOTE: This is _only_ the contents -- the parent
     *  object will emit a container DIV.
     */
  function emit()
  {
    /* Emit all notes for this page */
    foreach ($this->notes as $note) {
      $note->emit();
    }
  }
}

/*
 * akNotebook -- Notebook object
 *
 * A Notebook is comprised of a control area (tab-bar, menu, etc) and
 * a display area.  Each control selects a <DIV> to show, hiding all the
 * other DIVs.  This produces an effect similar to a notebook UI widget.
 *
 * Unlike other containers, this object does not support a createSimpleXXX
 * interface, because the client objects are necessarily complex.
 */
class akNotebook
{
  /* Basic public members */
    /* Required */
  public $dom_id; // ID of DOM object
    /* Optional */
  public $dom_class; // Class of DOM object(s)

  /* Basic private members */
  private $pages; // Array of pages

  /* Constructor */
  function __construct($dom_id = null)
  {
    /* Step 1: Copy elements from args */
    if ($dom_id == null) $this->dom_id = 'notebook';
    else $this->dom_id=$dom_id;

    /* Step 2: Optional members */
    $this->class = null;

    /* Step 2: Init private members */
    $this->pages=array(); /* Create empty array */
  }

  /* Method to emit the HTML for this object */
  function emit()
  {
    /* Open the DIV and UL elements */
    print '<div ';
    print   'id="'.$this->dom_id.'" ';
    if (null != $this->dom_class) 
    print   'class="'.$this->dom_class.'" '; 
    print '>';

    /* Emit the notebook tabs as a styled list */
    print '<ul ';
    print   'id="'.$this->dom_id.'-tab-list" ';
    print '  class="notebook-tab-list" ';
    print '>';
    /* Emit the labels as list items */
      /* NOTE: First page is active, and use skin-specific method 
       *       for switching pages.
       */
    $idx = 0;
    foreach($this->pages as $page) {
      print '<li ';
      print   'id="'.$this->dom_id.'-tab-'.$page->token.'" ';
      if ($idx == 0)
        print '  class="notebook-tab-active" ';
      else
        print '  class="notebook-tab" ';
      print   'onclick="skin_notebookTabClick(this);" ';
      print   'onmouseover="skin_notebookTabEnter(this);" ';
      print   'onmouseout="skin_notebookTabExit(this);" ';
      print '>';  
      print $page->label;
      print '</li>';
      $idx++;
    }
    /* Close the tabs list */
    print '</ul>';
    /* Separator div */
    print '<div class="closure-div"></div>';
    /* Emit each tab's content (only the first should be visible */
    $idx = 0;
    foreach($this->pages as $page) {
      print '<div ';
      print   'id="'.$this->dom_id.'-page-'.$page->token.'" ';
      if ($idx == 0)
        print '  class="notebook-page-active" ';
      else
        print '  class="notebook-page" ';

      print '>';
      $page->emit();
      print '</div>';
      $idx++;
    }

    /* Close the overall DIV */
    print '</div>';
    print "\n";
  }

  /* Method to add a child element to an item */
  function adopt($page) 
  {
      $this->pages[]=$page;
  }
}
?>
