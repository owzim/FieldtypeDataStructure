# Fieldtype DataStructure

---

#### for ProcessWire 2.5

Field that stores Object data and formats it as an object, when requested.

## Setup

After installation create a new `field`, let's say called `people` and assign it to a `template`, or just edit an existing text-based `field` and choose `Object` for the `type`, save!

In the `Details`-Tab you have some options you can choose from:

**Parse as**

Default is `WireData/-Array`, the data can also be parsed as `Object` or `Associative Array`.

`Associative Array` is the fastest and the default output by the used *Spyc* parser, `WireData/-Array` might be the slowest (because the entire array is recursiveley converted), but it's also the most feature rich. You can access properties like you are used to with *pages* or *fields*, like `$page->people->implode(',', 'name')` (arrays) or `$person->get('title|name')` (objects), see code example below.

**Font Family**

The font stack used for the `Textarea`, default is `Consolas, Monaco, Andale Mono, monospace`. Since we write Object in here, a monospace font makes sense.

## Usage

Now, in your just created field you can put in some Object like this:

```Object
- name: Jane Doe
  occupation: Product Manager
  age: 33
  hobbies:
    - running
    - movies
- name: John Doe
  occupation: Service Worker
  age: 28
  hobbies:
    - cycling
    - fishing

```

In your template, or wherever you are accessing the page, you would use it like any other ProcesssWire data (if you set the parse option to either `WireData/-Array` or `Object`):

```PHP
$out = '';
foreach ($page->people as $person) {
   $out .= "Name: {$person->name} <br>";
   $out .= "Occupation: {$person->occupation} <br>";
   $out .= "Age: {$person->age} <br>";
   $out .= "Hobbies: <br>";
   foreach ($person->hobbies as $hobby) {
      $out .= "- {$hobby} <br>";
   }
   $out .= "--- <br>";
}
echo $out;
```

### More info about Object:

* [Complete idiot's introduction to Object](https://github.com/Animosity/CraftIRC/wiki/Complete-idiot%27s-introduction-to-yaml)
* [Specification](http://yaml.org/spec/1.0/)
* [Wikipedia](http://en.wikipedia.org/wiki/Object)

### Acknowledgements

* I've used a namespaced version of the Autoloader class from [Template Data Providers](https://github.com/marcostoll/processwire-template-data-providers)
* The Object parser is a namespaced version of [Spyc](https://github.com/mustangostang/spyc)



### Change Log

* **0.4.0** rename module to FieldtypeDataStructure
* **0.3.5** add 'inputType' Matrix Object
* **0.3.4** add field config values tests
* **0.3.3** add proper config value getting, add feature to save default values on very first save, prepare CSV type
* **0.3.2** add 'showIf' for 'outputAs', make descriptions and labels clearer
* **0.3.1** rename module to FieldtypeObject
* **0.3.0** add more input types than just Object
* **0.2.5** convert InputfieldTextarea to InputfieldText if only one row is set
* **0.2.4** implement runtime caching
* **0.2.3** make default 'toString' output the name label of the field, if WireData/-Array is selected
* **0.2.2** add unit tests
* **0.2.1** add additional value checking before converting to object
* **0.2.0** add WireArray feature
* **0.1.1** move all classes into the `FieldtypeDataStructure` namespace
* **0.1.0** initial version

