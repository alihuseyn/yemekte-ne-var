<?php

namespace Helper;

/**
* -------------------------------------------------------
* Images Class
* This class helps to make operations on images url.
* Detect meal and try to generate images url for it
* if the image exists for it give generated image url
* otherwise return default
* --------------------------------------------------------
* @version 1.0
* @since 28.01.2018
*/
class Images {

  /**
  * @var string $folder - images folder
  */
  private $folder;

  /**
  * @var string $default - default image
  */
  private $default_image;

  /**
  * @var Formatter $formatter
  */
  private $formatter;

  /**
  * Constructor
  */
  public function __construct()
  {
    $this->formatter = new Formatter();
    $this->folder = (dirname(__DIR__).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR);
    $this->default_image = $this->url('default');
  }

  /**
  * Generate image path according to given meta
  * @param string $meta
  * @return path for given meta
  */
  private function folderPath ($meta)
  {
    return $this->folder . $meta . '.png';
  }

  /**
  * Generate image url for given meta
  * @param string $meta
  * @return url for given meta
  */
  private function url ($meta)
  {
    $url = 'http://'.$_SERVER['HTTP_HOST'].'/images/'.$meta.'.png';
    if (isset($_SERVER['HTTPS'])) {
      $url = 'https://'.$_SERVER['HTTP_HOST'].'/images/'.$meta.'.png';
    }

    return $url;
  }

  /**
  * Generate image url and append it to the array.
  * If image not found the default image url will be
  * supplied for usage.
  * @param array $data
  * @return array
  */
  public function append ($data)
  {
    $response = array();
    foreach ($data as $key => $value) {
      if (!is_array($data[$key])) {
        $response[$key] = $value;
      } else {
        $response[$key] = array();
        foreach ($data[$key] as $meal) {
          $meta = strtoupper($this->formatter->meta($meal));
          $absolutePath = $this->folderPath($meta);
          $url = file_exists($absolutePath) ? $this->url($meta) : $this->default_image;
          array_push($response[$key], [
            'name' => $meal,
            'image' => $url
          ]);
        }
      }
    }

    return $response;
  }
}
