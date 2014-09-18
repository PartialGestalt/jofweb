<div id="calendar-div">
<?php
require_once("library/core/class-date.php");
?>
<script type="text/javascript">
function monthNav(calId,date)
{
  svc_loadDiv('calendar-div','calendar','refDate='+date);
}
</script>
<?php
// Calendar goes here, possibly filtered.
$cal_filter=config_getvalue("calendar");

// Create base object
$cal = new akMonthCalendar(null,"block-calendar-table","block-calendar block-table");
// Set nav callback (function name with no params)
$cal->onNavClick = 'monthNav';
// Emit
$cal->emit();
?>
</div>
