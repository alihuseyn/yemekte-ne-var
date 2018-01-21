<?php

namespace Helper;

/**
* -----------------------
* Validator class validate given items
* and throw error message if not match occured
* -----------------------
* @since 21.01.2018
* @version 1.0
*/
class Validator {

  /**
  * Validate date whether match with pattern or not
  *
  * @return bool true value if validate
  * @throws Exception if not matched given
  */
  public function date($date) 
  {
    $pattern = '/^([0-9]{4}-[0-9]{2}-[0-9]{2})|(today)$/';
    if (preg_match($pattern, $date)) {
      return true;
    }

    throw new Exception('The given date is not valid!');
  }

}
