<?php

namespace Helper;

/**
* ----------------------------------
* Formatter class helps to change given
* items to required format easily
* ----------------------------------
* @version 1.0
* @since 21.01.2018
*/
class Formatter {

  /**
  * Change Format of date from dd.mm.yyyy to yyyy-mm-dd
  * If the format not match return itself
  * @param $date String date which will be changed
  * @return new formatted date
  */
  public function date($date) 
  {
    $date = explode('.', $this->trim($date));
    if (count($date) >= 3 ) {
      $date = "{$date[2]}-{$date[1]}-{$date[0]}";
    } else {
      $date = implode('', $date); // not match implode return
    }

    return $date;
  }

  /**
  * Convert first charachter of text to uppercase
  * @param String $text
  * @return new first uppercased item
  */
  public function firstUpper($text) 
  {
    return ucfirst($this->trim($text));
  }

  /**
  * Trim text
  * @param String $text
  * @return new trimmed text
  */
  public function trim($text) 
  {
    return trim($text);
  }

  /**
  * Convert all charachter of text to uppercase
  * @param String $text
  * @return new all uppercased item
  */
  public function allUpper($text) 
  {
    return strtoupper($this->trim($text));
  } 
}
