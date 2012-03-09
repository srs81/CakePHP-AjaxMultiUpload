<?php 

class UploadHelper extends AppHelper {


	public function view ($model, $id) {
		
		require_once (CORE_PATH . "/Plugin/AjaxMultiUpload/Config/bootstrap.php");
		$dir = Configure::read('AMU.directory');
		if (strlen($dir) < 1) $dir = "files";

		$lastDir = $this->last_dir ($model, $id);
		$directory = WWW_ROOT . DS . $dir . DS . $lastDir;
		$baseUrl = Router::url("/") . $dir . DS . $lastDir;
		$files = glob ("$directory/*");
		$str = "<dt>" . __("Files") . "</dt>\n<dd>";
		$count = 0;
		foreach ($files as $file) {
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$str .= "<img src='" . Router::url("/") . "ajax_multi_upload/img/fileicons/$type.png' /> ";
			$filesize = $this->format_bytes (filesize ($file));
			$file = basename($file);
			$url = $baseUrl . "/$file";
			$str .= "<a href='$url'>" . $file. "</a> ($filesize)";
			$str .= "<br />\n";
		}
		$str .= "</dd>\n"; 
		return $str;
	}

	public function edit ($model, $id) {
		require_once (CORE_PATH . "/Plugin/AjaxMultiUpload/Config/bootstrap.php");
		$dir = Configure::read('AMU.directory');
		if (strlen($dir) < 1) $dir = "files";

		$str = $this->view ($model, $id);
		$webroot = Router::url("/") . "ajax_multi_upload";
		// Replace / with underscores for Ajax controller
		$lastDir = str_replace ("/", "___", 
			$this->last_dir ($model, $id));
$str .= <<<END
    <link rel="stylesheet" type="text/css" href="$webroot/css/fileuploader.css" />
    <script src="$webroot/js/fileuploader.js" type="text/javascript"></script>
    <div id="AjaxMultiUpload">
        <noscript>
             <p>Please enable JavaScript to use file uploader.</p>
        </noscript>
    </div>
    <script src="$webroot/js/fileuploader.js" type="text/javascript"></script>
    <script>        
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('AjaxMultiUpload'),
                action: '$webroot/uploads/upload/$lastDir/',
                debug: true
            });           
        }
        window.onload = createUploader;     
    </script>
END;
		return $str;
	}

	// Function to create the "last" set of directories for uploading
	function last_dir ($model, $id) {
		return $model . "/" . $id;
	}

	// From http://php.net/manual/en/function.filesize.php
	function format_bytes($size) {
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}
}
