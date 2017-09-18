# Perch Locator v3.0.0

Perch Locator is an app to manage locatable resources within Perch CMS. Addresses are Geocoded and can be searched using coordinates or by a valid address to allow users to find places of interest near to them.

## Installation

### Perch 2.x

If you still wish to use this application on Perch 2 please see the [v2.x branch](https://github.com/RootStudio/Perch-Locator/tree/v2.x). All active development will now take place on Perch 3.

### Fresh Install
Upload the `root_locator` directory to `perch/addons/apps` and add `root_locator` to your `config/apps.php` file.

Example:

	<?php
	    $apps_list = array(
	        'content', 
	        'categories',
	        'root_locator'
	    );
	    
Before you can begin geocoding you must set an API key in the Perch settings area. This should have the following Google APIs enabled:

* Geocoding
* Static Maps

### Upgrading

If you are upgrading from v1 you must follow these steps:

1. Remove the old `jw_locator` application from your `perch/addons/apps` directory.
2. Upload the new app folder as per the instructions above.
3. Log in to Perch and visit the new locator app. It will have the same name in the Apps menu but will have a different path of `root_locator` rather than `jw_locator`.
4. The app should automatically install and begin importing old location data. Failed jobs from the previous app due to API quota exceptions will be automatically requeued.
5. When the import is complete you can remove the old database tables. It is recommended you take a full backup of them first.

Finally there are some changes to the runtime functions that will cause breaking changes in your code:

* `jw_locator_get_custom` is now `root_locator_get_custom`
* `jw_locator_location_json` has been removed.
* The search distance key is now `range` and not `radius`
* The `json` output option is no longer used, instead render `addresses_list_json.html` or `address_json.html`, these may need modifying to match the older JSON format.

---
	    
## Using the App

Like standard Perch apps, the Locator can be accessed using the Apps menu in the top of the CMS admin area. Inside the app you are able to create new addresses or import in bulk using a CSV file. It is not recommended to give clients access to the import section and instead request data from them to format yourself. You can disable access by setting the correct priveledges for the user roles.

CSV Data must include the following columns:

* `addressTitle` (required)
* `addressBuilding` (required)
* `addressStreet` (recommended)
* `addressTown`
* `addressRegion`
* `addressPostcode` (required)
* `addressCountry`

Rows that are missing any of the required fields will not be imported. Those missing recommended fields will be imported but may fail in the geocoding queue.

**v2.1**: You can now include custom fields in the CSV import. Any additional columns that are not listed above will be included in the dynamic fields array to be used in your templates.

---

## Displaying on a map
To display your location results onto a map, take a look at the example code in the `locations/index.php` file.

---

## Function Reference

### root\_locator\_address
Display a single address found by ID.

#### Parameters
<table>
    <thead>
        <tr>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Integer</td>
            <td>ID of the address to show</td>
        </tr>
        <tr>
            <td>Boolean</td>
            <td>Set to <code>true</code> to have the value returned instead of echoed.</td>
        </tr>
    </tbody>
</table>

#### Usage

```php
<?php
    $id = perch_get('address'); 
    root_locator_address($id); 
?>
```

### root\_locator\_address\_field
Outputs a single field from the address template, this may be useful for setting the page title.

#### Parameters
<table>
    <thead>
        <tr>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Integer</td>
            <td>ID of the address</td>
        </tr>
        <tr>
            <td>String</td>
            <td>The ID of the template field to return</td>
        </tr>
        <tr>
            <td>Boolean</td>
            <td>Set to <code>true</code> to have the value returned instead of echoed.</td>
        </tr>
    </tbody>
</table>

#### Usage

```php
<?php
    $id = perch_get('address');
    echo '<title>' . root_locator_address_field($id, 'addressTitle', true) . '</title>';
?>
```

### root\_locator\_nearby
Returns a list of addresses that are near to a found address. The amount returned can be set using the `$options` array.

#### Parameters
<table>
    <thead>
        <tr>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Integer</td>
            <td>ID of the source address</td>
        </tr>
        <tr>
            <td>Array</td>
            <td>Options array, see table below</td>
        </tr>
        <tr>
            <td>Boolean</td>
            <td>Set to <code>true</code> to have the value returned instead of echoed.</td>
        </tr>
    </tbody>
</table>

#### Usage

```php
<?php
    $id = perch_get('address');
    
    root_locator_nearby($id, [
        'range' => 10,
        'count' => 3
    ]);
?>
```

### root\_locator\_get\_custom
Returns a custom query of the address data. Many of the functions above are shortcuts to options that can be configured using this function.

For more information on the available settings see [perch\_content\_custom](https://docs.grabaperch.com/functions/content/perch-content-custom/).

### Available Options
These are in addition to the ones listed for `perch_content_custom()`.

<table>
    <thead>
        <tr>
            <th>Option</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>coordinates</td>
            <td>An array containing latitude and longitide data.</td>
        </tr>
        <tr>
            <td>address</td>
            <td>An address to find nearby locations</td>
        </tr>
        <tr>
            <td>exclude</td>
            <td>To avoid returning the same record in a search you can exlude an ID from the results</td>
        </tr>
    </tbody>
</table>

---

## Locator Field Type

The locator field type can be used to include processed addresses in other Perch content types.

### Installation

Copy the entire `locator` directory to `perch/addons/fieldtypes`.

### Requirements

In order for the field type to work, you must have already installed and configured the main Locator app. This includes setting a Google API key.

### Usage

The locator field type works very similarly to the Pages field type. Including it within your templates will give you a list of geocoded addresses:

```html
<perch:content id="venue" type="locator" label="Venue" />
```
The example above will output the title of the address that is selected.

Additionally, using the `output` attribute, you can include other fields from the address:

```html
<perch:content id="venue" type="locator" label="Venue" output="title" />
<perch:content id="venue" type="locator" label="Venue" output="building" />
<perch:content id="venue" type="locator" label="Venue" output="street" />
<perch:content id="venue" type="locator" label="Venue" output="town" />
<perch:content id="venue" type="locator" label="Venue" output="region" />
<perch:content id="venue" type="locator" label="Venue" output="country" />
<perch:content id="venue" type="locator" label="Venue" output="postcode" />
<perch:content id="venue" type="locator" label="Venue" output="latitude" />
<perch:content id="venue" type="locator" label="Venue" output="longitude" />
```

---

## License

The MIT License (MIT)

Copyright (c) 2016 Root Studio

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
