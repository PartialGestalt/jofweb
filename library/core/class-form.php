<?php
/****************************************************************************
 * class-form.php -- Define object classes for forms
 ***************************************************************************/

/*
 * CONSTANTS
 */
  /* Encodings */
define("FORM_FORMAT_URL","application/x-www-form-urlencoded");
define("FORM_FORMAT_BINARY","multipart/form-data");
  /* Processor methods */
define("FORM_METHOD_GET","get");
define("FORM_METHOD_POST","post");
  /* Form generic values */
define("FORM_VALUE_TRUE","true");
define("FORM_VALUE_FALSE","false");
define("FORM_VALUE_UNCHECKED","false");
define("FORM_VALUE_CHECKED","true");
  /* Input field grouping */
define("FORM_GROUP_BOOLEAN","group-boolean"); /* Conditionally visible */
define("FORM_GROUP_FRAME","group-frame");     /* Simple framing */
define("FORM_GROUP_END","group-end");         /* End the most recent group */
  /* Input types */
define("FORM_ITEM_HIDDEN","hidden");
define("FORM_ITEM_BUTTON","button");
define("FORM_ITEM_SUBMIT","submit");
define("FORM_ITEM_FILE","file");
define("FORM_ITEM_CHECK","checkbox");
define("FORM_ITEM_TEXT","text");
define("FORM_ITEM_TEXTAREA","textarea");
define("FORM_ITEM_PASSWORD","password");
define("FORM_ITEM_SELECT","select");
define("FORM_ITEM_RADIO","radio");
define("FORM_ITEM_RAW","raw");
define("FORM_ITEM_DATE","date");
define("FORM_ITEM_TIME","time");
define("FORM_ITEM_IMAGE","image");

/*
 * GLOBAL FUNCTIONS
 */
function form_getField($field)
{
  /* Retrieve a field, with sanity checking */
  if (!isset($_REQUEST[$field])) return null;                                   
  if (null == $_REQUEST[$field]) return null;                                   
  if (strlen($_REQUEST[$field]) < 1) return null;                               
  return $_REQUEST[$field];
}

/*
 * akFormOption -- Individual form option for select/radio
 */
class akFormOption
{
  public $tag;  // Unique tag for this option
  public $label; // Display label for this option 
  public $is_selected; // Hint

  /* Constructor */
  function __construct($tag=null, $label="Option", $is_selected=false)
  {
    $this->tag = $tag;
    $this->label = $label;
    $this->is_selected = $is_selected;
  }
}

/*
 * akFormItem -- Individual form element, union of all item types
 */

class akFormItem
{
  /* Basic public members */
  public $id;    // Item identifier (DOM)
  public $name;  // Item name (form field name)
  public $type;  // Item type (hidden, text, button, etc.) 
  public $value; // Currently-assigned value for this form item
  public $label; // If non-empty, label for item (makes a new DOM element)
  public $hsize;  // Size attribute (columns)
  public $max_hsize; // Max-size attribute (columns)
  public $vsize;     // Size attribute (rows)
  public $prefix; // HTML for cell preceeding item's cells
  public $suffix; // HTML for cell following item's cells

  /* Non-standard bits */
  public $hint;  // Form element hint (placeholder, perhaps)
  private $style; // Local item style (USE WITH CAUTION)
  private $rowStyle; // Overall row (label+item) style (USE WITH CAUTION)
  private $events; // Events and handlers
  private $options; // For select or radio items
  public $lastOption; // Most recently-added option

  /* Constructor */
  function __construct($id=null, $type=null, $name=null, $value=null, $label=null)
  {
    /* Step 1: Load basic member info */
      /* CLEAN: Throw exception on bad inits? */
    $this->id = $id;
    $this->type = $type;
    $this->name = $name;
    $this->value = $value;
    $this->label = $label;

    /* Step 2: Interpretive defaults, should be overwritten */
    $this->prefix=null;
    $this->suffix=null;
    $this->hsize=null;
    $this->vsize=null; 
    $this->max_hsize=256; // CLEAN: Make this a constant? 
    $this->hint = null; 
    $this->style = null;
    $this->events = array();
    $this->options = null;
  }

  function addStyle($parm=null,$value=null)
  {
    if ((null != $parm) && (null != $value))
      $this->style .= $parm.':'.$value.';';
  }

  function addRowStyle($parm=null,$value=null)
  {
    if ((null != $parm) && (null != $value))
      $this->rowStyle .= $parm.':'.$value.';';
  }

  function addEvent($event=null,$handler=null)
  {
    if (($event != null) && ($handler != null)) {
      $this->events[$event] = $handler;
    }
  }

  function addOption($tag=null,$label="Option",$is_selected=false)
  {
    $newOpt = new akFormOption($tag,$label,$is_selected);
    $this->options[] = &$newOpt;
    $this->lastOption = &$newOpt;
  }

  /* Method to emit the HTML for this item */
    /* NOTE: Each item takes up a 4-cell row in the
     *       form table: prefix,label,content,suffix.
     */
  function emit()
  {
    /* For all other items, build a table row */
    /* Row container */
    print '<tr ';
    print '    class="form-item-row form-item-'.$this->type.'-row" ';
    print '    id="'.$this->id.'-row" ';
    if (null != $this->rowStyle)
      print '  style="'.$this->rowStyle.'" ';
    print '>'."\n";
    /* Prefix cell */
    print '<td ';
    print '   class="form-item-prefix" ';
    print '    id="'.$this->id.'-prefix" ';
    print '>';
    if (null != $this->prefix) print $this->prefix;
    print '</td>';
    /* Label cell (if not raw HTML content) */
    if ($this->type != FORM_ITEM_RAW) {
      print '<td ';
      print '   class="form-item form-item-label" ';
      print '   id="'.$this->id.'-label" ';
      print '>';
      /* Radio buttons have label on the inside... */
      if ((null != $this->label) && ($this->type != FORM_ITEM_RADIO)) {
        print '<label class="form-item-label" for="'.$this->id.'">'.$this->label."</label>\n";
      }
      print '</td>';
      /* Contents cell */
      print '<td ';
      print '   class="form-item-contents" ';
      print '   id="'.$this->id.'-contents" ';
      print '>';
    } else {
      // For RAW, both cells are the raw content
      print '<td class="form-item-raw" colspan="2">';
    }
    /* Switch on type */
    switch($this->type) {
      case FORM_ITEM_RAW: {
        // Extra bits inside the form...
        print '<div ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-raw" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print '>';
        print $this->value;
        print '</div>';
        break;
      }
      case FORM_ITEM_HIDDEN: {
        // Hidden field for passing data 
        print '<input ';
        print '  type="hidden" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-hidden" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        break;
      }   
      case FORM_ITEM_TEXT: {
        // Simple text entry
        print '<input ';
        print '  type="text" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-text" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->hsize)
          print 'size="'.$this->hsize.'" ';
        print '  maxlength="'.$this->max_hsize.'" ';
        if (null != $this->hint) 
          print 'placeholder="'.$this->hint.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        break;
      }
      case FORM_ITEM_CHECK: {
        // Checkbox
        print '<input ';
        print '  type="checkbox" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-check" ';
        print '  name="'.$this->name.'" ';
        if ($this->value == FORM_VALUE_CHECKED) {
          print 'checked ';
        }
        print '  value="'.$this->value.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        break;
      }
      case FORM_GROUP_BOOLEAN: {
        // Boolean control for item grouping; implement (for now) as a
        // checkbox with an invisible span for storage.  The form method
        // to start a new group must have added the controlling event.
        print '<input ';
        print '  type="checkbox" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-group-boolean" ';
        print '  name="'.$this->name.'" ';
        if ($this->value == FORM_VALUE_CHECKED) {
          print 'checked ';
        }
        print '  value="'.$this->value.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        // Tag storage here
        print '<span ';
        print '  id="'.$this->id.'-storage" ';
        print '  class="form-item-storage" ';
        print '  style="display:none;" ';
        print '>';
        foreach ($this->options as $ref)
          print $ref->tag.',';
        print '</span>';
        break;
      }
      case FORM_ITEM_DATE: {
        // Date item; text entry + datepicker
        print '<input ';
        print '  type="text" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-text" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->hsize)
          print 'size="'.$this->hsize.'" ';
        print '  maxlength="'.$this->max_hsize.'" ';
        if (null != $this->hint) 
          print 'placeholder="'.$this->hint.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        print '<a class="frameless" title="Select a date" href="javascript:">';
        skin_img("form-datepick.png","Select a date","frameless",$this->id."-trigger");
        print '</a>';
        print "\n".'<script type="text/javascript">'."\n";
        print "akForm_registerPopup('".$this->id."','".$this->id."-trigger','form-date',null);\n";
        print '</script>';
        break;
      }
      case FORM_ITEM_TIME: {
        // Time item: hidden field to store value, with 
        // selectors for components.
        // CLEAN: TODO: Use input value to set fields.
          // Hidden field for passing data 
        print '<input ';
        print '  type="hidden" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-hidden" ';
        print '  name="'.$this->name.'" ';
        //print '  value="'.$this->value.'" ';
        print '  value="190000" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
          // Hour selector
        print '<select ';
        print '  id="'.$this->id.'-hour" ';
        print '  class="form-item form-item-time" ';
        print '  name="'.$this->name.'-hour" ';
        print '  onChange="akForm_timepickerChange(\''.$this->id.'\');"';
        print '>';
        print '<option value="12">12</option>'."\n";
        for ($iter=1;$iter<12;$iter++) {
            print '<option ';
            if ($iter==7) print 'selected="true" ';
            print 'value="'.$iter.'">'.$iter.'</option>'."\n";
        }
        print '</select>';
          // Minute selector
        print '<select ';
        print '  id="'.$this->id.'-minute" ';
        print '  class="form-item form-item-time" ';
        print '  name="'.$this->name.'-minute" ';
        print '  onChange="akForm_timepickerChange(\''.$this->id.'\');"';
        print '>';
        print '<option value="0" selected="true">:00</option>'."\n";
        print '<option value="15">:15</option>'."\n";
        print '<option value="30">:30</option>'."\n";
        print '<option value="45">:45</option>'."\n";
        print '</select>';
          // AM/PM selector
        print '<select ';
        print '  id="'.$this->id.'-ampm" ';
        print '  class="form-item form-item-time" ';
        print '  name="'.$this->name.'-ampm" ';
        print '  onChange="akForm_timepickerChange(\''.$this->id.'\');"';
        print '>';
        print '<option value="0">AM</option>'."\n";
        print '<option value="12" selected="true">PM</option>'."\n";
        print '</select>';
          // noon/midnight indicator
        print '<span class="form-item-note" id="'.$this->id.'-note">';
        print '</span>';
        break;
      }
      case FORM_ITEM_TEXTAREA: {
        // Multiline text entry 
        print '<textarea ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-textarea" ';
        print '  name="'.$this->name.'" ';
        if (null != $this->hsize)
          print 'cols="'.$this->hsize.'" ';
        if (null != $this->vsize)
          print 'rows="'.$this->vsize.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        print $this->value;
        print '</textarea>';
        break;
      }
      case FORM_ITEM_SELECT: {
        print '<select ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-select" ';
        print '  name="'.$this->name.'" ';
        if (null != $this->hsize)
          print 'size="'.$this->hsize.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print ">\n";
        if ($this->options != null) foreach($this->options as $option) {
          print '<option ';
          print   'value="'.$option->tag.'" ';
          if ($option->is_selected)
            print 'selected="true" ';
          print '>';
          print $option->label;
          print '</option>'."\n";
        }
        print '</select>';
        break;
      }
      case FORM_ITEM_RADIO: {
        // Radio button
        print '<input ';
        print '  type="radio" ';
        //print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-radio" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->hint) 
          print '  checked="'.$this->hint.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>".$this->label."\n";
        break;
      }
      case FORM_ITEM_PASSWORD: {
        // Password field
        print '<input ';
        print '  type="password" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-password" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->hsize)
          print 'size="'.$this->hsize.'" ';
        print '  maxlength="'.$this->max_hsize.'" ';
        if (null != $this->hint) 
          print '  placeholder="'.$this->hint.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        break;
      }
      case FORM_ITEM_BUTTON: {
        // Generic button (remember to add an onClick event)
        print '<input ';
        print '  type="button" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-button" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        break;
      }
      case FORM_ITEM_SUBMIT: {
        // Submit button 
        print '<input ';
        print '  type="submit" ';
        print '  id="'.$this->id.'" ';
        print '  class="form-item form-item-submit" ';
        print '  name="'.$this->name.'" ';
        print '  value="'.$this->value.'" ';
        if (null != $this->style)
          print 'style="'.$this->style.'" ';
        foreach(array_keys($this->events) as $evkey)
          print $evkey.'="'.$this->events[$evkey].'" ';
        print "/>\n";
        break;
      }
    }
    print '</td>';
    // Suffix cell
    print '<td ';
    print '    class="form-item-suffix" ';
    print '    id="'.$this->id.'-suffix" ';
    print '>';
    if (null != $this->suffix) print $this->suffix;
    print '</td>';

    print '</tr>'."\n";
  }
}

/*
 * akForm -- Structural form object
 */
class akForm
{
  /* Basic public members */
  public $id;   // Form identifier (DOM)
  public $method; // Method for processing
  public $action; // URL or service that will process this form
 
  /* Advanced public members */
  public $format; // How to format data 
  public $accept; // For uploads, which types to accept
  public $charset; // What charset the server expects
  public $lastSimpleItem; // Reference to last simple item created

  /* Private members */
  private $items; // Array of items 
  private $lastGroupItem;  // Reference to the last group started

  /* Wrapper public members (customization) */
    /* NOTE: These will be in the form DIV, but
     *       outside the FORM element.
     */
  public $header_text;
  public $header_file;
  public $footer_text;
  public $footer_file;

  /* Event handlers */
      /* CLEAN: Add this */

  /* Constructor */
  function __construct($id=null, $action=null, $method=FORM_METHOD_POST)
  {
    /* Step 1: Load basic member info */
      /* CLEAN: Throw exception on bad inits? */
    $this->id = $id;
    $this->action = $action;
    $this->method = $method;

    /* Step 2: Defaults */
      /* Caller must set these... */
    $this->format=null;
    $this->accept=null;
    $this->charset=null;
    $this->header_text=null;
    $this->header_file=null;
    $this->footer_text=null;
    $this->footer_file=null;
  }

  /* Method to adopt a form item */
  function adopt($item=null)
  {
    if ($item == null) return; // CLEAN: throw a warning here?
    /* Add to array */
    $this->items[] = &$item;
    if (null != $this->lastGroupItem) {
      /* Add this item to current group (if any) */
      $this->lastGroupItem->addOption($item->id);
      /* Make visibility match group */
      if ($this->lastGroupItem->value == FORM_VALUE_UNCHECKED) {
        $item->addRowStyle("display","none");
      }
    }
  }

  /* Method to create and adopt a simple item */
  function createSimpleItem($id=null,$type=null,$name=null,$value=null,$label=null)
  {
    $item = new akFormItem($id,$type,$name,$value,$label);
    $this->adopt($item);
    $this->lastSimpleItem = &$item;
  }

  /* GROUP HANDLING: 
   *
   * WARNING: Groups can't be nested (yet)!
   */
  /* Method to mark the start of an item group */
  function startItemGroup($id=null,$type=null,$label=null,$value=null)
  {
    /* Create base item */
    $item = new akFormItem($id,$type,$id,$value,$label);
    /* Optionally add controller events based on type */
    $item->addEvent("onclick","akForm_handleGroupClick('".$id."');");
    $this->adopt($item);
    $this->lastSimpleItem = &$item;
    $this->lastGroupItem = &$item;
  }

  /* Method to mark the end of an item group */
  function endItemGroup()
  {
    $this->lastGroupItem = null;
  }

  /* Method to emit the HTML for this form */
  function emit()
  {
    print '<div class="form-div">'."\n";
    if (null != $this->header_text) print $this->header_text;
    if (null != $this->header_file) @include($this->header_file);
    print '<form ';
    print '  id="'.$this->id.'" ';
    print '  method="'.$this->method.'" ';
    print '  action="'.$this->action.'" ';
    if (null != $this->format)
      print '  enctype="'.$this->format.'" ';
    if (null != $this->accept)
      print '  accept="'.$this->accept.'" ';
    if (null != $this->charset)
      print '  accept-charset="'.$this->charset.'" ';
    print ">\n";
    /* Loop over all items as a table */
    print '<table class="form-table">';
    foreach ($this->items as $item) $item->emit();
    print "</table>\n";
    print "</form>\n";
    if (null != $this->footer_file) @include($this->footer_file);
    if (null != $this->footer_text) print $this->footer_text;
    print "</div>\n";
  }
}


?>
