# AjaxMultiUpload Plugin for CakePHP

A full-blown AJAX file uploader plugin for CakePHP 2.0.x and 2.1.
Using this, you can add multiple file upload behaviour to any or all
of your models without having to modify the database or schema.

You can click on the Upload File button, or drag-and-drop files into 
it. You can upload multiple files at a time without having to click
on any button, and it shows you a nice progress notification during
uploads. You can also delete files in edit mode.

## How to Use

### Download or checkout

You can either download the ZIP file:
https://github.com/srs81/CakePHP-AjaxMultiUpload/zipball/master

or checkout the code (leave the Password field blank):

```
git clone https://srs81@github.com/srs81/CakePHP-AjaxMultiUpload.git
```

### Put it in the Plugin/ directory

Unzip or move the contents of this to "Plugin/AjaxMultiUpload" under
the app root.

### Add to bootstrap.php load

Open Config/bootstrap.php and add this line:

```php
CakePlugin::load('AjaxMultiUpload');
```

This will allow the plugin to load all the files that it needs.

### Create file directory

Make sure to create the correct files upload directory if it doesn't
exist already:
<pre>
cd cake-app-root
mkdir webroot/files
chmod -R 777 webroot/files
</pre>

The default upload directory is "files" under /webroot - but this can
be changed (see FAQ below.) 

You don't have to give it a 777 permission - just make sure the web 
server user can write to this directory.

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

## Some Gotchas

Thanks to rscherf@github for the following two fixes.

#### Using Auth

If you are using Auth (either the CakePHP core Auth or some of the
compatible or incompatible ones), you need to modify the controller
to allow uploads to work.

Add these lines to the UploadsController.php (you may have to modify
slightly depending on your Auth setup):
```php
function isAuthorized() {
    return true;
}

function beforeFilter() {
    $this->Auth->allow(array('upload','delete'));
}
```

#### Subdomain

If you are using a subdomain, you will have to set up the plugin
correctly to work (depending, again, on how you have your sub-domains
set up in your Apache/htaccess settings).

These are the changes to be made to routes.php:
```php
// AJAX Multi Upload plugin
Router::connect('/:subdomain/ajax_multi_upload/:controller', array('plugin' => 'ajax_multi_upload'), $ops);
Router::connect('/:subdomain/ajax_multi_upload/:controller/:action/*', array('plugin' => 'ajax_multi_upload'), $ops);
```

## FAQ

#### Dude! No database/table schema changes?

Nope. :) Just drop this plugin in the right Plugin/ directory and add 
the code to the controller and views. Make sure the "files" directory
under webroot is writable, otherwise uploads will fail.

No tables/database changes are needed since the plugin uses a directory
structure based on the model name and id to save the appropriate files
 for the model.

#### Help! I get file upload or file size error messages!

The default upload file size limit is set to a conservative 2 MB 
to make sure it works on all (including shared) hosting. To change 
this:

* Open up Plugin/AjaxMultipUpload/Config/bootstrap.php and change the
"AMU.filesizeMB" setting to whatever size in MB you like.
* Make sure to also change the upload size setting (
upload_max_filesize and post_max_size) in your PHP settings (
php.ini) and reboot the web server!

#### Change directory 

Are you stuck to the "files" directory under webroot? Nope.

Open up Config/bootstrap.php under the Plugin/AjaxMultiUpload directory 
and change the "AMU.directory" setting. 

The directory will live under the app webroot directory - this is
as per CakePHP conventions.

#### Change directory paths

Coming soon.

## ChangeLog

* Version 1.0.1 / April 2, 2012: Delete functionality added
* Version 1.0.0 / March 2012: Initial release

## Thanks

This uses the Ajax Upload script from: http://valums.com/ajax-upload/
and file icons from: http://www.splitbrain.org/projects/file_icons

## Support

If you find this plugin useful, please consider a [donation to Shen
Yun Performing Arts](https://www.shenyunperformingarts.org/support)
to support traditional and historic Chinese culture.


