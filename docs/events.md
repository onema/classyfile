# Events
`ClassyFile` emits three events to allow you to extend the basic functionality of this library.

## classyfile.traverse
This event is emitted after the file has been opened and parsed. The event will contain the following values:

- 'statements': an array containing all the statements in the file.
- 'create_namespace': boolean value with selected option to create name spaces.
- 'offset': path offset used to generate the namespace.
- 'length': used to determine how many sections of the path to use starting at the given offset.

All values can be modified by the listeners.

## classyfile.get_class
This event is emitted before the new class is generated and saved. This allow us to make modifications to the generated class. The event will contain the following values:

- Class statement (subject): `Class_` statement. This can be retrieved from the event like such: `$event->getStatement()`.
- 'namespace': string containing the current namespace. Empty if no namespace was set.
- 'file_location': place where the new class will be saved.
- 'uses': any use statements to be added to the class.

## classyfile.after_get_class
This event is emitted after the new class is generated. This allow us to make modifications to the class after it has been added to the tempalte. This is where the plugin to save the classes to a files system get's triggered. The event will contain the following values:

- Class statement (subject): `Class_` statement. This can be retrieved from the event like such: `$event->getStatement()`.
- 'code': the generated code
- 'file_location': place where the new class will be saved.
