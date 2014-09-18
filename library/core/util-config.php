<?php
/****************************************************************************
 * util-config.php -- Generic configuration support
 *
 * For our purposes, a config value is a value associated with some
 * kind of config information.  The config sources (in priority order) are:
 * (1) Direct input (URL request, post value, etc.)
 * (2a) Cookie (no prefix)
 * (2b) Cookie (with prefix)
 * (3) Config file
 *
 ***************************************************************************/
define("CONFIG_COOKIE_PREFIX","jof-");

/*
 * Config sources
 */
define("CONFIG_SOURCE_URL",0x1);
define("CONFIG_SOURCE_COOKIE",0x2);
define("CONFIG_SOURCE_FILES",0x4);
define("CONFIG_SOURCE_ANY",0xF);

/**
 * config_getValue() -- Return the value associated with a config token
 *
 * Any errors (including not set) return the string "default"
 */
function config_getValue($token=null,$source=CONFIG_SOURCE_ANY)
{
  /* Step 0: Check for null */
    /* CLEAN: Should throw an exception here... */
  if ($token == null) return "default";

  /* Step 1: User input */
  if (($source & CONFIG_SOURCE_URL) && (isset($_REQUEST[$token]))) {
    return $_REQUEST[$token];
  }

  /* Step 2: Cookies */
  if ($source & CONFIG_SOURCE_COOKIE)
  {
      /* Step 2a: with prefix */
    if (isset($_COOKIE[CONFIG_COOKIE_PREFIX.$token])) {
      return $_COOKIE[CONFIG_COOKIE_PREFIX.$token];
    }
      /* Step 2b: as-is */
    if (isset($_COOKIE[$token])) {
      return $_COOKIE[$token];
    }
  }

  /* Step 3: From config file */
    /* NOTYET */
  return "default";
}

/**
 * config_setParameter() -- Set a global system parameter
 */
function config_setParameter($param='option',$value='')
{
  $GLOBALS['config-parameters'][$param] = $value;
}

/**
 * config_getParameter() -- Retrieve a global system parameter
 */
function config_getParameter($param='option',$default=null)
{
  if (!isset($GLOBALS['config-parameters'][$param])) {
    return($default);
  }
  return($GLOBALS['config-parameters'][$param]);
}

/**
 * config_getBookDir() -- Get the asset path for a book
 */
function config_getBookDir($bookid=0)
{
  return "assets/books/".floor($bookid/100)."/".$bookid;
}

/**
 * config_setLastError() -- Store a string describing the last error
 */
function config_setLastError($err='Success')
{
  config_setParameter('errno',$err);
}

/**
 * config_getLastError() -- Retrieve a global system error string
 */
function config_getLastError()
{
  return config_getParameter('errno','Success');
}

?>
