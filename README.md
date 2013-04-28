# Bootstrap stuff from xml  
[![Build Status](https://travis-ci.org/iwyg/xmlconf.png?branch=master)](https://travis-ci.org/iwyg/xmlconf)


## Synopsis

This package lets you easily bootstrap data, objects, or whatever from an xml configuration file.
The xml configuration file however must validate against a provided xsd schema.  



## Installation

Add thapp/xmlconf as a requirement to composer.json:

```json
{
    "require": {
        "thapp/xmlconf": "1.0.*"
    }
}
```

Then run `composer update` or `composer install`

Next step is to tell laravel to load the serviceprovider. In `app/config/app.php` add

```php
  // ...
  'Thapp\XmlConf\XmlConfServiceProvider' 
  // ...
```
to the `providers` array.

### Publish configuration

```sh
php artisan config:publish thapp/xmlconfig
```
 

## Examples 

This package provides an example directory that should get you started quickly.

For a first glimpse you may copy the `storage/sections/config.xml` that is included with this package to
`app/storage/sections/config.xml`.

Next, create a new route. Somethig like this should do: 

```php
Route::get('/examples', function () use ($app) {
    $SectionRepository = new Thapp\XmlConf\Examples\Sections\Repository($app['xmlconf.sections']);
    var_dump($SectionRepository);
});

```

## General 

### File structure

```
- Vendor/
  - ReaderName/
    - Schema/
      - readername.xsd
    - ReaderNameSimpleXml.php
    - ReaderNameConfigReader.php
```    

### xml storage structure

```
- app/
  - storage/
    - ReaderName/
      - config.xml    
      
```   
  
