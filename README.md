# AjaxMultiUpload Plugin for CakePHP


## How to Use

### Download or checkout

You can either download the ZIP file:
* https://github.com/srs81/CakePHP-AjaxMultiUpload/zipball/master

or checkout the code (leave the Password field blank):
* git clone https://srs81@github.com/srs81/CakePHP-AjaxMultiUpload.git

### Put it in the Plugin/ directory

Unzip or move the contents of this to "Plugin/AjaxMultiUpload" under
the app root.

### Add to controller 

Add to Controller/AppController.php for use in all controllers, or 
in just your specific controller where you will use it as below:

```php
var $helpers = array('AjaxMultiUpload.Upload');
```

### Add to views

Let's say you had a "companies" table with a "id" primary key.

Add this to your View/Companies/view.ctp:

```php
echo $this->Upload->view('Company', $company['Company']['id']);
```

and this to your View/Companies/edit.ctp:

```php
echo $this->Upload->edit('Company', $this->Form->fields['Company.id']);
```

### Create file directory

Make sure to create the correct files upload directory:
<pre>
cd cake-app-root
mkdir webroot/files
chmod -R 777 webroot/files
</pre>

The default upload directory is "files" under /webroot - but this can
be changed (see FAQ below.) 

You don't have to give it a 777 permission - just make sure the web 
server user can write to this directory.


## FAQ

#### No database/table schema changes?

No. :) Just drop this plugin in the right Plugin/ directory and add 
the code to the controller and views. Make sure the "files" directory
under webroot is writable, otherwise uploads will fail.

No tables/database changes are needed since the plugin uses a directory
structure based on the model name and id to save the appropriate files
 for the model.
 
#### Change directory 

Are you stuck to the "files" directory? Nope.

Open up Config/bootstrap.php under the Plugin/AjaxMultiUpload directory 
and change the "AMU.directory" setting. 

The directory will live under the app webroot directory - this is
as per CakePHP conventions.

#### Change directory paths


## Future Work

* Deleting files is not supported in this version.

## Thanks

This uses the Ajax Upload script from: http://valums.com/ajax-upload/
and file icons from: http://www.splitbrain.org/projects/file_icons

## Support


