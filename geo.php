<?php 

/**
 * Kirby GEO Plugin
 * 
 * @author Bastian Allgeier <bastian@getkirby.com>
 */

use Kirby\Geo;

/**
 * Autoloader for all Kirby GEO Classes
 */
load([
  'kirby\\geo'        => __DIR__ . DS . 'lib' . DS . 'geo.php',
  'kirby\\geo\\point' => __DIR__ . DS . 'lib' . DS . 'geo' . DS . 'point.php'
]);

/**
 * Adds a new radius filter to all collections
 */
collection::$filters['radius'] = function($collection, $field, $options) {

  $origin = geo::point(a::get($options, 'lat'), a::get($options, 'lng'));
  $radius = intval(a::get($options, 'radius'));
  $unit   = a::get($options, 'unit', 'km') === 'km' ? 'km' : 'mi';

  if(!$origin) {
    throw new Exception('Invalid geo point for radius filter. You must specify valid lat and lng values');
  }

  if($radius === 0) {
    throw new Exception('Invalid radius value for radius filter. You must specify a valid integer value');
  }

  foreach($collection->data as $key => $item) {
    
    $value = collection::extractValue($item, $field);

    // skip invalid points
    if(!is_string($value) and !is_a($value, 'Field')) {
      unset($collection->$key);
      continue;
    } 

    try {
      $point = geo::point((string)$value);            
    } catch(Exception $e) {
      unset($collection->$key);
      continue;            
    }

    $distance = geo::distance($origin, $point, $unit);

    if($distance > $radius) {
      unset($collection->$key);          
    }

  }

  return $collection;

};

/**
 * Adds a new field method "coordinates", 
 * which can be used to convert a field with 
 * comma separated lat and long values to a Kirby Geo Point
 */
field::$methods['coordinates'] = function($field) {
  return geo::point($field->value);
};

/**
 * Adds a new field method "distance", 
 * which can be used to calculate the distance between a
 * field with comma separated lat and long values and a
 * valid Kirby Geo Point
 */
field::$methods['distance'] = function($field, $point, $unit = 'km') {
  if(!is_a($point, 'Kirby\\Geo\\Point')) {
    throw new Exception('You must pass a valid Geo Point object to measure the distance');
  }
  return geo::distance($field->coordinates(), $point, $unit);
};

/**
 * Same as distance, but will return a human readable version 
 * of the distance instead of a long float
 */
field::$methods['niceDistance'] = function($field, $point, $unit = 'km') {
  if(!is_a($point, 'Kirby\\Geo\\Point')) {
    throw new Exception('You must pass a valid Geo Point object to measure the distance');
  }
  return geo::niceDistance($field->coordinates(), $point, $unit);
};