<?php

namespace Helper;

/**
* ------------------------------------------
* Redis Class Help to easily make connection
* with redis over predis library. This class
* written according to project structure.
* ------------------------------------------
* @version 1.0
* @since 21.01.2018
*/
class Redis {

  /**
  * @var $redis - redis connection
  */
  private $redis;

  /**
  * Constructor
  */
  public function __construct()
  {
    // Initialize Redis Connection
    $this->redis = new \Predis\Client(getenv('REDIS_URL'));
  }

  /**
  * Check whether the given keys exists on redis.
  * If the childTag is given null then check with exists function
  * otherwise check with hexists function for hash type
  * @param string $parentTag
  * @param string $childTag
  * @return bool true/false
  */
  public function exists ($parentTag, $childTag = null) 
  {
    if (!empty($childTag)) {
      return boolval($this->redis->executeRaw(['HEXISTS', $parentTag, $childTag]));
    } else {
      return boolval($this->redis->executeRaw(['EXISTS', $parentTag]));
    }
  }

  /**
  * Return data according to given tags
  * If data not found return empty array
  * @param $parentTag
  * @param $childTag
  * @return array
  */
  public function get ($parentTag, $childTag = null)
  {
    $response = array();
    if ($this->exists($parentTag, $childTag)) {
      if (!empty($childTag)) {
        $response = json_decode($this->redis->hget($parentTag, $childTag), true);
      } else {
        // raw response
        $response = $this->redis->hgetall($parentTag);
        // sort according to dates
        ksort($response);
        // get values only without keys
        $response = array_values($response);
        // map json_decode for items
        $response = array_map(function($item) {
          return json_decode($item, true);
        }, $response);
      }
    }

    return $response;
  }

  /**
  * Set data according to given tag and its date parameter
  * @param $parentTag
  * @param $data
  * @return void
  */
  public function set ($parentTag, $data)
  {
    foreach ($data as $meal) {
      $childTag = $meal['date'];
      $dataAsJson = json_encode($meal);
      $this->redis->executeRaw(['HSET', $parentTag, $childTag, $dataAsJson]);
    }
  }

}
