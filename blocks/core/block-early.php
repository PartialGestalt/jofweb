<?php
/* Step 1: Enable output buffering */

$tidyopts = array('indent' => FALSE,
                  'wrap' => 0,
                  'hide-comments' => TRUE);

$extraHeaders = '';

function finalize_headers($outstuff)
{
  global $extraHeaders;
  return str_ireplace('</head>',"<botox></botox>".$extraHeaders."\n".'</head>',$outstuff);
}

function finalize_output($outstuff)
{
  global $tidyopts;
  return (finalize_headers($outstuff));
  //return(tidy_repair_string($outstuff,$tidyopts));
}
ob_start(finalize_output);

/* Step 2: Some core HTML api bits */

/* Step 2.1: Allow redirects */
  /* MUST have output buffering on! */
function core_redirect($url)
{
   /* Wipe the previously buffered output */
   while (ob_get_level()) ob_end_clean();
   /* Add our redirect header */
     /* NOTE: HTTP 1.1 requires a full URI, scheme and all */
   $host=$_SERVER['HTTP_HOST'];
   header("Location: http://$host$url",true,303);
   ob_end_flush();
   exit();
}

/* Step 2.2: Add per-page bits in the header */
function core_addScriptFile($type, $url)
{
  global $extraHeaders;
  $extraHeaders += '<script type="'.$type.'" src="'.$url.'"></script>'."\n";
}
function core_addScriptCode($type, $codeString)
{
  global $extraHeaders;
  $extraHeaders += '<script type="'.$type.'">'."\n".$codeString."\n".'</script>';
}

/* Step 3: Authentication handling */
require_once("library/core/util-auth.php");
switch ($_REQUEST['auth-action'])
{
  case 'logout': auth_logout(); break;
  case 'login':  auth_login(); break;
  default:       auth_validate(); break;
}

/* Step 4: Gather/store skin information */
require_once("library/core/util-skin.php");
skin_storeName();
skin_include("parameters.php");

/* Step 5: Import commonly used classes */
require_once("library/core/class-block.php");
require_once("library/core/class-story.php");
?>
