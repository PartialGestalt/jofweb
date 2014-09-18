<?php
/****************************************************************************
 * util-skin.php -- Skin support functions
 ***************************************************************************/
  /* Set the default skin */
define("DEFAULT_SKIN","agua");

/* Includes */
require_once("library/core/util-config.php");
require_once("library/core/util-auth.php");

$cache_skin='';

/**
 * skin_getName() -- Return the name of the currently-selected skin
 *                   (defaults to DEFAULT_SKIN)
 */
function skin_getName()
{
  global $cache_skin;
  global $auth_user;

  /* Step 1: check cache */
  if ('' != $cache_skin) return $cache_skin;

  /* Step 2: Get requested value from config sources */
  $skin_name=config_getValue("skin");

  /* Step 3: If not in config sources, check authenticated user prefs */
  if (is_authenticated() && ($skin_name == 'default')) {
    $skin_name = $auth_user->skin;
  }

  /* Step 4: Validate */
  if ((FALSE == file_exists("skins/".$skin_name."/default.css")) ||
      ($skin_name == 'default'))
  {
    $skin_name=DEFAULT_SKIN;
  }

  $cache_skin = $skin_name;

  return $skin_name;
}

/**
 * skin_storeName() -- Store a skin name given or part of the URL
 */
function skin_storeName($skin_name=null)
{
  // Get configuration value
  if (null == $skin_name) $skin_name=config_getValue("skin",CONFIG_SOURCE_URL);
  // Store for a bit less than 10 years...
  $expire_time=time()+300000000;
  if ($skin_name != "default") setcookie(CONFIG_COOKIE_PREFIX."skin","$skin_name",$expire_time);
  return;
}

/**
 * skin_include() -- Include a skin-specific file (fixed name)
 */
function skin_include($filename)
{
  /* Step 1: build filepath */
  $_skin_inc_file="skins/".skin_getName()."/".$filename;

  /* Step 2: Include if readable */
  if (is_readable($_skin_inc_file)) {
    @include($_skin_inc_file);
  }
}

/**
 * skin_img() -- Emit HTML for a skin-specific image
 *
 * NOTE: We do no error check here -- just implement the HTML emit.
 */
function skin_img($name=null,$alt='Image',$class=null,$dom_id=null)
{
  /* Step 1: Straight up emit */
  print '<img ';
  print '  src="skins/'.skin_getName().'/'.$name.'" ';
  print '  alt="'.$alt.'" ';
  if (null != $class)
    print 'class="'.$class.'" ';
  if (null != $dom_id)
    print 'id="'.$dom_id.'" ';
  print '/>';
}
?>
