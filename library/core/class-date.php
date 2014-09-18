<?php
/****************************************************************************
 * util-date.php -- Block date/calendar support
 *
 * This requires the calendar extension to be installed on the PHP server.
 *
 ***************************************************************************/

/*
 * akDate -- Class object
 */

class akDate
{
  /* Basic public members */
  public $dowNum; // Day of the week (0-6)
  public $dowName; // Day of the week (Sun-Sat)
  public $dowFirst; // Day of the week (0-6) of the 1st of this month
  public $domNum; // Day of the month (1-31)
  public $monthNum; // Month of the year (1-12)
  public $monthName; // Month of the year (Jan-Dec)
  public $monthLen; // Length of the current month
  public $monthLenPrev; // Length of the previous month
  public $year; // Year number
  /* Basic private members */
  private $dateInfo; // All getdate() information

  /* Constructor */
  function __construct($when=null)
  {
    /* Step 1: Load basic date info array */
    if ($when == null) $this->dateInfo = getdate();
    else $this->dateInfo = getdate($when);

    /* Step 2: Init simple members */
    $this->dowNum = $this->dateInfo["wday"];
    $this->dowName = $this->dateInfo["weekday"];
    $this->domNum = $this->dateInfo["mday"];
    $this->dowFirst = ($this->dowNum + 7 - (($this->domNum % 7)-1))%7;
    $this->monthNum = $this->dateInfo["mon"];
    $this->monthName = $this->dateInfo["month"];
    $this->year = $this->dateInfo["year"];

    /* Step 3: Calendar extensions */
    $this->monthLen = cal_days_in_month(CAL_GREGORIAN,$this->monthNum,$this->dateInfo["year"]);
    if ($this->monthNum == 1) 
      $this->monthLenPrev = cal_days_in_month(CAL_GREGORIAN,12,$this->dateInfo["year"]-1);
    else
      $this->monthLenPrev = cal_days_in_month(CAL_GREGORIAN,$this->monthNum-1,$this->dateInfo["year"]);
  }
}

/*
 * akMonthCalendar -- Month-based calendar object
 *
 * A calendar object is a representation of one month.  It presents a grid
 * of days, each of which may have events or other data attached.  This object
 * forms the basis for a date picker, as well as a representation of a
 * monthly calendar view.
 *
 * Note that we always start the week on sunday.
 */
class akMonthCalendar
{
  /* Basic public members */
  public $id;       // ID for table element
  public $class;    // Class for table element
  public $refDate;  // Reference akDate for this calendar.
  /**
    Scriptlets -- each of these functions will receive two arguments:
    the calendar id
    a date string
   
    For days, the date string is the YYYY-MM-DD of the day; for nav,
    the date string will be YYYY-MM-01
   */
  public $onNavClick;
  public $onDayClick;
  public $onDayMouseover;
  public $onDayMouseout;
  /* Display options */
  public $showTitle; // Show title line (includes nav)
  public $showNav; // Show prev- or next-month navigation (only seen if showTitle is true)
  public $livePrevNext; // Make prev/next days active (false means just visible)
  /* Private status bits */
  private $daysPrev; // Days from previous month needed to fill out first week
  private $daysNext; // Days from next month needed to fill out last week

  /* Constructor */
  function __construct($when=null,$id=null,$class=null)
  {
    /* Create reference date */
    $this->refDate = new akDate($when);

    /* Set other parameters to be non-null */
    if (null != $id) $this->id = $id;
    else $this->id = 'calendar-'.mt_rand();
    if (null != $class) $this->class = $class;
    else $this->class = 'block-calendar';

    /* Init control variables */
    $this->showTitle = true;
    $this->showNav = true;
    $this->livePrevNext = false;
    $this->parameter = '';

    /* Init scriptlets */
    $this->onNavClick = null;
    $this->onDayClick = null;
    $this->onDayMouseover = null;
    $this->onDayMouseout = null;

    /* Calculate support bits */
      /* How many days from prev month required to fill out the first week? */
    $this->daysPrev = $this->refDate->dowFirst;
    if ($this->daysPrev == 0) $this->daysPrev = 7;
      /* How many days from next month required to fill out the last week? */
    $this->daysNext = 7 - ((($this->refDate->monthLen % 7) + $this->refDate->dowFirst) % 7);
    if ($this->daysNext == 0) $this->daysNext = 7;

  }

  /* Emitter */
  function emit()
  {
    /* Precalc some convenience bits */
    $daysPrev = $this->daysPrev;
    $daysCur = $this->refDate->monthLen;
    $daysNext = $this->daysNext;

    if ($this->refDate->monthNum == 1) {
      $prevYear = $this->refDate->year - 1;
      $nextYear = $this->refDate->year;
      $prevMonth = 12;
      $nextMonth = 2;
    } else if ($this->refDate->monthNum == 12) {
      $prevYear = $this->refDate->year;
      $nextYear = $this->refDate->year + 1;
      $prevMonth = 11;
      $nextMonth = 1;
    } else {
      $prevYear = $this->refDate->year;
      $nextYear = $this->refDate->year;
      $prevMonth = $this->refDate->monthNum - 1;
      $nextMonth = $this->refDate->monthNum + 1;
    }
    $prevString = sprintf("%04d-%02d-01",$prevYear,$prevMonth);
    $nextString = sprintf("%04d-%02d-01",$nextYear,$nextMonth);

    /* Start table */
    print '<table ';
    if (null != $this->id) {
      print 'id="'.$this->id.'" ';
    }
    if (null != $this->class) {
      print 'class="'.$this->class.'" ';
    }
    print '>';

    /* Emit table header as a row */
    if ($this->showTitle == true)
    {
      print '<tr class="block-calendar-title">';
      if ($this->showNav == true) {
        print '<td class="block-calendar-title">';
        print '<a title="Previous Month" ';
        if (null != $this->onNavClick) {
          print 'href="javascript:'.$this->onNavClick.'(\''.$this->id.'\',\''.$prevString.'\');"';
        }
        print '>';
        print '<span class="block-calendar-nav">';
        print '&lt;';
        print '</span>';
        print '</a>';
        print '</td>';
        print '<td class="block-calendar-title" colspan=5>';
        print $this->refDate->monthName.' '.$this->refDate->year;
        print '</td>';
        print '<td class="block-calendar-title">';
        print '<a title="Next Month" ';
        if (null != $this->onNavClick) {
          print 'href="javascript:'.$this->onNavClick.'(\''.$this->id.'\',\''.$nextString.'\');"';
        }
        print '>';
        print '<span class="block-calendar-nav">';
        print '&gt;';
        print '</span>';
        print '</a>';
        print '</td>';
      } else {
        print '<td class="block-calendar-title" colspan=7>';
        print $this->refDate->monthName.' '.$this->refDate->year;
        print '</td>';
      }
      print '</tr>';
    }
    /* Emit the dayname headers as a row */
    print '<tr class="block-calendar-title">';
    print '<td class="block-calendar-title">Su</td>';
    print '<td class="block-calendar-title">Mo</td>';
    print '<td class="block-calendar-title">Tu</td>';
    print '<td class="block-calendar-title">We</td>';
    print '<td class="block-calendar-title">Th</td>';
    print '<td class="block-calendar-title">Fr</td>';
    print '<td class="block-calendar-title">Sa</td>';
    print '</tr>';
    /* Emit table body */
    while ($daysNext > 0) {
      /* Emit a week at a time as a row */
        /* Start row */
      print '<tr class="block-calendar-row">';
        /* Loop over days in week */
      for ($td=0;$td<7;$td++)
      {
        $showFull = true; 
          /* For each day, calculate value,class, and refstring */
        if ($daysPrev > 0) {
            /* Label number */
          $dayNum = $this->refDate->monthLenPrev-$daysPrev+1;
            /* CSS class */
          $dayClass = 'block-calendar-previous';
            /* String tag */
          if ($this->refDate->monthNum == 1) {
            $dayString = sprintf("%4d-12-%02d",$this->refDate->year-1,$dayNum);
          } else {
            $dayString = sprintf("%4d-%02d-%02d",
                                 $this->refDate->year,
                                 $this->refDate->monthNum-1,
                                 $dayNum);
          }
          /* Mark for magic bits */
          if ($this->livePrevNext == false) $showFull = false;
          /* Decrement */
          $daysPrev--;
        } else if ($daysCur > 0) {
            /* Label number */
          $dayNum = $this->refDate->monthLen-$daysCur+1;
            /* CSS class */
          $dayClass = 'block-calendar-current';
            /* String tag */
          $dayString = sprintf("%4d-%02d-%02d",
                               $this->refDate->year,
                               $this->refDate->monthNum,
                               $dayNum);
          /* Decrement */
          $daysCur--;
        } else if ($daysNext > 0) {
          /* Label number */
          $dayNum = $this->daysNext-$daysNext+1;
          /* CSS Class */
          $dayClass = 'block-calendar-next';
          if ($this->refDate->monthNum == 12) {
            /* Next year... */
            $dayString = sprintf("%4d-01-%02d",
                                 $this->refDate->year+1,
                                 $dayNum);
          } else {
            $dayString = sprintf("%4d-%02d-%02d",
                                 $this->refDate->year,
                                 $this->refDate->monthNum+1, /* zero-based */
                                 $dayNum);
          }
          /* Mark for magic bits */
          if ($this->livePrevNext == false) $showFull = false;
          /* Decrement */
          $daysNext--;
        }
        /* Now, emit cell */
        print '<td ';
        if (null != $this->onDayClick)
        {
          print ' onClick="'.$this->onDayClick.'(\''.$this->id.'\',\''.$dayString.'\');" ';
        }
        print '  class="'.$dayClass.'" id="'.$dayString.'">';
          /* TODO: CLEAN: Add decoration, links, onClick,etc */
        print ''.$dayNum;
        print '</td>';

      }
        /* Finish row */
      print '</tr>';
    }

    /* Finish table */
    print '</table>';
  }
}

/**
 * Function to create a simple title for a month calendar
 */
function calendar_getTitle($when=null)
{
   $now = new akDate($when);

   return $now->monthName.' '.$now->year;
}

?>
