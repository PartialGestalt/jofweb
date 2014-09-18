<?php
// PHP service script to generate in-place calendar

  // This is an HTML-formatted service
header('Content-type: text/html');
?>
<?php
// Includes
require_once("library/core/util-config.php");
require_once("library/core/util-auth.php");
require_once("library/core/class-date.php");
?>

<script type="text/javascript">
function monthNav(calId,date)
{
  svc_loadDiv('calendar-div','calendar','refDate='+date);
}
</script>
<?php
/* Step 1: Get current user/target info */
auth_validate();
$refDate=$_REQUEST['refDate'];

/* Step 2: Create base calendar object */
$cal = new akMonthCalendar(strtotime($refDate),"block-calendar-table", "block-calendar block-table");

/* Step 3: Set any whatnot */
$cal->onNavClick = 'monthNav';

/* Step 4: Emit it */
$cal->emit();
?>
