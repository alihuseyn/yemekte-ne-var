<?php

namespace Helper;

/**
* -------------------------------------------------------
* Download Class 
* This Class helps to download data xls file from source
* to defined destination. Also filename for destination and 
* url address generation done inside of this file.
* --------------------------------------------------------
* @version 1.0
* @since 21.01.2018
*/
class Download {

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
  }

   /**
  * Download File From Source to 
  * Destination Folder with same name
  * @return void
  */
  public function download() 
  {
    $destination = $this->getFileDestination();
    $file = fopen($destination, 'w+');
    $client = new \GuzzleHttp\Client();
    $stream = \GuzzleHttp\Psr7\stream_for($file);
    $client->request('GET', $this->getFileSource(), ['save_to' => $stream]);
    fclose($file);
  }

  /**
  * Return File Destination Source
  * @return String file destination path
  */
  public function getFileDestination() 
  {
    return dirname(__DIR__).DIRECTORY_SEPARATOR.'xls'.DIRECTORY_SEPARATOR.$this->getFileName();
  }

  /**
  * Return File Source URL
  * @return String file source url
  */
  public function getFileSource() 
  {
    $username = getenv('VESTEL_USERNAME');
    $password = getenv('VESTEL_PASSWORD');
    $url = getenv('VESTEL_MEAL_URL');
    // Response URL
    // Note: username & password added to complete basic authentication
    $url = "https://{$username}:{$password}@{$url}";
    // Format
    $url .= $this->getFileName();

    return $url;
  }

  /**
  * Return file name on destination. The file name change 
  * dynamically according to month and year
  * @return String destination & source file name
  */
  public function getFileName ()
  {
    $format = getenv('VESTEL_XLS_FORMAT');
    // Year & month
    $now = explode('-', strftime('%B-%Y', time()));
    $month = $this->formatter->allUpper($now[0]);
    $year =  $now[1];
    // generate filename
    $filename = str_replace(':year', $year ,str_replace(':month', $month, $format));

    return $filename;
  }
}
