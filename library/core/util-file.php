<?php
/****************************************************************************
 * util-file.php -- Filesystem support functions
 ***************************************************************************/
/* Includes */


/***********************************************
 * akFile is a base class for text file operations
 * (config files, simple datastores), that is meant
 * to be subclassed for specific file types.
 *
 * Mostly, this will have to do with how lines are
 * processed before writing or after reading.
 */
class akTextFile {
  private $handle;

  /* Base constructor.  Args are same as fopen() */
  public function __construct($path,$access="r") {
     $this->handle = @fopen($path,$access);
  }

  public function close() {
     @fclose($this->handle);
  }

  /* Basic text line processing.  Subclasses should override this
     for comments, whitespace, etc.

     Return FALSE if the line should be ignored.
   */
  public function decodeText($line) {
    return $line;
  }
  public function encodeText($line) {
    return $line;
  }

  public function getLine($rawRead=false)
  {
    if ((FALSE==$this->handle) || (feof($this->handle))) return null;

    while (!feof($this->handle)) {
      /* Get a line */
      if (($buffer = fgets($this->handle,512)) == false) return null;
      /* If we're reading raw, just return it */
      if ($rawRead) return $buffer;
      /* Otherwise, decode and check for discard */
      if (FALSE == ($buffer = $this->decodeText($buffer))) continue;
      /* If we're good, return it! */
      return $buffer;
    }

    /* Nothing left */
    return null;
  }
}
