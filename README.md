# Custom Alignment

A REDCap external module that provides custom alignment overrides for FORMs, SURVEYs, and PDFs

## Requirements

- REDCap with EM Framework v14

## Installation

Automatic installation:

- Install this module from the REDCap External Module Repository and enable it.

Manual installation:

- Clone this repo into `<redcap-root>/modules/redcap_custom_alignment_v<version-number>`.
- Go to _Control Center > Technical / Developer Tools > External Modules_ and enable 'Custom Alignment'.

## Configuration and Effects

- This module has no configuration options.
- Action tags set on fields allow to override their custom alignment setting. This can be done separately for when the fields are rendered on a **form**, a **survey**, or in a **PDF**.

## Action Tags

- `@CUSTOM-ALIGNMENT-FORM`
- `@CUSTOM-ALIGNMENT-SURVEY`
- `@CUSTOM-ALIGNMENT-PDF`

All three take a string as argument. The string must be one of `LH` (left horizontal), `LV` (left vertical), `RH` (right horizontal), or `RV` (right vertical). 

Example:
```js
@CUSTOM-ALIGNMENT-FORM="LV"
```

## Changelog

Version | Description
------- | --------------------
v1.0.0  | Initial release.
