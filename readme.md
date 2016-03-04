# Kirby GEO Plugin

This plugin adds basic geo search and conversion functionalities to Kirby

## Geo Class Option

### geo::point

Creates a new Kirby Geo Point

Example: 

```php
geo::point(49.4883333, 8.4647222);
geo::point('49.4883333, 8.4647222');
geo::point([49.4883333, 8.4647222]);
geo::point(['lat' => 49.4883333, 'lng' => 8.4647222]);
```

### geo::distance

Returns the distance between two geo points.

```php
$mannheim = geo::point(49.4883333, 8.4647222);
$hamburg  = geo::point(53.553436, 9.992247);

echo 'The distance between Mannheim and Hamburg is: ' . geo::distance($mannheim, $hamburg);
```

You can also return the distance in miles instead of kilometers

```php
echo 'The distance between Mannheim and Hamburg is: ' . geo::distance($mannheim, $hamburg, 'mi');
```

### geo::niceDistance

Returns the distance between two geo points in a human readable way (i.e. 461.32 km)

```php
$mannheim = geo::point(49.4883333, 8.4647222);
$hamburg  = geo::point(53.553436, 9.992247);

echo 'The distance between Mannheim and Hamburg is: ' . geo::niceDistance($mannheim, $hamburg);
```

You can also return the "nice distance" in miles instead of kilometers

```php
echo 'The distance between Mannheim and Hamburg is: ' . geo::niceDistance($mannheim, $hamburg, 'mi');
```

### geo::locate

Runs the Google geo locator to find the latitude and longitude for a certain address

```php
$mannheim = geo::locate('Mannheim, Germany'); 

echo $mannheim->lat();
echo $mannheim->lng();
```

## Radius Filter

The plugin automatically adds a new filter for all collections, which can be used to do a radius search:

```php
$addresses = page('addresses')->children()->filterBy('location', 'radius', [
  'lat'    => 49.4883333,
  'lng'    => 8.4647222,
  'radius' => 10
]);
```

To make this work, the location field for each address page must be in the following format: 

```
location: {lat},{lng}
```

or with a real life example: 

```
location: 49.4883333,8.4647222
```

You can also filter in miles

```php
$addresses = page('addresses')->children()->filterBy('location', 'radius', [
  'lat'    => 49.4883333,
  'lng'    => 8.4647222,
  'radius' => 10,
  'unit'   => 'mi'
]);
```

## Field Methods

The plugin also adds a set of field methods, which can be handy to work with locations

### coordinates

Converts a field with the value format {lat},{lng} into a valid Kirby Geo Point Object:

```php
$page->location()->coordinates()->lat();
$page->location()->coordinates()->lng();
```

### distance

Calculates the distance between a location field and another Kirby Geo Point:

```php
$hamburg = geo::point(53.553436, 9.992247);

echo $page->location()->distance($hamburg);
```

Of course you can run this in miles again:

```php
$hamburg = geo::point(53.553436, 9.992247);

echo $page->location()->distance($hamburg, 'mi');
```

### niceDistance

Returns the distance in a more human friendly format:

```php
$hamburg = geo::point(53.553436, 9.992247);

echo $page->location()->niceDistance($hamburg);
```

## License

<http://www.opensource.org/licenses/mit-license.php>

## Author

Bastian Allgeier <https://getkirby.com>