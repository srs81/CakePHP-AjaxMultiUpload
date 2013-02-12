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

		$str = "<dt>" . __("Files") . "</dt>\n<dd>";
		$count = 0;
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
			$str .= "<img src='" . Router::url("/") . "ajax_multi_upload/img/fileicons/$type.png' /> ";
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

	public function edit ($model, $id) {
		$dir = Configure::read('AMU.directory');
		if (strlen($dir) < 1) $dir = "files";

		$str = $this->view ($model, $id, true);
		$webroot = Router::url("/") . "ajax_multi_upload";
		// Replace / with underscores for Ajax controller
		$lastDir = str_replace ("/", "___", 
			$this->last_dir ($model, $id));
		$str .= <<<END
			<br /><br />
			<link rel="stylesheet" type="text/css" href="$webroot/css/fileuploader.css" />
			<script src="$webroot/js/fileuploader.js" type="text/javascript"></script>
			<div class="AjaxMultiUpload$lastDir" name="AjaxMultiUpload">
				<noscript>
					 <p>Please enable JavaScript to use file uploader.</p>
				</noscript>
			</div>
			<script src="$webroot/js/fileuploader.js" type="text/javascript"></script>
			<script>
				if (typeof document.getElementsByClassName!='function') {
				    document.getElementsByClassName = function() {
				        var elms = document.getElementsByTagName('*');
				        var ei = new Array();
				        for (i=0;i<elms.length;i++) {
				            if (elms[i].getAttribute('class')) {
				                ecl = elms[i].getAttribute('class').split(' ');
				                for (j=0;j<ecl.length;j++) {
				                    if (ecl[j].toLowerCase() == arguments[0].toLowerCase()) {
				                        ei.push(elms[i]);
				                    }
				                }
				            } else if (elms[i].className) {
				                ecl = elms[i].className.split(' ');
				                for (j=0;j<ecl.length;j++) {
				                    if (ecl[j].toLowerCase() == arguments[0].toLowerCase()) {
				                        ei.push(elms[i]);
				                    }
				                }
				            }
				        }
				        return ei;
				    }
				}
				function createUploader(){
					var amuCollection = document.getElementsByClassName("AjaxMultiUpload$lastDir");
					for (var i = 0, max = amuCollection.length; i < max; i++) {
							action = amuCollection[i].className.replace('AjaxMultiUpload', '');
							window['uploader'+i] = new qq.FileUploader({
								element: amuCollection[i],
								action: '$webroot/uploads/upload/' + action + '/',
								debug: true
							});
						}
					}
				window.onload = createUploader;     
			</script>
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
