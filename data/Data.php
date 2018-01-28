<?php

namespace DataLayer;

use Helper\Formatter;
use Helper\Download;
use Helper\Parser;
use Helper\Validator;
use Helper\Redis;
use Helper\Images;

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
  * @var Images $images
  */
  private $images;

  /**
  * @var Parser $parser
  */
  private $parser;

  /**
  * @var Redis $redis
  */
  private $redis;

  /**
  * @var string $type - API|SLACK
  */
  private $type;

  /**
  * Constructor
  * Initialize and set required objects
  */
  public function __construct($type = 'API')
  {
    $this->formatter = new Formatter();
    $this->validator = new Validator();
    $this->download = new Download();
    $this->parser = new Parser();
    $this->parser->setSourceFile($this->download->getFileDestination());
    $this->redis = new Redis();
    $this->images = new Images();
    // Type api | slack
    $this->type = $type;
  }

  /**
  * Check whether given array is associative array
  * or not and return boolean value accordingly.
  * @param array $arr
  * @return boolean
  */
  private function is_assoc(array $arr)
  {
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

  /**
  * Convert api type to slack required text format
  * @param array $data
  * @return array
  */
  private function slack (array $data)
  {
    $response = [
      'text' => 'Bugün ne var acaba yemekte ?',
      'attachments' => array()
    ];

    foreach ($data['meals'] as $meal) {
      array_push($response['attachments'], [
        'title' => $meal['name'],
        'image_url' => $meal['image']
      ]);
    }

    return $response;
  }

  /**
  * Format given data according to selected
  * type. if the type is SLACK then format
  * will be shown for slack json format otherwise
  * will be used default API tag and json format
  * will be standart. In default images for each meal
  * will be applied.
  * @param array $data
  * @param string $typ
  * @return array
  */
  private function format ($data, $type = 'API')
  {
    $response = array();
    // Convert to default api type
    if ($this->is_assoc($data)) {
      $response = $this->images->append($data);
    } else {
      foreach ($data as $item) {
        array_push($response, $this->images->append($item));
      }
    }

    if ($this->type === 'SLACK') {
      // Convert to slack required format
      $response = $this->slack($response);
    }

    return $response;
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
      return $this->format($this->redis->get($parentTag, $childTag));
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
    return $this->format($data);
  }
}
