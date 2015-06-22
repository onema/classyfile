classyfile
==========
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7cd81039-46d2-4a18-b57b-6242cb18f3b4/mini.png)](https://insight.sensiolabs.com/projects/7cd81039-46d2-4a18-b57b-6242cb18f3b4)
[![Code Climate](https://codeclimate.com/github/onema/classyfile/badges/gpa.svg)](https://codeclimate.com/github/onema/classyfile)
[![Build Status](https://travis-ci.org/onema/classyfile.svg?branch=develop)](https://travis-ci.org/onema/classyfile) [![Coverage Status](https://coveralls.io/repos/onema/classyfile/badge.svg?branch=develop)](https://coveralls.io/r/onema/classyfile?branch=develop) 
[![Documentation Status](https://readthedocs.org/projects/classyfile/badge/?version=latest)](http://classyfile.readthedocs.org/en/latest/)
[![MIT License](https://img.shields.io/packagist/l/onema/classyfile.svg?style=flat)](https://tldrlegal.com/license/mit-license)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/onema/classyfile/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/onema/classyfile/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/onema/classyfile/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/onema/classyfile/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/onema/classyfile/badges/build.png?b=master)](https://scrutinizer-ci.com/g/onema/classyfile/build-status/master)

## Summary
Provides a way to break PHP files containing multiple classes and creates single files per class.
This can be useful when refactoring old libraries. 

This library uses the [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser) to parse all the php classes.

See the [Documentation](http://classyfile.readthedocs.org/en/latest/) for more information. 

## Requirements
 
  - PHP 5.4.0
  
## Installation

### Install it using composer

```
composer require 'onema/classyfile:^1.0.0'
```

Or manually add it to the `composer.json` file

```json
{
    "require": {
        "onema/classyfile": "^1.0.0"
    }
}
```

After it has been installed you can run the command: `php vendor/bin/classyfile`

### Download from github
You can download the project from github. You still need to use composer to install all the dependencies.
```
git clone git@github.com:onema/classyfile.git
cd classyfile
composer install
```
After it has been installed you can run the command: `php classyfile`

## Documentation
See the [documentation](docs/index.md) section.
