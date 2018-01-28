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
  * @var array turkish charachters
  */
  private $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "İ", "Ğ", "Ü", "Ş", "Ö", "Ç", "+", "%", "#", "$", "&", "/", "=");

   /**
  * @var array english charachters
  */
  private $english = array("i", "g", "u", "s", "o", "c", "i", "g", "u", "s", "o", "c", "-", "-", "-", "-", "-", "-", "-");


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
  * Convert given text to meta tag
  * @param String text
  * @return meta tag for given item
  */
  public function meta($text)
  {
    $text = str_replace(' ', '_', strtolower($text));
    $meta = str_replace($this->turkish, $this->english, $text);

    setlocale(LC_ALL, 'en_US.UTF8');
    $meta = iconv('UTF-8', 'ASCII//TRANSLIT', $meta);
    $meta = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $meta);
    $meta = strtolower(trim($meta, '-'));
    $meta = preg_replace("/[\/_|+ -]+/", '_', $meta);

    return $meta;
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
  * Drop all quotes inside text
  * @param String $text
  * @return new text
  */
  public function dropQuotes($text)
  {
    return preg_replace('/\'/', '', $this->trim($text));
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
