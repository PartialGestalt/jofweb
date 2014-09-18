<?php
/****************************************************************************
 * util-html.php -- HTML special functions
 ***************************************************************************/
/* Includes */
require_once("library/core/class-string.php");

/* 
 * EMAIL addresses are generally protected
 */
function html_email($addr='nobody@nowhere')
{
  string_obfuscate($addr,OBFUSCATE_CHUNK);
}
/*
 * MAILTO links require some special handling
 */
function html_mailto($addr='nobody@nowhere',$meta=null,$label=null)
{
  /* Step 1: Fill in any gaps */
  if (null == $label) $label=$addr;
  if (null == $meta) $meta=$addr;

  /* Step 2: Create object */
  $m = new akLink('mailto:'.$addr,$label,$meta);
  $m->setObfuscation(OBFUSCATE_CHUNK);
  $m->emit();
}
 
?>
