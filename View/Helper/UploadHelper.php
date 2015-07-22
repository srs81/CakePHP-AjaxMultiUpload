<?php 
/**
 *
 * Dual-licensed under the GNU GPL v3 and the MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Suman (srs81 @ GitHub)
 * @package       plugin
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *                and/or GNU GPL v3 (http://www.gnu.org/copyleft/gpl.html)
 */
 
class UploadHelper extends AppHelper {

	public function view ($model, $id, $edit=false) {
		$results = $this->listing ($model, $id);
				
		$directory = $results['directory'];
		$baseUrl = $results['baseUrl'];
		$files = $results['files'];

        if (sizeof($files) == 0) {
        	return "";
        }

        $allIconFiles = array();
        foreach (glob(ROOT . DS . "app/Plugin/AjaxMultiUpload/webroot/img/fileicons/*") as $iconFile) {
            $iconFile = pathinfo($iconFile, PATHINFO_FILENAME);
            $allIconFiles[] = str_replace(".png", "", $iconFile);
        }

        $str = "<dt>" . __("Files") . "</dt>\n";
        $str .= "<dd>\n";
		$webroot = Router::url("/") . "ajax_multi_upload";
		foreach ($files as $file) {
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$filesize = $this->format_bytes (filesize ($file));
			$f = basename($file);
			$url = $baseUrl . "/$f";
			if ($edit) {
				$baseEncFile = base64_encode ($file);
				$delUrl = "$webroot/uploads/delete/$baseEncFile/";			
				$str .= "<a href='$delUrl'><img src='" . Router::url("/") . 
					"ajax_multi_upload/img/delete.png' alt='Delete' /></a> ";
			}
            if (in_array($type, $allIconFiles)) {
                $str .= "<img src='" . Router::url("/") . "ajax_multi_upload/img/fileicons/$type.png' /> ";
            }
			$str .= "<a href='$url'>" . $f . "</a> ($filesize)";
			$str .= "<br />\n";
		}
		$str .= "</dd>\n"; 
		return $str;
	}

	public function listing ($model, $id) {
		$dir = Configure::read('AMU.directory');
		if (strlen($dir) < 1) $dir = "files";

		$lastDir = $this->last_dir ($model, $id);
		$directory = WWW_ROOT . DS . $dir . DS . $lastDir;
		$baseUrl = Router::url("/") . $dir . "/" . $lastDir;
		$files = glob ("$directory/*");
		return array("baseUrl" => $baseUrl, "directory" => $directory, "files" => $files);
	}

	public function edit ($model, $id, $acceptedFiles=null) {
		$dir = Configure::read('AMU.directory');
		if ($dir === "") $dir = "files";
        $size = Configure::read ('AMU.filesizeMB');
        if ($size === "") $size = 4;

		$str = $this->view ($model, $id, true);
		$webroot = Router::url("/") . "ajax_multi_upload";
		// Replace / with underscores for Ajax controller
		$lastDir = str_replace ("/", "___", $this->last_dir ($model, $id));
		if ($acceptedFiles == null) {
			$acceptedFilesStr = "";
		} else {
			$acceptedFilesStr = "acceptedFiles: \"$acceptedFiles\"";
		}
		$str .= <<<END
			<script src="$webroot/js/dropzone.js" type="text/javascript"></script>
		    <script type="text/javascript">
		        Dropzone.options = {
                    maxFilesize: $size, 
                    $acceptedFilesStr
		        }
		    </script>
			<link rel="stylesheet" type="text/css" href="$webroot/css/dropzone.css" />
			<form action='$webroot/uploads/upload/$lastDir/' class="dropzone" id="dropzone-$model-$id"></form>
END;
		return $str;
	}

	// The "last mile" of the directory path for where the files get uploaded
	public function last_dir ($model, $id) {
		return $model . "/" . $id;
	}

	// From http://php.net/manual/en/function.filesize.php
	public function format_bytes($size) {
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}
}
