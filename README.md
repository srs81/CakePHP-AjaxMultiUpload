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
CakePlugin::load('AjaxMultiUpload', array('bootstrap' => true));
```

This will allow the plugin to load all the files that it needs including it's own bootstrap.

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
public $components = array('Session', 'AjaxMultiUpload.Upload');
```
The component will load the required helper automatically so you
don't have to manually load it in your controllers.

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

### Custom listing of files

If you don't like the custom views that result from this->Upload->view(), you can use the listing() function to custom-list files, or use the file listing for other purposes (generating thumbnails, for instance). 

In your view, you can do this:

```php
$results = $this->Upload->listing ($model, $id);

$directory = $results['directory'];
$baseUrl = $results['baseUrl'];
$files = $results['files'];

foreach ($files as $file) {
	$f = basename($file);
	$url = $baseUrl . "/$f";
	echo "<a href='$url'>" . $f . "</a><br />\n";
}
```

and use the directory, baseUrl, and files data structures to display your files. Look at UploadHelper's view() function to see how the listing() function is used internally.

### Add to controllers 

Add the following to the delete() function of your Company controller where appropriate (either first line, or right after $this->Company->delete() check):
 
```php
echo $this->Upload->deleteAll('Company', $id);
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
public function isAuthorized() {
    return true;
}

public function beforeFilter() {
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

By default, the plugin stores files into /webroot/files/$model/$id . It is possible
to change the /files/ directory through the configuration setting mentioned above.
To change the /$model/$id/ path though (say you want to change it to md5($model . $id)),
look for this line in Controller/Component/UploadComponent.php AND View/Helper/UploadHelper.php:

```php
	public function last_dir ($model, $id) {
```

Change the function in both these files to do whatever you would like. Note that you have to make
the changes in BOTH files for this to work.

#### Multiple Uploads in same view/edit

It is now possible to have multiple view/edit functions in the same CakePHP view. For example, for a Photo controller, add this to your view.ctp:

```php
echo $this->Upload->view('Photo', "thumbs/" . $photo['Photo']['id']);
echo $this->Upload->view('Photo', "highres/" . $photo['Photo']['id']);
```

and this to your View/Photos/edit.ctp:

```php
echo $this->Upload->edit('Photo', "thumbs/" . $this->Form->fields['Photo.id']);
echo $this->Upload->edit('Photo', "highres/" . $this->Form->fields['Photo.id']);
```

This allows you to upload and two sets of files to your same entity/object in a controller/view.

## ChangeLog

* Version 1.0.3 / Jul 30 2012: multiple view/edit on same views possible (thanks to bobartlett@github)
* Version 1.0.2 / Jul 16 2012: deleteAll() and listing() functionality added
* Version 1.0.1 / Apr 02 2012: Delete functionality - from view() - added
* Version 1.0.0 / Mar 2012: Initial release

## Thanks

This uses the Ajax Upload script from: http://valums.com/ajax-upload/
and file icons from: http://www.splitbrain.org/projects/file_icons .

Also, thanks to contributions from the following GitHub users: 
* @rscherf : Getting it to work with Auth and sub-domains
* @bobartlett : Fix to allow multiple AMU helpers in same view

## Support

If you find this plugin useful, please consider a [donation to Shen
Yun Performing Arts](https://www.shenyunperformingarts.org/support)
to support traditional and historic Chinese culture.


