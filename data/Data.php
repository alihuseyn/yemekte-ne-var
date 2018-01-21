<?php

namespace DataLayer;

use Helper\Formatter;
use Helper\Download;
use Helper\Parser;
use Helper\Validator;
use Helper\Redis;

/**
* -----------------------
* Data Class controls all mechanism
* of data layer. Read and write to cache (Redis => Key:Pair DB)
* applied inside of this class.
* -----------------------
* @since 21.01.2018
* @version 1.0
*/
class Data {

  /**
  * @var Formatter $formatter
  */
  private $formatter;

  /**
  * @var Validator $validator
  */
  private $validator;

  /**
  * @var Download $download
  */
  private $download;

  /**
  * @var Parser $parser
  */
  private $parser;

  /**
  * @var Redis $redis
  */
  private $redis;

  /**
  * Constructor
  * Initialize and set required objects
  */
  public function __construct()
  {
    $this->formatter = new Formatter();
    $this->validator = new Validator();
    $this->download = new Download();
    $this->parser = new Parser();
    $this->parser->setSourceFile($this->download->getFileDestination());
    $this->redis = new Redis();
  }

  /**
  * Return specific data according to given date
  * @param String $date
  * @return array
  */
  public function get ($date)
  {
    // Apply Formatter
    $date = $this->formatter->trim($date);
    try {
      // Apply Validator
      $this->validator->date($date);
      if ($date == 'today') {
        $date = date('Y-m-d');
      }

      $date = explode('-', $date); // explode to get items
      // Generate Tags
      $parentTag = "{$date[0]}-{$date[1]}";
      $childTag = implode('-', $date);
      // Check Cache exists for given date
      if (!$this->redis->exists($parentTag, $childTag)) {
        $this->download->download();
        $data = $this->parser->apply();
        // Write Cache
        $this->redis->set($parentTag, $data);
      }
      // Return response
      return $this->redis->get($parentTag, $childTag);;
    } catch (Exception $ex) {
      throw $ex;
    }
  }

  /**
  * Return all data for current month
  * @return array
  */
  public function getAll()
  {
    // Empty Response data
    $data = array();
    // Parent Tag
    $parentTag = date('Y-m');
    // Check Cache exists for given date
    if ($this->redis->exists($parentTag)) {
      $data = $this->redis->get($parentTag);
    } else {
      $this->download->download();
      $data = $this->parser->apply();
      // Write Cache
      $this->redis->set($parentTag, $data);
    }
    // Return Data
    return $data;
  }
}
