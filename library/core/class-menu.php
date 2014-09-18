<?php
/****************************************************************************
 * class-menu.php -- Menu object definitions
 *
 ***************************************************************************/

/*
 * akMenuItem -- Menu Item
 *
 * Each akMenuItem is an <A> element inside a <LI> element;
 * both elemetns share the $dom_class, but the <LI> element
 * is the only one tagged with the (optional) id.
 * The mouseover/mouseout methods MUST be provided by the skin.
 */
class akMenuItem
{
  /* Basic public members */
    /* Required */
  public $dom_class; // Class of DOM object
  public $label;     // Text label to show
  public $url;       // Target URL
    /* Optional */
  public $dom_id; // ID of DOM object
  public $alt;       // ALT (or TITLE) text

  /* Basic private members */
  private $child; // Child object (usually a menu)

  /* Constructor */
  function __construct($dom_class="menu-item",$label="Entry",$url="/",$alt=null,$dom_id=null)
  {
    /* Step 1: Copy elements from args */
    $this->dom_class=$dom_class;
    $this->label=$label;
    $this->url=$url;
    $this->alt=$alt;
    $this->dom_id=$dom_id;

    /* Step 2: Init private members */
    $this->child=null;
  }

  /* Method to add a child element to an item */
  function adopt($child) 
  {
      /* CLEAN: Verify that $child is akMenu class? */
      $this->child=$child;
  }

  /* Method to emit the HTML for this object */
    /* NOTE: The mouseover/mouseout functions are provided by the skin */
  function emit()
  {
    print '<li ';
    print   'class="'.$this->dom_class.'" '; 
    print   'onmouseover="skin_menuItemEnter(this);" ';
    print   'onmouseout="skin_menuItemExit(this);" ';
    if (null != $this->dom_id) 
      print 'id="'.$this->dom_id.'" ';
    print '>';
    print   '<a ';
    print     'class="'.$this->dom_class.'" '; 
    print     'href="'.$this->url.'" ';
    if (null != $this->alt)
      print   'title="'.$this->alt.'" ';
    print   '>';
    print $this->label;
    print '</a>';
    if (null != $this->child) {
      // Emit child menu 
      print "\n";
      $this->child->emit();
    }
    print '</li>';
    print "\n";
  }
}

/*
 * akMenu -- Menu Object
 */
class akMenu
{
  /* Basic public members */
    /* Required */
  public $dom_class; // Class of DOM object(s)
    /* Optional */
  public $dom_id; // ID of DOM object
  public $prefix; // Anything to emit beforehand 
  public $postfix; // Anything to emit afterwards

  /* Basic private members */
  private $attach; // How does this menu attach to its parent?
  private $items; // Child items (akMenuItem objects)
  private $title;  // Title text for menu

  /* Constructor */
  function __construct($dom_class="menu-inline",$dom_id=null)
  {
    /* Step 1: Copy elements from args */
    $this->dom_class=$dom_class;
    $this->dom_id=$dom_id;
    $this->prefix = null;
    $this->postfix = null;

    /* Step 2: Init private members */
    $this->attach=null;
    $this->title=null;
    $this->items=array(); /* Create empty array */
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
    /* Handle any prefix (inside the main DIV) */
    if (null != $this->prefix) print $this->prefix;
    print   '<ul ';
    print     'class="'.$this->dom_class.'" '; 
    print   '>';
    print "\n";
    /* If we have a title, emit it as an item with title class */
    if (null != $this->title) {
      print '<li ';
      print   'class="'.$this->dom_class.'-title" ';
      print   '>';
      print   $this->title;
      print '</li>';
      print "\n";
    }
    /* Emit all children */
    foreach ($this->items as $item) {
      $item->emit();
    }
    /* Close all elements */
    print '</ul>';
    /* Handle any postfix (inside the main DIV) */
    if (null != $this->postfix) print $this->postfix;
    print '</div>';
    print "\n";
  }

  /* Method to add a child element to an item */
  function adopt($child) 
  {
      $this->items[]=$child;
  }

  /* Method to create and adopt all at once */
    /* NOTE: This signature should be the same as akMenuItem constructor. */
  function createItem($dom_class=null,$label=null,$url=null,$alt=null,$dom_id=null)
  {
    $this->items[] = new akMenuItem($dom_class,$label,$url,$alt,$dom_id);
  }

  /* Method to create and adopt all at once */
    /* NOTE: This is just like createItem, but inherits the $dom_class
     *       and doesn't allow any alt text or dom_id 
     */
  function createSimpleItem($label=null,$url=null,$alt=null)
  {
    $this->items[] = new akMenuItem($this->dom_class,$label,$url,$alt);
  }

  /* Method to add a title to the menu */
  function setTitle($title=null)
  {
    $this->title = $title;
  }
}
?>
