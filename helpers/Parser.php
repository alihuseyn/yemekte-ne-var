<?php

namespace Helper;

/**
* ----------------------------------------
* Parser Class Parse Data From xls for given
* source file. After Parse Return data array
* as required format for further operations
* -----------------------------------------
* @since 21.01.2018
* @version 1.0
* 
*/
class Parser {

  /**
  * @var String source file
  */
  private $source;

  /**
  * @var Formatter formatter
  */
  private $formatter;

  /**
  * Constructor
  */
  public function __construct()
  {
    $this->formatter = new Formatter();
  }

  /**
  * Set source file path
  * @param $source String source file
  */
  public function setSourceFile($source)
  {
    $this->source = $source;
  }

  /**
  * Parse content of xls file and return data array
  * @return array parsed and arranged data content
  */
  public function apply()
  {
    // data content array
    $data = array();
    // Pattern applied to detect correct row
    $pattern = '/([0-9]{2}\.[0-9]{2}\.[0-9]{4})\s+(.+)/';
    $reader = new \SpreadsheetReader($this->source);
    // Response array
    foreach ($reader as $row) {
      if ($this->validateRow($row) && preg_match($pattern, $row[0], $matches)) {
        array_push($data, [
          'date'    => $this->formatter->date($matches[1]),
          'weekday' => $this->formatter->firstUpper($matches[2]),
          'meals'   => [
            $this->formatter->trim($row[1]),
            $this->formatter->trim($row[2]),
            $this->formatter->trim($row[3]),
            $this->formatter->trim($row[4])
          ]
        ]);
      } 
    }

    return $data;
  }

  /**
  * Return Whether row is full and required or empty
  * @param array $row 
  * @return bool true/false
  */
  private function validateRow($row)
  {
    return is_array($row) && count($row) >= 5 && !empty($row[0]);
  }
}
