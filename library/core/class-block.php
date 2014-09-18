<?php
/****************************************************************************
 * class-block.php -- Block (sidebar/multibar) object definitions
 ***************************************************************************/
  // Block types
define("BLOCK_TYPE_FULL","full");   // Full-width block
define("BLOCK_TYPE_SIDEBAR","sidebar");   // Sidebar block
define("BLOCK_TYPE_MULTIBAR","multibar"); // Multibar block
define("BLOCK_TYPE_WORKAREA","workarea"); // Workarea (app+multibar)
define("BLOCK_TYPE_APP","app");           // App area block
define("BLOCK_TYPE_RAW","raw");           // Raw block, no decoration
define("BLOCK_TYPE_BOOKSHELF","bookshelf"); // Bookshelf canvas block

/*
 * A "block" has the following structure:
 *
 * <BLOCK>
 *   <BLOCK-PREFIX/>
 *   <TITLE>
 *     <TITLE-PREFIX>
 *     <TITLE-BODY>
 *     <TITLE-SUFFIX>
 *   </TITLE>
 *   <CONTENT>
 *     <CONTENT-PREFIX>
 *     <CONTENT-BODY>
 *     <CONTENT-SUFFIX>
 *   </CONTENT>
 *   <BLOCK-SUFFIX/>
 * </BLOCK>
 * 
 */
/*
 * akBlock -- Block object
 *
 * A 'block' is a container for general content; the block is
 * rendered according to skin rules.  All components are rendered,
 * even if no content is available for that component; this allows
 * the skin to handle all bits completely.
 */
class akBlock
{
  /* Basic public members */
    /* Required */
  public $title;       // Block titlebar -- may be HTML
  public $provider;    // PHP script providing content
  public $type;        // What type of block is this
    /* Optional */
  public $dom_id; // ID of DOM object
  public $prefix;   // Block prefix 
  public $suffix;   // Block suffix
  public $title_prefix;
  public $title_suffix;
  public $content_prefix;
  public $content_suffix;
  
  /* Private parameters */
  private $opts;    // Block parameters

  /* Constructor */
  function __construct($type=BLOCK_TYPE_SIDEBAR,$provider=null,$title=null)
  {
    /* Step 1: Copy elements from args */
    $this->type = $type;
    $this->provider = $provider;
    $this->title = $title;

    /* Step 2: Generic init */
    $this->dom_id=null;
    $this->prefix=null;
    $this->suffix=null;
    $this->title_prefix=null;
    $this->title_suffix=null;
    $this->content_prefix=null;
    $this->content_suffix=null;

    /* Step 3: Internal parameter set */
    $this->opts = array();
    $this->opts['block-width'] = config_getParameter("skin_width_".$this->type,0);

  }

  /* Method to set a parameter */
  function setParameter($parameter="option",$value='')
  {
    $this->opts[$parameter] = $value;
  }

  /* Method to add a link to the title */
  function setTitleLink($targetURL="/",$hint="")
  {
    $this->title_prefix='<a class="block-title-link" href="'.$targetURL.'" title="'.$hint.'">';
    $this->title_suffix='</a>';
  }

  /* Method to emit the HTML for this object */
    /* NOTE: The mouseover/mouseout functions may be provided by the skin */
  function emit()
  {
    /* Step 1: Open overall wrapper  */
    print "\n<div ";
    if (null != $this->dom_id)
      print 'id="'.$this->dom_id.'" ';
    print   'class="block-'.$this->type.'" ';
    //print   'onmouseover="skin_blockEnter(this);" ';
    //print   'onmouseout="skin_blockExit(this);" ';
    print ">";
    /* Step 2: Emit block prefix */
    if ($this->type != BLOCK_TYPE_RAW) {
      $this->emit_prefix();
    }
    /* Step 3: Emit block title (if not raw) */
    if ($this->type != BLOCK_TYPE_RAW) {
      $this->emit_title();
    }
    /* Step 4: Emit the content */
    $this->emit_content();
    /* Step 5: Emit suffix  */
    if ($this->type != BLOCK_TYPE_RAW) {
      $this->emit_suffix();
    }
    /* Step 6: Close the overall wrapper */
    print '</div>';
  }

  /* Method to emit the prefix DIV for a block */
  function emit_prefix()
  {
    print '<div class="block-prefix block-prefix-'.$this->type.'">';
    print $this->prefix;
    print '</div>';
  }

  /* Method to emit the suffix DIV for a block */
  function emit_suffix()
  {
    print '<div class="block-suffix block-suffix-'.$this->type.'">';
    print $this->suffix;
    print '</div>';
  }

  /* Method to emit the title for a block */
  function emit_title()
  {
    print '<div class="block-title block-title-'.$this->type.'">';
    print $this->title_prefix;
    print $this->title;
    print $this->title_suffix;
    print '</div>';

  }

  /* Method to emit the content for the block */
  function emit_content()
  {
    /* Save options as parameters */
    $GLOBALS['block-parameters'] = &$this->opts;
    print '<div class="block-content block-content-'.$this->type.'">';
    print $this->content_prefix;
    @include($this->provider); 
    print $this->content_suffix;
    print '</div>';
  }
}


/*
 * Function to retrieve a block parameter
 */
function block_getParameter($which='option',$default=null)
{
  if (!isset($GLOBALS['block-parameters'][$which])) {
    return($default);
  }
  return($GLOBALS['block-parameters'][$which]);
}

?>
