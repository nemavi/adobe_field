# adobe_field
adobe_field
Adobe Field Module forked from gdoc_field: https://git.drupalcode.org/project/gdoc_field
Thanks to  gisle, jmarkel, Webbeh, ramblinwreck 

Maintainer:
Nemavi

Requires:
Drupal 10/11
Tested on 10 only
 INSTRUCTION:
 The root of folder is in modules/contrib/adobe_field
 Put your Adobe CLIENT ID in js/adobe.js instead of CLIENTID

License:
GPL (see LICENSE)
Description

The Adobe Field module adds a custom field type that allows users to preview Adobe Acrobat PDF files directly in a Drupal site using Adobe's embeddable iframe viewer. It supports up to 20 files and provides an improved file selection interface.

After adding an Adobe Field to a Drupal content type, the field's display can be configured under the "Manage Display" tab. This ensures that users can preview PDFs seamlessly within the Drupal environment.

Note:
Only publicly accessible files can be previewed, as Adobe’s viewer requires access to the file URL. This means the module may not function correctly on local development environments or servers behind firewalls.
