<?php
/****************************************************************************
 * util-image.php -- Generic image utilities
 *
 ***************************************************************************/

/**
 * akimg_emit_constrained() -- Make sure an image fits into the skin's hints
 *
 * $path -- Path to image file
 * $alt -- ALT text
 */
function akimg_html_constrained($path=null,$alt='untitled',$bound=0)
{
  /* Step 1: Base validation */
  if ((null == $path) || (!is_readable($path))) return '';

  /* Step 2: Secondary validation */
  $d = getimagesize($path);
  if (FALSE == $d) return '';

  /* Step 3: Get bounding side from skin or args */
  if ($bound == 0) {
    $side = config_getParameter('skin_img_limit',500);
  } else {
    $side = $bound;
  }
  
  /* Step 4: If it fits already, just return with defaults */
  if ($d[0] <= $side && $d[1] <= $side) {
    /* Fits.  No size info given. */
    return '<img src="'.$path.'" alt="'.$alt.'"/>';
  }

  /* Step 5: Scale down based on largest dimension */
  if ($d[0] > $d[1]) {
    /* "Wide" */
    $w = $side;
    $h = round($d[1] * ($w / $d[0]));
  } else {
     /* "Tall" */
    $h = $side;
    $w = round($d[0] * ($h / $d[1]));
  }

  /* Step 6: Return with new size */
  return '<img src="'.$path.'" alt="'.$alt.'" width="'.$w.'" height="'.$h.'"/>';

}
