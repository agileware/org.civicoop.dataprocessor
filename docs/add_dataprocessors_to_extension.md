# Add a dataprocessor to your extension

Adding dataprocessors to an extension allows you to put them in version control and move them in a structured way from development to production. And dataprocessors can provide custom reports, custom tokens and custom action lists.

Dataprocessors creates a dependence on the  `dataprocessor` extension. Its good practice to add this to the `info.xml` file.

````xml
<requires>
  <ext>dataprocessor</ext>
</requires>
````

Create in your extension a subdirectory with the name `data-processors`. Export your dataprocessors and copy them to
this directory. Now they can be imported with the following code.

````php
CRM_Dataprocessor_Utils_Importer::importFromExtensions('<your extension>');
````

If you want the dataprocessors installed directly after the installation of the extension, use the `postInstall` method of the Upgrader class.

````php
 public function postInstall() {
    CRM_Dataprocessor_Utils_Importer::importFromExtensions(E::LONG_NAME);
  }
````
