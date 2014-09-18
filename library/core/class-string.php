<?php
/****************************************************************************
 * class-string.php -- Simple string object
 ***************************************************************************/

  /* Types of string obfuscation */
define("OBFUSCATE_NONE",0);
define("OBFUSCATE_CHUNK",1); /* Simple chunking */
define("OBFUSCATE_EMAIL",2); /* Email addresses */

/* 
 * string_obfuscate() -- Emit a string, with potential obfuscation
 * 
 * Parameters:
 *   string -- The string to emit
 *   mode -- Obfuscation mode
 */
function string_obfuscate($string="",$mode=OBFUSCATE_NONE, $parameter=null)
{
  /* Step 1: Overhead */
  /* Step 2: Switch on type */
  switch($mode)
  {
    case OBFUSCATE_NONE: {
      /* No obfuscation, just print */
      print $string;
      break;
    }
    case OBFUSCATE_CHUNK: {
      /* Split into javscript multi-character chunks */
      if (null != $parameter) $chunksize=$parameter;
      else $chunksize=5;
      // Do simple entity replacement during split
      $chunks = str_split($string,$chunksize);
      print '<script type="text/javascript">';
      foreach ($chunks as $chunk)
      {
        print "document.write('".$chunk."');";
      }
      print '</script>';
      break;
    }
    case OBFUSCATE_EMAIL: {
      /* Replace special chars */
      $outs = array('<','>','@');
      $ins = array('&lt;','&gt;','@');
      $newstr = str_replace($outs,$ins,$string);
      /* Split into javscript multi-character chunks */
      if (null != $parameter) $chunksize=$parameter;
      else $chunksize=5;
      $chunks = str_split($newstr,$chunksize);
      print '<script type="text/javascript">';
      foreach ($chunks as $chunk)
      {
        print "document.write('".$chunk."');";
      }
      print '</script>';
      break;
    }
    default: {
      print 'UNKNOWN_OBFUSCATE('.$string.')';
      break;
    }
  }
  return;
}

/*
 * akString -- String object with emittability.
 *
 * In some containers (notebook pages, e.g.) the contents of the container
 * are an array of objects, whose emit() methods will be called during 
 * render time.  This object class allows a simple string to be used in 
 * those cases.
 */
class akString
{
  /* Required public members */
  public $string; // The sting itself

  /* Constructor */
  function __construct($string=null) {
    $this->string = $string;
  }

  /* Emit method */
  function emit()
  {
    print $this->string;
  }
}

/*
 * akAttribute -- Attribute object, with name and value 
 */
class akAttribute
{
  /* Required public members */
  public $name;
  public $value;

  /* Constructor */
  function __contstruct($name=null,$value=null) {
    $this->name = $name;
    $this->value = htmlentities($value,ENT_QUOTES);
  }

  /* Create a string from contents */
  function __toString()
  {
    $retstr = '(null)';
    if (null == $this->name) return $retstr;
    $retstr = $this->name;
    if (null != $this->value) $retstr .= '="'.$this->value.'" ';
    return $retstr;
  }

  /* Emitter */
  function emit()
  {
    print $this;
  }
}

/*
 * akLink -- Link object, with custom emitter 
 *
 */
class akLink
{
  /* Required public members */
  public $label; // User-visible label (HTML)
  public $url;   // Target of link
  public $meta;  // Pop-up info on mouseover

  /* Optional members */
    /* If obfuscated, the output HTML will not be easily parsable */
  private $ob_type; /* Obfuscation method */
  private $attributes; /* additional attributes for element */

  /* Constructor */
  function __construct($url=null,$label=null,$meta=null) {
    $this->url = $url;
    $this->label = $label;
    $this->meta = htmlentities($meta);
    $this->ob_type = OBFUSCATE_NONE;
  }

  /* Add a general attribute */
  function setAttr($name=null,$value=null)
  {
    if ($name == null) return;
    $attributes[] = new akAttribute($name,$value);
  }

  /* Set the emitter obfuscation mode */
  function setObfuscation($ob_type=OBFUSCATE_NONE)
  {
    $this->ob_type = $ob_type;
  }

  /* Emit method */
  function emit()
  {
    /* Step 1: Generate HTML string */
    $estr =  '<a ';
    /* HREF */
    $estr .=  'href="'.$this->url.'" ';
    /* Title */
    if (null != $this->meta) {
      $estr .= 'title="'.$this->meta.'" ';
    }
    /* Any other attributes */
    foreach ($this->attributes as $a) $estr .= $a;
    /* Close start tag */
    $estr .= '>';
    /* Label */
    $estr .= $this->label;
    /* End tag */
    $estr .= '</a>';
    /* Emit it */
    string_obfuscate($estr,$this->ob_type);
  }
}
?>
