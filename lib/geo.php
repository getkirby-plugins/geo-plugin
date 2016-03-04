<?php 

namespace Kirby;

use A;
use Exception;
use Str;
use Remote;
use Obj;

/**
 * Kirby Geo Class
 * 
 * @author Bastian Allgeier <bastian@getkirby.com>
 * @license MIT
 * @link https://getkirby.com
 */
class Geo {

  /**
   * Creates a new Kirby Geo Point object
   * 
   * @see Kirby\Geo\Point::make
   * @return Kirby\Geo\Point
   */
  public static function point() {
    return call_user_func_array('Kirby\\Geo\\Point::make', func_get_args());
  }

  /**
   * Converts Miles to Kilometers
   * 
   * @param int|float $miles
   * @return float
   */
  public static function milesToKilometers($miles) {
    return $miles * 1.60934;
  }

  /**
   * Converts Kilometers to Miles
   * 
   * @param int|float $kilometers
   * @return float
   */
  public static function kilometersToMiles($kilometers) {
    return $kilometers * 0.621371;
  }

  /**
   * Calculates the distance between to Kirby Geo Points
   * 
   * @param Kirby\Geo\Point|string $a   
   * @param Kirby\Geo\Point|string $b
   * @param null|string $unit ("km", "mi")
   * @return float
   */
  public static function distance($a, $b, $unit = 'km') {

    if(!is_a($a, 'Kirby\\Geo\\Point')) {
      $a = geo::point($a);
    }    

    if(!is_a($b, 'Kirby\\Geo\\Point')) {
      $b = geo::point($b);
    }    

    $theta = $a->lng() - $b->lng();
    $dist  = sin(deg2rad($a->lat())) * sin(deg2rad($b->lat())) +  cos(deg2rad($a->lat())) * cos(deg2rad($b->lat())) * cos(deg2rad($theta));
    $dist  = acos($dist);
    $dist  = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    if(strtolower($unit) === 'km') {
      return static::milesToKilometers($miles);      
    } else {
      return $miles;
    }

  }

  /**
   * Calculates the distance between to Kirby Geo Points
   * and returns the result in a a human readable format
   * 
   * @param Kirby\Geo\Point|string $a   
   * @param Kirby\Geo\Point|string $b
   * @param null|string $unit ("km", "mi")
   * @return string
   */
  public static function niceDistance($a, $b, $unit = 'km') {
    return number_format(static::distance($a, $b, $unit), 2) . ' ' . strtolower($unit);
  }

  /**
   * Returns a Kirby Geo Point for a given address
   * 
   * @param string $address
   * @param array $components Additional component info for the Google Geo Locator
   * @return Kirby\Geo\Point
   */
  public static function locate($address, $components = []) {

    foreach($components as $key => $component) {
      $components[$key] = strtolower($key) . ':' . urlencode(strtolower($component));
    }

    $string   = str_replace(' ', '+', urlencode($address));
    $url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $string . '&components=' . implode('|', $components) . '&sensor=false';
    $response = remote::get($url);

    if($response->error()) {
      throw new Exception('The Google geocoder call failed');
    }

    $content  = json_decode($response->content(), true);
    $results  = a::get($content, 'results', []);
    $first    = a::first($results, []);
    $geometry = a::get($first, 'geometry', []);
    $location = a::get($geometry, 'location', []);

    return static::point([
      'lat' => str_replace(',', '.', a::get($location, 'lat')),
      'lng' => str_replace(',', '.', a::get($location, 'lng')),
    ]);

  }

}