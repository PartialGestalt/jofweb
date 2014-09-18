<?php
// PHP service script to generate in-place book editor
// NOTE: Document root is ../..

  // This is an HTML-formatted service
header('Content-type: text/html');
?>
<?php
// If the skin has pre-content setup, include it here.
require_once("library/core/util-skin.php");
require_once("library/core/util-db.php");
require_once("library/core/util-auth.php");
require_once("library/core/class-book.php");
require_once("library/core/util-config.php");
skin_include("svc-edit-book-pre.php");
?>

<?php
/* Step 1: Get current user info */
auth_validate();
$bookid=0+$_REQUEST['book'];

/* Step 2: Get book out of DB (if it's there) */
$booklist = db_booklist($auth_user->uid,PERM_EDIT,$bookid);
if (null != $booklist) {
  $book = &$booklist[$bookid];
  config_setParameter('edit-book-object',$book);
}

/* Step 3: Check for special book types */
if ($bookid < 1000)
{
  switch($bookid) { 
    case BOOK_ID_PROFILE:
      @include("svc-edit-book-profile.php");
      break;
    case BOOK_ID_ANNOUNCEMENTS:
      @include("svc-edit-book-announcement.php");
      break;
    case BOOK_ID_SERMONS:
      @include("svc-edit-book-sermon.php");
      break;
    case BOOK_ID_BULLETINS:
      @include("svc-edit-book-album.php");
      break;
    case BOOK_ID_CALENDAR:
      @include("svc-edit-book-calendar.php");
      break;
    case BOOK_ID_MUSIC:
      @include("svc-edit-book-generic.php");
      break;

    default:
      print 'Unknown "special" bookID="'.$bookid.'"';
  }
}
/* Step 3: Handle generic books by type */
else
{
   /* Step 2.1: Get (and set global ref to) book info */

   /* Step 2.2: Switch on type */
   switch ($book->type) {
     case BOOK_TYPE_ALBUM:
      @include("svc-edit-book-album.php");
      break;
     default: {
       print 'No editor configured for book type "'.$book->type.'", sorry about that.';
       break;
     }
   }
}
?>

<?php
// If the skin has post-content setup, include it here.
skin_include("svc-edit-book-post.php");
?>
