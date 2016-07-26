# Metagatagrapher for Yuml data preparation v 1.1

[![Build Status](https://travis-ci.org/Nono1971/Doctrine-MetadataGrapher.svg?branch=master)](https://travis-ci.org/Nono1971/Doctrine-MetadataGrapher) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/build-status/master) [![Latest Stable Version](https://poser.pugx.org/onurb/doctrine-metadata-grapher/v/stable)](https://packagist.org/packages/onurb/doctrine-metadata-grapher) [![Total Downloads](https://poser.pugx.org/onurb/doctrine-metadata-grapher/downloads)](https://packagist.org/packages/onurb/doctrine-metadata-grapher) 

MetadataGrapher formats objects data to prepare data for [YUML](http://yuml.me) api
to generate visual Entities mapping graphs

## Installation

- add to your project as a composer dependency:
```javascript
    // composer.json
    {
        // ...
        require: {
            // ...
            "onurb/doctrine-metadata-grapher": "~1.1"
        }
    }
```

### Use

Send an array of Doctrine ClassMetadata to the YUMLMetadataGrapher::generateFromMetadata() method  
It returns the string to send to [YUML](http://yuml.me) to get the mapping graph from the api  
If you're a symfony user, you should install onurb/doctrine-yuml-bundle which already uses this library
adding a link into the dev toolbar.
if you're Zend framework user, it is included into the DoctrineORMModule if you install zend-developer-tools


Go to these github repos for concrete examples of use to retrieve the array of ClassMetadata
- [here](https://github.com/Nono1971/doctrine-yuml-bundle)
- or [here](https://github.com/doctrine/DoctrineORMModule)


## -- NEW FEATURE -- Add notes, color your classes or display fields description
Fully optional, you can now display fieldds description,
add color to your map or wwrite notes linked to a specific class.
[![Colored Map with note](http://yuml.me/23e34ac0)](http://yuml.me/23e34ac0)

To do this, the library provides few Doctrine Annotations, to define color, to add notes,
to hide or show attributes properties or display specific methods in the graph
All of them are described below

## Options use


### Display fields description
To display an Entity attributes properties, Use the @ShowAttributesProperties annotation on your entity : 

[![Yuml Graphs](http://yuml.me/5b9d0c6b)](http://yuml.me)

```php
[...]
use Onurb\Doctrine\ORMMetadataGrapher\Mapping as Grapher;
/**
* @Grapher\ShowAttributesProperties
* @ORM\Entity
*/
Class MyEntity
{
    //[...]
}
```

It il also possible to show properties on all Entities and hide it only on less important Entities

=> New optional arguments have been added the the main method generateFromMetadata():

```php
    /**
     * @param ClassMetadata[] $metadata
     * @param boolean $showFieldsDescription
     * @param array $colors
     * @param array $notes
     * @return string
     */
    public function generateFromMetadata(
        array $metadata,
        $showFieldsDescription = false,
        $colors = array(),
        $notes = array()
    )
```

To show fields descriptions just turn the $showField to true
```php
    $metadataGrapher = new \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher();
    $dsl_text = $metadataGrapher->generateFromMetadata($classMetadata, true);
```

It is also possible to hide One Entity (or more) using the @HideAttributesProperties annotation
```php
/**
* @Grapher\HideAttributesProperties
*/
MyEntity
{
    //[...]
}
```

### Use colors
you can use the yuml colors and apply it as you wish , on a class or on an entire namespace.
color priority is given by hierarchy in the namespace...
if Onurb\Doctrine is set to blue, and Onurb\Doctrine\ORMmetadataGrapher to green,
all classes under this namespace will turn green, except a specific color given to a class.


#### Colors usage
The easiest way to define class color is to use annotations in Entities like this :
```php
use Onurb\Doctrine\ORMMetadataGrapher\Mapping as Grapher;
/**
* @Grapher\Color("blue")
*/
```

But doing it on each class can be boring if you want a complete namespace with the same color
(to display a zend Module or a Symfony bundle with a specific color for example).

So it is also possible to define default color on namespaces, but not with annotations, only applied on entities.

It is done using the third argument of generateMetadata() method to inject an associated array.
Color priority is given to Annotations, user can define a color for the namespace, and specifics colors
for classes he want to highlight.
It can be passed by parameters options depending of your framework logic to allow final users customization
=> module_config.php for zend modules
=> resources/config.* for Symfony bundles 
=> ...

```php
    // [...]
    $defaultColors = array(
        'MyModule\\Project\Namespace\FunctionalityOne => 'green,
        'MyModule\\Project\Namespace\FunctionalityTwo => 'red,
        'AnotherModule\\Namespace' => 'violet',
    );
    $dsl_text = $metadataGrapher->generateFromMetadata($classMetadata, false, $defaultColors);
```
A complete list of available colors can be found [here](http://yuml.me/69f3a9ba.svg)
[![Color list](http://yuml.me/69f3a9ba.svg)](http://yuml.me/69f3a9ba.svg)

### Display methods
Final user can display any method he wants in the graph, simply adding an annotation  
 [![Class With Methods](http://yuml.me/82b066e9)](http://yuml.me/82b066e9)
 
 ```php
 use use Onurb\Doctrine\ORMMetadataGrapher\Mapping as Grapher;
 
 // [...]
 /**
 * @Grapher\IsDisplayedMethod()
 */
 public function myImportantMethod()
 {}
 ```

### Add notes
You can now add a note linked in the mapping to an Entity
[![Note graph](http://yuml.me/824c3183)](http://yuml.me/824c3183)

#### Notes usage
Notes are managed by annotations declared directly on classes to note:

```php
use Doctrine\ORM\Mapping as ORM;
use Onurb\Doctrine\ORMMetadataGrapher\Mapping as Grapher;
/**
* @Grapher\Note("My first Entity note")
* @ORM\Entity
*/
class MyEntity{}
```
Note color is yellow by default... Maybe because of post-it color... My choice ;)

you can customize it like this :
```php
/**
* @Grapher\Note(value="An Entity note", color="a_great_yuml_color")
*/
```
As we did with colors, you can also inject an array of notes in the generateMetadata method,
but it is useless with annotations => we don't put notes on namespaces, only Entities.


