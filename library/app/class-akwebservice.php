<?php
/****************************************************************************
 * class-akwebservice.php -- Define a class to describe a web service
 ***************************************************************************/
/*
 * akWebService -- Class object
 */
class akWebService
{
  /* Basic public members */
  public $token; // Nickname for the service
  public $label; // User-visible service name
  public $url;   // Base-level service access URL

  /* Constructor */
  function __construct($tk=null,$lb=null,$ur=null)
  {
    $this->token=$tk;
    $this->label=$lb;
    $this->url=$ur;
  }
}
