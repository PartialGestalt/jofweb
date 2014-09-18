<?php
/****************************************************************************
 * util-auth.php -- Generic authentication handling
 *
 ***************************************************************************/
require_once("library/core/util-config.php");
require_once("library/core/util-db.php");
require_once("library/core/class-user.php");

/* Global authenticated user info */
   /* NOTE: We set this here to guarantee that it is set
    *       for other script activity. 
    */
$auth_user = new akUser('default');
$auth_prev = null;

/**
 * rand_string() -- Generate a string of random length
 *
 * We'll generate a string of letters/numbers, with letters
 * weighted 4 to 1.
 */
function rand_string($length=5)
{
  /* Step 1: First char is always a letter */
  $s = chr(mt_rand(65,90));
  for ($i=1;$i<$length;$i++) {
    if (mt_rand(0,4) == 0) {
      /* Number */
      $s .= chr(mt_rand(48,57));
    } else {
      /* Letter (all uppercase) */
      $s .= chr(mt_rand(65,90));
    }
  }
  return $s;
}

/**
 * auth_validate() -- Validate an existing user/session
 *
 * NOTE: This must be called before anything is
 *       emitted.
 */
function auth_validate()
{
  global $auth_user;
  $user=config_getValue("user");
  $sess=config_getValue("session-id");

  /* Step 1: Degenerate case, don't bother */
  if ($user=="default") return false;

  /* Step 2: Had something, validate it */
    /* Step 2.1: Make sure the user exists at all */
  $valid=db_userlist($user); 
  if ((null == $valid) || (!array_key_exists($user,$valid)))
  {
    /* Invalid user; leave auth info as default and return 
     * failure.
     */
    return false; 
  }
    /* Step 2.2: Check session id */
  if ($valid[$user]->session == $sess) {
    /* Session valid */
    $auth_user = $valid[$user];
    return true;
  }

  /* Step 3: Session failure */
    /* Step 3.1: Clear cookies */
  setcookie("jof-session-id","default",time()+300000);
  $_COOKIE["jof-session-id"] = "default";
  setcookie("jof-user","default",time()+300000);
  $_COOKIE["jof-user"] = "default";

    /* Step 3.2: Redirect to session failure page */
  core_redirect("/session_expired.php");

  return false;
}

/**
 * auth_login() -- Generate a session for a user
 *
 * NOTE: This must be called before anything is
 *       emitted.
 */
function auth_login()
{
  global $auth_user;
  /* Step 1: Get info from URL/form/cookies */
  $user=config_getValue("username");
  $pass=config_getValue("password");

  /* Step 2: Validate against the DB */
  $valid=db_userlist($user,$pass);
  if ((null == $valid) || (!array_key_exists($user,$valid)))
  {
    /* FAILED; return without updating */
    return false;
  }

  /* Step 3: Validated.  Save user info */
  $auth_user = $valid[$user];

  /* Step 4: Generate/save new session key */
  $auth_user->session = rand_string(20);
  db_updateUserSession($auth_user->uid,$auth_user->session);

  /* Step 5: Set cookies for subsequent requests */
  setcookie("jof-session-id",$auth_user->session,time()+300000);
  $_COOKIE["jof-session-id"] = $auth_user->session;
  setcookie("jof-user",$auth_user->name,time()+300000);
  $_COOKIE["jof-user"] = $auth_user->name;

  return true;
}

/**
 * auth_logout() -- Do any logout processing
 *
 * NOTE: This must be called before anything is
 *       emitted.
 */
function auth_logout()
{
  global $auth_user;
  /* Step 1: Destroy session in DB */
  $user=config_getValue("user");
  if ((null!=$user) && ("default" != $user))
  {
    $valid=db_userlist($user); 
    if ((null != $valid) || (array_key_exists($user,$valid))) {
      db_updateUserSession($valid[$user]->uid,null);
    }
  }

  /* Step 2: Clear any cookies */
  setcookie("jof-user","default",time()-3600);
  $_COOKIE["jof-user"] = "default";
  setcookie("jof-session-id","",time()-3600);
  $_COOKIE["jof-session-id"] = "";

  /* Step 3: Wipe the auth user */
  if ($auth_user->name != 'default')
  {
    $auth_user = new akUser('default');
  }

  return true;
}

/**
 * is_authenticated() -- Has the user been authenticated?
 *
 */
function is_authenticated()
{
  global $auth_user;
  if (isset($auth_user) && ($auth_user->name != 'default')) {
    return true;
  }
  return false;
}

/**
 * is_editor() -- Does the user have any editing rights?
 *
 */
function is_editor()
{
  global $auth_user;
  if (isset($auth_user) && ($auth_user->editor != 0)) {
    return true;
  }
  return false;
}
?>
