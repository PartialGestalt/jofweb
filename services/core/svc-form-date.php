<?php
// PHP service script to generate form item date picker

  // This is an HTML-formatted service
header('Content-type: text/html');
?>
<?php
// Includes
require_once("library/core/util-config.php");
require_once("library/core/util-auth.php");
require_once("library/core/util-skin.php");
require_once("library/core/class-date.php");
?>

<?php
/* Step 1: Get current user/target info */
auth_validate();
$refDate=$_REQUEST['refDate'];
$targetDiv=$_REQUEST['targetDiv'];
?>

<?php
// Pull in any skin-based frame decoration
skin_include("form-popup-pre.php");
?>
<?php
/* Step 2: Create base calendar object */
$cal = new akMonthCalendar(strtotime($refDate),$targetDiv, "block-calendar block-table");

/* Step 3: Set any whatnot */
$cal->onNavClick = 'akForm_datepickerNav';
$cal->onDayClick = 'akForm_datepickerDay';

/* Step 4: Emit it */
$cal->emit();
?>

<?php
// Pull in any skin-based frame decoration
skin_include("form-popup-post.php");
?>
