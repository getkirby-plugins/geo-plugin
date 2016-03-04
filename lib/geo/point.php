<?php 

namespace Kirby\Geo;

use A;
use Exception;
use Str;

/**
 * Kirby Geo Point Class
 * 
 * @author Bastian Allgeier <bastian@getkirby.com>
 * @license MIT
 * @link https://getkirby.com
 */
class Point {
  
  /**
   * Latitude
   * 
   * @var float
   * @access protected
   */  
  protected $lat;

  /**
   * Latitude
   * 
   * @var float
   * @access protected
   */  
  protected $lng;

  /**
   * Creates a new Point object
   *
   * @param string|float $lat
   * @param string|float $lng
   */
  public function __construct($lat, $lng) {

    if(!is_numeric($lat) or !is_numeric($lng)) {
      throw new Exception('Invalid Geo Point values');
    }

    $this->lat = floatval($lat);
    $this->lng = floatval($lng);

  }

  /**
   * Static method to create a new Geo Point
   * This can be used with various combinations of values
   * 
   * 1.) static::make($lat, $lng)
   * 2.) static::make("$lat,$lng")
   * 3.) static::make([$lat, $lng])
   * 4.) static::make(['lat' => $lat, 'lng' => $lng])
   * 
   * @return Kirby\Geo\Point
   */
  public static function make() {
  
    $args  = func_get_args();
    $count = count($args);

    switch($count) {
      case 1:
        if(is_string($args[0])) {
          $parts = str::split($args[0]);
          if(count($parts) === 2) {
            return new static($parts[0], $parts[1]);
          } 
        } else if(is_array($args[0])) {

          $array = $args[0];

          if(isset($array['lat']) and isset($array['lng'])) {
            return new static($array['lat'], $array['lng']);            
          } else if(count($array) === 2) {
            return new static(a::first($array), a::last($array));
          }

        }
        break;
      case 2:
        return new static($args[0], $args[1]);
        break;
    }

    throw new Exception('Invalid Geo Point values');

  }

  /**
   * Returns the latitude value of the point
   * 
   * @return float
   */
  public function lat() {
    return $this->lat;
  }

  /**
   * Returns the longituted value of the point
   * 
   * @return float
   */
  public function lng() {
    return $this->lng;
  }

  /**
   * Returns the longituted value of the point
   * 
   * @return float
   */
  public function long() {
    return $this->lng();
  }

}