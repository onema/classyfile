# classyfile
Provides a way to break PHP files containing multiple classes and creates single files per class.
This can be useful when refactoring old libraries. 

This library uses the [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser).

## Usage

Using the `ClassyFile` class:

```php 
<?php
include 'vendor/autoload.php';

$classifile = new \Onema\ClassyFile\ClassyFile();

$filePath = 'path/to/directory/with/PHP/Files/';
$classifile->generateClassFiles($filePath);

```

Files will be created under a directory structure following the classes namespace. All directories are created at the Current Working Directory.

If the classes in the files do not contain a namespace you can choose to create it based on a section of the `$filePath`, e.g.

```php
$filePath = 'vendor-lib/src/VendorName/Api/Lib/';
$classifile->generateClassFiles($filePath, true, 2, 3);
```

This will generate all classes with the `namespace VendorName\Api\Lib;` and will be saved under the `VendorName/Api/Lib/` directory.

### Command
A command is provided to enable you to quickly convert files
```sh
$ php classyfile convert vendor-lib/src/VendorName/Api/Lib/ --create-namespace --offset=2 --length=3
```
## Events
`ClassyFile` emits two events to allow you to extend the basic functionality of this library.

### classyfile.traverse
This event is emitted after the file has been opened and parsed. The event will contain the following values:

- 'statements': an array containing all the statements in the file.
- 'create_namespace': boolean value with selected option to create name spaces.
- 'offset': path offset used to generate the namespace.
- 'length': used to determine how many sections of the path to use starting at the given offset.

All values can be modified by the listeners.

### classyfile.set_class
This event is emitted before the new class is generated and saved. This allow us to make modifications to the generated class. The event will contain the following values:

- Class statement (subject): `Class_` statement. This can be retrieved from the event like such: `$event->getStatement()`.
- 'namespace': string containing the current namespace. Empty if no namespace was set.
- 'file_location': place where the new class will be saved.
- 'uses': any use statements to be added to the class.


