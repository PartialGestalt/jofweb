<div id="bookshelf-menu-div">
<?php
// 
//  Bookshelf menu block -- retrieve the current user's 
//        books and optionally start editing one.
// 
// If the skin has pre-content setup, include it here.
require_once("library/core/util-skin.php");
skin_include("block-bookshelf-menu-pre.php");
?>
<div id="bookshelf-menu-container">
<?php
// Includes 
require_once("library/core/util-db.php");

// Make main shelf container
$bookShelf = new akBookshelf("bookshelf","bookshelf-div");
$bookShelf->setTitle("Available Books");

// Retrieve list of books for current user
$booklist=db_booklist(); // NULL author means current
if (null != $booklist) {
  foreach($booklist as $book) $bookShelf->adopt($book);
}

// Add divider
$book = new akBook("Tools",BOOK_TYPE_DIVIDER);
$bookShelf->adopt($book);
// Add common elements (last)
  // Profile editor
$book = new akBook("Edit Profile",BOOK_TYPE_PROFILE);
$book->id = BOOK_ID_PROFILE;
$book->setHint("Edit your user profile and settings");
$bookShelf->adopt($book);
  // Add a new book
$book = new akBook("Create Book",BOOK_TYPE_BOOK);
$book->id = BOOK_ID_NEW;
$book->setHint("Add a new blog, calendar, or photo album");
$bookShelf->adopt($book);

// emit HTML
$bookShelf->emit();
?>
</div><!-- Close bookshelf-menu-container -->
<?php
// If the skin has post-content setup, include it here.
skin_include("block-bookshelf-menu-post.php");
?>
</div>
