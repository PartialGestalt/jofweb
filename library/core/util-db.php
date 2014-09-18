<?php
/****************************************************************************
 * util-db.php -- Database support functions
 ***************************************************************************/
/* Includes */
require_once("library/core/util-config.php");
require_once("library/core/class-user.php");
require_once("library/core/class-book.php");
require_once("library/core/class-story.php");
require_once("library/core/class-event.php");

/* Configuration */
  /* Overall DB info */
define("DB_HOST","jofdb-main.jofumc.net");
define("DB_USER","jofadmin");
define("DB_PASS","Pel3map1");

  /* If we're on the beta site, use the beta db */
if ($_SERVER["SERVER_NAME"] == 'beta.jofumc.net') {
  define("DB_NAME","jofdb_beta");
} else {
  define("DB_NAME","jofdb");
}

  /* Individual tables */
define("TBL_USERS","people");
define("TBL_BOOKS","books");
define("TBL_STORIES","stories");
define("TBL_ACCESS","access");
define("TBL_SERIES","series");
define("TBL_EVENTS","events");
  /* Access permissions */
define("PERM_NONE",0);    // No access to book
define("PERM_READ",4);    // Can read stories in book
define("PERM_COMMENT",8); // Can comment on stories in book
define("PERM_EDIT",12);   // Can modify story text
define("PERM_WRITE",16);  // Can add, delete, edit, etc.
define("PERM_FULL",255);  // Can do _anything_

/* Global connection object */
$db_cxn=null;

/***********************************************
 * db_connect() -- Open a connection to the DB
 *
 * CLEAN: Try to open a persistent connection....
 */
function db_connect()
{
  global $db_cxn;

  /* Step 1: Check for previous connect */
  if ($db_cxn != NULL) return true;

  /* Step 2: Create base object/connect */
  $db_cxn = new MySQLi(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  /* Step 3: Check result */
  if ($db_cxn->connect_errno != 0) {
    $db_cxn = null;
    return false;
  }

  /* Step 4: it worked. */
  return true;
}


/***********************************************
 * db_userlist() -- Get a user or list of users 
 *
 * @param name If non-null, username to match
 * @param pass If non-null, password to match
 *             (only checked if $name is non-null)
 *
 * CLEAN: Scrub input!
 */
function db_userlist($name=null,$pass=null)
{
  /* Step 0: Connect to db if not connected */
  global $db_cxn;
  if (!db_connect()) return null;

  /* Step 1: Build match string from args */
  if ($name)
  {
    $where=" WHERE username='".$name."'";

    if ($pass)
    {
        // Username and password specified...
        $where=$where." AND password='".$pass."'";
    }
  }
  /* Step 2: build and issue the query */
  $query="SELECT * FROM ".TBL_USERS.$where;
  $result=@$db_cxn->query($query);

  /* Step 3: pull results into user objects */
  if ($result->num_rows == 0) {
    $userlist = null;
  } else {
    $userlist = array();
    while ($row=$result->fetch_assoc())
    {
      /* Step 3.1: Create the new reference */
      $userlist[$row['username']]=new akUser($row['username']);
      /* Step 3.2: Fill in the remaining fields */
      $userlist[$row['username']]->email = $row['email'];
      $userlist[$row['username']]->displayname = $row['display'];
      $userlist[$row['username']]->fullname = $row['fullname'];
      $userlist[$row['username']]->uid = $row['id'];
      $userlist[$row['username']]->skin = $row['skin'];
      $userlist[$row['username']]->editor = $row['editor'];
      $userlist[$row['username']]->session = $row['session'];
        /* NOTE: Leave password field null */
    }
  }

  /* Step 4: Free resources */
  $result->free_result();
    
  return $userlist;
}

/***********************************************
 * db_booklist() -- Get a list of books available to a given uid
 *
 * @param author ID (from people table) of author
 * @param book   ID (from book table) of target book
 * @param permission Minimum permission required
 * 
 * NOTE: If $author is null, use currently-authenticated
 */
function db_booklist($author=null,$permission=PERM_EDIT,$bookid=null)
{
  global $auth_user;
  global $db_cxn;

  /* Step 0: Connect to db if not connected */
  if (!db_connect()) return null;

  /* Step 1: Resolve author */
  if (null == $author) $author = $auth_user->uid;

  /* Step 2: build and issue the query */
    /* Step 2.1: basic joined query start */
  $query = "SELECT ".TBL_BOOKS.".* FROM ".TBL_BOOKS." LEFT JOIN ".TBL_ACCESS." ON ".TBL_BOOKS.".id=".TBL_ACCESS.".book ";
    /* Step 2.2: Author and permissions select */
  $query .= "WHERE (".TBL_BOOKS.".author=".$author." OR ( ".TBL_ACCESS.".uid=".$author." AND ".TBL_ACCESS.".level >= ".$permission.")) ";
    /* Step 2.3: If we're getting a particular one, add it here */
  if (null != $bookid) {
    $query .= " AND ".TBL_BOOKS.".id = ".$bookid." ";
  }
    /* Step 2.4: Order newest books first */
  $query .= 'ORDER BY '.TBL_BOOKS.'.id DESC';

  $result=@$db_cxn->query($query);

  /* Step 3: pull results into user objects */
  if ($result->num_rows == 0) {
    $booklist = null;
  } else {
    $booklist = array();
    while ($row=$result->fetch_assoc())
    {
      /* Step 3.1: Create the new reference */
      $booklist[$row['id']]=new akBook($row['title']);
      /* Step 3.2: Fill in the remaining fields */
      $booklist[$row['id']]->id = $row['id'];
      $booklist[$row['id']]->type = $row['type'];
      $booklist[$row['id']]->author = $row['author'];
      $booklist[$row['id']]->title = $row['title'];
    }
  }

  /* Step 4: clean up */
  $result->free_result();
    
  return $booklist;
}

/***********************************************
 * db_sermonsByDateRange() -- Get a list of sermons that meet criteria
 *
 * @param book   ID (from book table) of target sermon book
 * 
 */
function db_sermonsByDateRange($bookid=null,$start_date='1000-01-01',$end_date='2500-12-31')
{
  global $db_cxn;

  /* Step 1: Connect to db if not connected */
  if ((null == $bookid) || !db_connect()) return null;

  /* Step 2: build and issue the query */
  $query = "SELECT * FROM ".TBL_STORIES." WHERE book=".$bookid." AND deliver_date <= '".$end_date."' AND deliver_date >='".$start_date."'";

    /* Step 2.4: Order newest books first */
  $query .= ' ORDER BY deliver_date,edit_time ASC';

  $result=@$db_cxn->query($query);

  /* Step 3: pull results into user objects */
  if ($result->num_rows == 0) {
    $sermonlist = null;
  } else {
    $sermonlist = array();
    while ($row=$result->fetch_assoc())
    {
      /* Step 3.1: Create the new reference */
      $sermonlist[$row['deliver_date']]=new akStoryEntry($row['deliver_date']);
      /* Step 3.2: Fill in the remaining fields */
      $sermonlist[$row['deliver_date']]->id = $row['id'];
      $sermonlist[$row['deliver_date']]->book = $row['book'];
      $sermonlist[$row['deliver_date']]->author = $row['author'];
      $sermonlist[$row['deliver_date']]->deliver_date = $row['deliver_date'];
      $sermonlist[$row['deliver_date']]->series = $row['series'];
      $sermonlist[$row['deliver_date']]->title = $row['title'];
      $sermonlist[$row['deliver_date']]->section_title = $row['section_title'];
      $sermonlist[$row['deliver_date']]->text = $row['text'];
    }
  }

  /* Step 4: clean up */
  $result->free_result();
    
  return $sermonlist;
}

/***********************************************
 * db_getStoryById() -- Get a story with a given ID
 *
 * @param storyId DB ID of story to retrieve
 *
 * @returns akStory object built from DB entries.
 * 
 */
function db_getStoryById($storyId=0)
{
  global $db_cxn;

  /* Step 1: Connect to db if not connected */
  if ((0 == $storyId) || !db_connect()) return null;

  /* Step 2: build and issue the primary query */
  $query = "SELECT * FROM ".TBL_STORIES." WHERE id=".$storyId.";";

  $result=@$db_cxn->query($query);

  /* Step 3: pull result into user object */
  if ($result->num_rows == 0) {
    $story = null;
  } else {
    $row = $result->fetch_assoc();
    $story = new akStoryEntry($storyId);

    $story->id = $row['id'];
    $story->book = $row['book'];
    $story->author = $row['author'];
    $story->create_time = $row['create_time'];
    $story->edit_time = $row['edit_time'];
    $story->deliver_date = $row['deliver_date'];
    $story->is_frozen = $row['is_frozen'];
    $story->keywords = $row['keywords'];
    $story->series = $row['series'];
    $story->title = $row['title'];
    $story->section_title = $row['section_title'];
    $story->lede = $row['lede'];
    $story->banner = $row['banner'];
    $story->text = $row['text'];
  }

  /* Step 4: Gather any secondary chunks */
    /* CLEAN: TODO: Implement me */

  /* Step 5: clean up */
  $result->free_result();
    
  /* Step 6: Return the cooked story object */
  return $story->getCooked();
}

/***********************************************
 * db_getEventById() -- Get an event with a given ID
 *
 * @param eventId DB ID of event to retrieve
 *
 * @returns akEvent object built from DB entries.
 * 
 */
function db_getEventById($eventId=0)
{
  global $db_cxn;

  /* Step 1: Connect to db if not connected */
  if ((0 == $eventId) || !db_connect()) return null;

  /* Step 2: build and issue the primary query */
  $query = "SELECT * FROM ".TBL_EVENTS." WHERE id=".$eventId." LIMIT 1;";

  $result=@$db_cxn->query($query);

  /* Step 3: pull result into user object */
  if ($result->num_rows == 0) {
    $event = null;
  } else {
    $row = $result->fetch_assoc();
    $event = new akEvent($row['book']);

    $event->id = $row['id'];
    $event->story = $row['story'];
    $event->startdate = $row['startdate'];
    $event->starttime = $row['starttime'];
    $event->enddate = $row['enddate'];
    $event->endtime = $row['endtime'];
    $event->title = $row['title'];
    $event->update = $row['update'];
  }

  /* Step 4: clean up */
  $result->free_result();
    
  /* Step 5: Return the cooked event object */
  return $event;
}

/***********************************************
 * db_announcementList() -- Get recent announcements
 *
 * @param maxcount Most announcements to retrieve
 * 
 */
function db_announcementList($maxcount=10)
{
  global $db_cxn;

  /* Step 1: Connect to db if not connected */
  if (!db_connect()) return null;

  /* Step 2: build and issue the query */
  $query = "SELECT * FROM ".TBL_STORIES." WHERE book=".BOOK_ID_ANNOUNCEMENTS;

    /* Step 2.4: Order newest books first */
  $query .= ' ORDER BY edit_time DESC LIMIT '.$maxcount;

  $result=@$db_cxn->query($query);

  /* Step 3: pull results into user objects */
  if ($result->num_rows == 0) {
    $list = null;
  } else {
    $list = array();
    $slot = 0;
    while ($row=$result->fetch_assoc())
    {
      /* Step 3.1: Create the new reference */
      $list[$slot]=new akStoryEntry($slot);
      /* Step 3.2: Fill in the remaining fields */
      $list[$slot]->id = $row['id'];
      $list[$slot]->book = $row['book'];
      $list[$slot]->author = $row['author'];
      $list[$slot]->title = $row['title'];
      $list[$slot]->edit_time = $row['edit_time'];
      $list[$slot]->text = $row['text'];
      $slot++;
    }
  }

  /* Step 4: clean up */
  $result->free_result();
    
  return $list;
}

/***********************************************
 * db_seriesList() -- Get a list of topic/series for a given book
 *
 * @param book   ID (from book table) of target book
 * 
 */
function db_seriesList($bookid=null)
{
  global $db_cxn;

  /* Step 1: Connect to db if not connected */
  if ((null == $bookid) || !db_connect()) return null;

  /* Step 2: build and issue the query */
  $query = "SELECT * FROM ".TBL_SERIES." WHERE book=".$bookid;

    /* Step 2.4: Order newest series first */
  $query .= ' ORDER BY id DESC';

  $result=@$db_cxn->query($query);

  /* Step 3: pull results into user objects */
  if ($result->num_rows == 0) {
    $serieslist = null;
  } else {
    $serieslist = array();
    $slot=0;
    while ($row=$result->fetch_assoc())
    {
      /* Step 3.1: Store into array */
      $serieslist[] = new akSeries($row['id'],$row['title'],$row['subtitle'],$row['description'],$row['book']);
    }
  }

  /* Step 4: clean up */
  $result->free_result();
    
  return $serieslist;
}

/***********************************************
 * db_eventsUpcoming() -- Get a list of upcoming events
 *
 * @param book   ID (from book table) of target book
 * @param count  Max # of events to return
 * 
 */
function db_eventsUpcoming($bookid=null,$count=5)
{
  global $db_cxn;

  /* Step 1: Connect to db if not connected */
  if ((null == $bookid) || !db_connect()) return null;

  /* Step 2: build and issue the query */
    /* Step 2.1: What time is it? */
  $now = new DateTime();
    /* Step 2.2: Select later day than today or later time today than now */
  $query = "SELECT * FROM ".TBL_EVENTS." WHERE `book`=".$bookid;
  $query .= ' AND (';
  $query .= ' `startdate` > \''.$now->format("Y-m-d").'\'';
  $query .= ' OR (';
  $query .= ' `startdate` = \''.$now->format("Y-m-d").'\'';
  $query .= ' AND ';
  $query .= ' `starttime` >= \''.$now->format("His").'\'';
  $query .= ' ) ';
  $query .= ' ) ';
    /* Step 2.3: order by upcoming start date/time */
  $query .= ' ORDER BY `startdate`,`starttime` ';
    /* Step 2.4: Set limit by our parameter */
  $query .= ' LIMIT 0,'.$count.';';

  $result=@$db_cxn->query($query);

  /* Step 3: pull results into user objects */
  if ($result->num_rows == 0) {
    $eventlist = null;
  } else {
    $eventlist = array();
    while ($row=$result->fetch_assoc())
    {
      /* Step 3.1: Create a new object */
      $ev = new akEvent($row['book']);
      $ev->id = $row['id'];
      $ev->title = $row['title'];
      $ev->story = $row['story'];
      $ev->startdate = $row['startdate'];
      $ev->starttime = $row['starttime'];
      $ev->enddate = $row['enddate'];
      $ev->endtime = $row['endtime'];
      /* Step 3.1: Store into array */
      $eventlist[] = $ev;
    }
  }

  /* Step 4: clean up */
  $result->free_result();
    
  return $eventlist;
}

/***********************************************
 * db_updateUser() -- Change a user's info in DB
 *
 * @param nu New (updated) user object
 */
function db_updateUser($nu=null,$ou=null)
{
  global $db_cxn;

  $changed=false;

  /* Step 0: Early validation */
  if (($nu == null) || ($ou == null) || ($nu->uid != $ou->uid)) {
    return false;
  }

  /* Step 1: Connect to db if not connected */
  if (!db_connect()) return false;

  /* Step 2: start query */
  $query = "UPDATE `".TBL_USERS."` SET ";

  /* Step 3: Add a bit for each updated field */
    /* NOTE: Not all fields can be updated! */
    /* Step 3.1: Email address */
  if ((null != $nu->email) && ($nu->email != $ou->email)) {
    $changed=true;
    $query .= "`email` = '".$nu->email."', ";
  }
    /* Step 3.2: Display name */
  if ((null != $nu->displayname) && ($nu->displayname != $ou->displayname)) {
    $changed=true;
    $query .= "`display` = '".$nu->displayname."', ";
  }
    /* Step 3.3: Full name */
  if ((null != $nu->fullname) && ($nu->fullname != $ou->fullname)) {
    $changed=true;
    $query .= "`fullname` = '".$nu->fullname."', ";
  }
    /* Step 3.4: Password */
  if ($nu->password != null) {
    $changed=true;
    $query .= "`password` = '".$nu->password."', ";
  }
    /* Step 3.5: Skin */
  if ((null != $nu->skin) && ($nu->skin != $ou->skin)) {
    $changed=true;
    $query .= "`skin` = '".$nu->skin."', ";
  }
    /* Step 3.6: Session */
  if ((null != $nu->session) && ($nu->session != $ou->session)) {
    $changed=true;
    $query .= "`session` = '".$nu->session."', ";
  }

  /* Step 4: Finish query */
  $query .= "`ctime` = NOW() WHERE `id`=".$ou->uid;

  /* Step 5: Do it? */
  if ($changed) {
    return $db_cxn->query($query);
  }

  return true;
}

/***********************************************
 * db_updateUserSession() -- Change a user's session key in DB
 *
 * @param nu New (updated) user object
 */
function db_updateUserSession($uid = null, $key = null)
{
  global $db_cxn;

  /* Step 0: Early validation */
  if ($uid == null) {
    return false;
  }

  if ($key == null) $session_key = "default";
  else $session_key = $key;

  /* Step 1: Connect to db if not connected */
  if (!db_connect()) return false;

  /* Step 2: start query */
  $query = "UPDATE `".TBL_USERS."` SET ";

  /* Step 3: Add a bit for session field */
  $query .= "`session` = '".$session_key."', ";

  /* Step 4: Finish query */
  $query .= "`ctime` = NOW() WHERE `id`=".$uid;

  /* Step 5: Do it */
  return $db_cxn->query($query);

  return true;
}

/***********************************************
 * db_saveStory() -- Change or add a story to the DB
 *
 * @param st New (created or updated) akStoryEntry object
 *
 * @returns ID of inserted or updated story
 *
 * The DB action that takes place is determined by
 * the 'id' field of the story object.  If it is zero,
 * we treat this as a new entry and INSERT it.  If the
 * id is nonzero, we treat it as an UPDATE.
 */
function db_saveStory($st=null)
{
  global $db_cxn;

  /* Step 0: Early validation */
  if (($st == null) ||
      ($st->book == 0) ||
      ($st->author == 0)){
    return 0;
  }

  /* Step 1: Connect to db if not connected */
  if (!db_connect()) return 0;

  /* Step 2: Build SQL based on action */
    /* Step 2.1: Build a col/val array pair for any non-null fields */
  $c = array(); $v = array();
  if ($st->id != 0) {$c[] = 'id';$v[]=$st->id;}
  if ($st->book != 0) {$c[] = 'book';$v[]=$st->book;}
  if ($st->author != 0) {$c[] = 'author';$v[]=$st->author;}
  if ($st->deliver_date != '') {$c[] = 'deliver_date';$v[]="'".$st->deliver_date."'";}
  $c[] = 'is_frozen'; $v[]=$st->is_frozen;
  if ($st->keywords != '') {$c[]='keywords';$v[]="'".$st->keywords."'";}
  if ($st->series) {$c[]='series';$v[]="'".$st->series."'";}
  if ($st->title) {$c[]='title';$v[]="'".$st->title."'";}
  if ($st->section_title) {$c[]='section_title';$v[]="'".$st->section_title."'";}
  if ($st->lede) {$c[]='lede';$v[]="'".$st->lede."'";}
  if ($st->banner) {$c[]='banner';$v[]="'".$st->banner."'";}
  if ($st->text) {$c[]='text';$v[]="'".$st->text."'";}
  $c[] = 'edit_time'; $v[] = 'NOW()';

    /* Step 2.2: INSERT new entry */
  if ($st->id == 0) {
    /* Step 2.2.1: Startup */
    $query = "INSERT INTO ".TBL_STORIES." (";
    $comma = '';
    /* Step 2.2.2: Column names */
    foreach ($c as $column) {
      $query .= $comma.$column;
      $comma=',';
    }
    $query .= ")";
    /* Step 2.2.3: Values */
    $comma = '';
    $query .= " VALUES (";
    foreach ($v as $value) {
      $query .= $comma.$value;
      $comma=',';
    }
    $query .= ")";
  } else {
    /* Step 2.3: UPDATE existing with non-null fields */
  }

  /* Step 3: Do the DB action, returning the new or modified story ID */
  if(@$db_cxn->query($query)) {
    return $db_cxn->insert_id;
  } else {
    return 0;
  }
}

/***********************************************
 * db_saveSeries() -- Change or add a series to the DB
 *
 * @param id ID of series (zero for new, nonzero if updating)
 * @param book Book that owns the given series
 * @param title Title of series
 * @param subtitle Subtitle for series
 * @param desc Description of series
 *
 */
function db_saveSeries($id=0,$book=0,$title=null,$subtitle=null,$desc=null)
{
  global $db_cxn;

  /* Step 0: Early validation */
  if (($title == null) ||
      ($book == 0)){
    return 0;
  }

  /* Step 1: Connect to db if not connected */
  if (!db_connect()) return 0;

  /* Step 2: Build SQL based on action */
    /* Step 2.1: Build a col/val array pair for any non-null fields */
  $c = array(); $v = array();
  if ($id != 0) {$c[] = 'id';$v[]=$id;}
  if ($title) {$c[]='title';$v[]="'".$title."'";}
  if ($subtitle) {$c[]='subtitle';$v[]="'".$subtitle."'";}
  if ($desc) {$c[]='description';$v[]="'".$desc."'";}
  if ($book != 0) {$c[] = 'book';$v[]=$book;}

    /* Step 2.2: INSERT new entry */
  if ($id == 0) {
    /* Step 2.2.1: Startup */
    $query = "INSERT INTO ".TBL_SERIES." (";
    $comma = '';
    /* Step 2.2.2: Column names */
    foreach ($c as $column) {
      $query .= $comma.$column;
      $comma=',';
    }
    $query .= ")";
    /* Step 2.2.3: Values */
    $comma = '';
    $query .= " VALUES (";
    foreach ($v as $value) {
      $query .= $comma.$value;
      $comma=',';
    }
    $query .= ")";
  } else {
    /* Step 2.3: UPDATE existing with non-null fields */
  }

  /* Step 3: Do the DB action, returning the new or modified story ID */
  if(@$db_cxn->query($query)) {
    return $db_cxn->insert_id;
  } else {
    return 0;
  }
}

/***********************************************
 * db_saveEvent() -- Change or add an event to the DB
 *
 * @param id ID of event (zero for new, nonzero if updating)
 *
 */
function db_saveEvent($ev=null)
{
  global $db_cxn;

  /* Step 0: Early validation */
  if (($ev == null) ||
      ($ev->story == 0)){ // Must have description in a story 
    return 0;
  }

  /* Step 1: Connect to db if not connected */
  if (!db_connect()) return 0;

  /* Step 2: Build SQL based on action */
    /* Step 2.1: Build a col/val array pair for any non-null fields */
  $c = array(); $v = array();
  if ($ev->id != 0) {$c[] = 'id';$v[]=$ev->id;}
  if ($ev->book != 0) {$c[] = 'book';$v[]=$ev->book;}
  if ($ev->story != 0) {$c[] = 'story';$v[]=$ev->story;}
  if ($ev->title) {$c[]='title';$v[]="'".$ev->title."'";}
  if ($ev->startdate) {$c[]='startdate';$v[]="'".$ev->startdate."'";}
  if ($ev->starttime) {$c[]='starttime';$v[]="'".$ev->starttime."'";}
  if ($ev->enddate) {$c[]='enddate';$v[]="'".$ev->enddate."'";}
  if ($ev->endtime) {$c[]='endtime';$v[]="'".$ev->endtime."'";}

    /* Step 2.2: INSERT new entry */
  if ($ev->id == 0) {
    /* Step 2.2.1: Startup */
    $query = "INSERT INTO ".TBL_EVENTS." (";
    $comma = '';
    /* Step 2.2.2: Column names */
    foreach ($c as $column) {
      $query .= $comma.$column;
      $comma=',';
    }
    $query .= ")";
    /* Step 2.2.3: Values */
    $comma = '';
    $query .= " VALUES (";
    foreach ($v as $value) {
      $query .= $comma.$value;
      $comma=',';
    }
    $query .= ")";
  } else {
    /* Step 2.3: UPDATE existing with non-null fields */
  }

  /* Step 3: Do the DB action, returning the new or modified story ID */
  if(@$db_cxn->query($query)) {
    return $db_cxn->insert_id;
  } else {
    return 0;
  }
}
?>
