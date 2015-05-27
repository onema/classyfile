# Usage

## Command
A command is provided to enable you to quickly convert files:
```sh
$ php classyfile convert vendor-lib/src/VendorName/Api/Lib/ --create-namespace --offset=2 --length=3
```

The `--code-destinatio` option can be used to set where the files will be saved, the Current Working Directory is used by default. 

As of `v0.2.0` a new option was added that can covert constant names to uppercase (declaration only). Use the option `--constants-to-upper`

When using the `ClassyFile` class you have to available methods: `generateClasses` and `GenerateClassFiles`

## Generate Classes
```php 
<?php
include 'vendor/autoload.php';

$classifile = new \Onema\ClassyFile\ClassyFile();

$codeLocation = 'path/to/directory/with/PHP/Files/';
$code = $classyfile->generateClasses($codeLocation)
var_dump($code);
```
The code returned by the `generateClasses` method is a nested file containing the following information (you can see the sample class [here](tests/mock/mock_classes_style1.php) ):
```
array(1) {
  'mock_classes_style1.php' =>
  array(3) {
    'ServiceSettings' =>
        string "<?php\n\nnamespace Service\\WithBad\\ClassFiles;\n ..."
        'TimeInterval' =>
        string "<?php\n\nnamespace Service\\WithBad\\ClassFiles;\n ..."
        'Scale' =>
        string "<?php\n\nnamespace Service\\WithBad\\ClassFiles;\n ..."
  }
}
```

The returned array follows this structure:
```
[
  'original_file_name1.php' => [
    'ClassName1' [code],
    'ClassName2' [code],
    '...'
  ],
  'original_file_name2.php' => [
    '...'
  ],
]
```

## Generate Class Files
Each class can be saved into it's own file by using the method `generateClassFiles` and passing a code destination location and a code origin location. This method will not return the `code` array.

```php 
<?php
include 'vendor/autoload.php';

$classifile = new \Onema\ClassyFile\ClassyFile();

$codeLocation = 'path/to/directory/with/PHP/Files/';
$codeDestination = '/tmp/';
$classifile->generateClassFiles($codeDestination, $codeLocation);

```

Files will be created under a directory structure following the classes namespace in the `$codeDestination` directory.

If the classes in the files do not contain a namespace you can choose to create it based on a section of the `$filePath`, e.g.

```php
$filePath = 'vendor-lib/src/VendorName/Api/Lib/';
$classifile->generateClassFiles($codeDestination, $filePath, true, 2, 3);
```

This will generate all classes with the `namespace VendorName\Api\Lib;` and will be saved under the `VendorName/Api/Lib/` directory.
