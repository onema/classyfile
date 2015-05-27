# ClassyFile 

## Summary
Provides a way to break PHP files containing multiple classes and creates single files per class.
This can be useful when refactoring old libraries. 

This library uses the [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser) to parse all the php classes.

## Requirements
  - PHP 5.5.0
  
## Installation

### Install it using composer 

```json
{
    "require": {
        "onema/classyfile": "^0.3.0@dev"
    },
    "minimum-stability": "dev"
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

