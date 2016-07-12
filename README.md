# Perch Locator

Perch Locator is an app to plot places of interest on a map that can be searched using an address.

## Installation
Upload the `jw_locator` directory to `perch/addons/apps` and add `jw_locator` to your `config/apps.php` file.

Example:

	<?php
	    $apps_list = array(
	        'content', 
	        'categories',
	        'jw_locator'
	    );
	    
## Using the App
In the Perch admin panel find the 'locator' app using the apps menu. Here you can add single locations in the conventional way or alternatively upload a CSV file to import large quantities of locations in one go.

CSV imports are not reccommended for general client use, instead as a time-saving option for developers.

The app operates on a queue system to prevent Google blocking API requests and performance issues therefore setting up the scheduled tasks in Perch is essential.

<strike>A Google API key (Geocoding must be enabled) can be used to increase the number of calls that can be made.</strike>

**Update:** As of June 22nd, Google now requires that all mapping requests be made using an API Key. You can set an API Key in the settings panel of Perch.

## Displaying on a map
To display your location results onto a map, take a look at the example code in the `locations/index.php` file.

## Page Functions
### jw\_locator\_get\_custom

Locations can be queried in a similar way to [perch\_content\_custom](https://docs.grabaperch.com/docs/content/perch-content-custom/). There are additional parameters to search by address, for example:

	<?php jw_locator_get_custom(array(
	    'address' => 'Lincoln, UK',
	    'radius'  => 10
	)); ?>
	
For use with a mapping API, the output can be returned as JSON:

	<?php jw_locator_get_custom(array(
        'address' => 'LN1 3AU',
        'radius'  => 10,
        'json'    => true
    )); ?>
    
### jw\_locator\_location\_json

If existing location data has been returned using `skip-template`, it can be converted into a JSON format use this function.

## License

The MIT License (MIT)

Copyright (c) 2015 Root Studio

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
