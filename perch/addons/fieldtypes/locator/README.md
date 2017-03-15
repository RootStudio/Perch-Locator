# Locator Field Type

The locator field type can be used to include processed addresses in other Perch content types.

## Installation

Copy the entire `locator` directory to `perch/addons/fieldtypes`.

## Requirements

In order for the field type to work, you must have already installed and configured the main Locator app. This includes setting a Google API key.

## Usage

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