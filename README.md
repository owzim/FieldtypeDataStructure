# Fieldtype DataStructure

---

#### for ProcessWire 2.5

Field that stores any structural string-based data and converts it to object or array, when requested.

## Index

* [Setup](#setup)
* [Config settings](#config-settings)
  * [Input type](#input-type)
  * [Output as](#output-as)
  * [Delimiter](#delimiter)
  * [Font Family](#font-family)
* [Usage](#usage)
* [More info about YAML](#more-info-about-yaml)
* [Acknowledgements](#acknowledgements)
* [Change Log](#change-log)

## Setup

After installation create a new *Field*, let's say called `people` and assign it to a *Template*, or just edit an existing text-based field and choose `DataStructure` for the *Type*, save!


## Config settings

After saving the *Details tab* presents some new config settings you can choose from:

### Input type

The input type specifies how the actual text input should be parsed.

##### YAML (Object/Array), example:

```YAML
- name: Jane Doe
  age: 33
  hobbies:
    - running
    - movies
- name: John Doe
  age: 28
  hobbies:
    - cycling
    - fishing
```

##### Matrix, delimiter (in this case a comma) and line-break separated (Array)

```
r0c0,r0c1,r0c2,r0c3
r1c0,r1c1,r1c2,r1c3
r2c0,r2c1,r2c2,r2c3
r4c0,r4c1,r4c2,r4c3
```

##### Matrix, first row as keys (Array/Object)

```
name,        height, hasSunglasses,     isTheChosenOne
Neo,         178cm,  yes,               yes
Trinity,     171cm,  most of the times, no
Morpheus,    186cm,  yes,               no
Agent Smith, 182cm,  of course,         nope
```

##### Delimiter (in this case a comma) separated (Array)

```
swimming,bathing,diving,motorboating
```

##### Line-break separated (Array)

Example: 

```
milk
apples
bananas
toilet paper
Lamborghini
```

##### JSON (Object)

---

### Output as

Default is `WireData/-Array`, the data can also be parsed as `Object` or `Associative Array`. This option is only available for `Input Type` `YAML`, `Object Matrix` or `JSON`.

`Associative Array` is the fastest and the default output by the used *Spyc* parser, `WireData/-Array` might be the slowest (because the entire array is recursiveley converted), but it's also the most feature rich. You can access properties like you are used to with *pages* or *fields*, like `$page->people->implode(',', 'name')` (arrays) or `$person->get('title|name')` (objects), see code example below.

---

### Delimiter

Specifies the delimiter for `Matrix`, `Object Matrix` or `Delimiter separated`.

---

### Font Family

The font stack used for the `Textarea`.

## Usage

In your template, or wherever you are accessing the page, you would use it like any other ProcesssWire data (if you set the *Output as* option to either `WireData/-Array` or `Object`). This example is accessing data created by the YAML parser.

```PHP
$out = '';
foreach ($page->people as $person) {
   $out .= "Name: {$person->name} <br>";
   $out .= "Age: {$person->age} <br>";
   $out .= "Hobbies: <br>";
   foreach ($person->hobbies as $hobby) {
      $out .= "- {$hobby} <br>";
   }
   $out .= "--- <br>";
}
echo $out;
```

### More info about YAML

* [Complete idiot's introduction to Object](https://github.com/Animosity/CraftIRC/wiki/Complete-idiot%27s-introduction-to-yaml)
* [Specification](http://yaml.org/spec/1.0/)
* [Wikipedia](http://en.wikipedia.org/wiki/Object)

### Acknowledgements

* I've used a namespaced version of the Autoloader class from [Template Data Providers](https://github.com/marcostoll/processwire-template-data-providers)
* The Object parser is a namespaced version of [Spyc](https://github.com/mustangostang/spyc)



### Change Log

* **0.4.3** add uncache method
* **0.4.2** implement configurable 'delimiter', move parse parameters to options array
* **0.4.1** make 'delimiter' configurable
  * **0.3.5** add 'inputType' Matrix Object
  * **0.3.4** add field config values tests
  * **0.3.3** add proper config value getting, add feature to save default values on very first save
  * **0.3.2** add 'showIf' for 'outputAs', make descriptions and labels clearer
* **0.3.0** add more input types than just Object
  * **0.2.5** convert InputfieldTextarea to InputfieldText if only one row is set
  * **0.2.4** implement runtime caching
  * **0.2.3** make default 'toString' output the name label of the field, if WireData/-Array is selected
  * **0.2.2** add unit tests
  * **0.2.1** add additional value checking before converting to object
* **0.2.0** add WireArray feature
  * **0.1.1** move all classes into the `FieldtypeDataStructure` namespace
* **0.1.0** initial version

