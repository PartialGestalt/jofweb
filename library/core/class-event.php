<?php
/****************************************************************************
 * class-event.php -- Define object classes for events
 ***************************************************************************/

/*
 * CONSTANTS
 */

/*
 * akEvent -- object for an Event
 *
 * NOTE: This mostly just represents the DB object entry
 */
class akEvent
{
  /* Public members that reflect DB columns */
  public $id;   // Event id number from DB
  public $title; // Short headline/description
  public $book=0; // Book ID that owns this story
  public $story=0; // Story that describes this entry
  public $startdate='0000-00-00'; // 1st (or only) day of event
  public $starttime=0; // starting time of event
  public $enddate; // last day of event if multiday
  public $endtime; // ending time of event
  public $update; // timestamp of last update

  /* Private members */

  /* Constructor */
  function __construct($book=0)
  {
    /* Step 1: Load base info */
    $this->id = 0; // Zero means it isn't from DB 
    $this->book = $book;
    $this->story = 0;

    /* Step 2: Init any other required fields */
    $this->enddate=null; // Null means not multiday
    $this->endtime=null; // may be null
  }

  /* Method to emit the HTML for this event */
  function emit()
  {
    return;
  }
}
?>
