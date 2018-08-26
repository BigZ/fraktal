# Fraktal

A demo symfony project to test out our new fractal-based library.

Goal: provide a fully-featured petstore example using our lib, with a state of the art testing (think spec + story bdd & dredd)

## TODO

- Go for petstore instead of artist manager

- Split common code in a bundle: service declaration, subscriber, paramconverter. There:
- !config
- !global error catcher that display a json error
- ?suggest fos rest and allow swagger dump if installed
- ?create a command to automatically add annotations / confg on entities
- !headers map according to spec+serialisation schema


## planned config for bundle
```
format_listner: off|force|header|file_extension -> default to header 
	(forces output schema to the default one or let the user choose which format he wents by providing 
	either an accept header or a file extension. You can also disable automatic serialization by choosing
	the "off" value. you will then have to call the serializer yourself like shown in the above doc)
format_default: jsonapi|hal|json|xml|array -> default to json
format_errors: true|false -> default false in dev, true in prod (formats error in the demanded format)

entity_reader: doctrineorm|doctrineodm|... -> default to doctrineorm

```
