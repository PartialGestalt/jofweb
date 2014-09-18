<?php
/****************************************************************************
 * class-user.php -- User class definitions
 *
 ***************************************************************************/

/*
 * akUser -- User object
 */
class akUser
{
  /* Private members */
    /* NOTE: These aren't really private, but are separated
     *       here so I'll remember that they can't be updated
     *       from the UI.
     */
  public $uid;  // ID from people database
  public $name; // Username

  /* Basic public members */
  public $email; // Primary email contact
  public $displayname; // Name to display
  public $fullname; // Full user name
  public $skin; // User's preferred skin (name)

  /* Other members */
  public $password; // User's password in db
  public $editor;   // Is user an editor of anything?
  public $session;  // User's active session ID

  /* Misc. small options */
  public $dateformat;  // How to format date strings?
  public $timezone;   // Where are you?

  /* Constructor */
  function __construct($name=null,$displayname=null,$fullname=null)
  {
    /* Step 1: Pull from args */
    $this->name = $name;
    $this->displayname = $displayname;
    $this->fullname = $fullname;

    /* Step 2: initialize everything else */
    $this->password = null;
    $this->email = null;
    $this->skin = 'default';
    $this->uid = 0;
    $this->editor = 0;
    $this->session = null;
    $this->dateformat = 'F jS \a\t g:ia';
    $this->tz = 'America/Chicago';
  }

  /* User-specific method for date generation */
  function format_date($dateStr=null) {
    $fdt = new DateTime($dateStr,timezone_open($this->tz));
    return $fdt->format($this->dateformat);
  }
}
?>
