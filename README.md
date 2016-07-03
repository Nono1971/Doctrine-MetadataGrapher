# Metagatagrapher for Doctrine ORM Module for Zend Framework 2 and Onurb Yuml Bundle for symfony 3 (compatible with symfony 2)

[![Build Status](https://travis-ci.org/Nono1971/Doctrine-MetadataGrapher.svg?branch=master)](https://travis-ci.org/Nono1971/Doctrine-MetadataGrapher) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Nono1971/Doctrine-MetadataGrapher/build-status/master) 

MetadataGrapher formats objects data to prepare graph generated with yuml.me api

## Installation

Installing this bundle can be done through these simple steps:

- add to your project as a composer dependency:
```javascript
    // composer.json
    {
        // ...
        require: {
            // ...
            "onurb/doctrine-metadata-grapher": "~1.0"
        }
    }
```

### Use

Send an array of Doctrine ClassMetadata
to the YUMLMetadataGrapher::generateFromMetadata() method
it returns the string to send to yuml.me to get the mapping graph from the api

if you're a symfony user, I recommand to install instead onurb/doctrine-yuml-bundle wich use this library
if you're Zend framework user, it is included into the DoctrineORMModule if you install zend-developer-tools

go to there github repos for concrete examples of use
- here : https://github.com/Nono1971/doctrine-yuml-bundle
- or here : https://github.com/doctrine/DoctrineORMModule
