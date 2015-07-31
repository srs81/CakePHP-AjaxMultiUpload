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
 
class UploadsController extends AjaxMultiUploadAppController {

	public $name = "Upload";

	public function isAuthorized() {
		return true;
	}

	public function beforeFilter() {
		$this->Auth->allow(array('upload','delete'));
	}
	
	public function upload($dir=null) {
		// max file size in bytes
		$size = Configure::read ('AMU.filesizeMB');
		if ($size === "") $size = 4;
		$relPath = Configure::read ('AMU.directory');
		if ($relPath === "") $relPath = "files";

		$sizeLimit = $size * 1024 * 1024;
        $this->layout = "ajax";
        Configure::write('debug', 0);
		$directory = WWW_ROOT . DS . $relPath;
        $result = array();
 
		if ($dir === null) {
			$result = array("error" => "Upload controller was passed a null value.");
		}
		// Replace underscores delimiter with slash
		$dir = str_replace ("___", "/", $dir);
		$dir = $directory . DS . "$dir/";
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
        if (!is_writable($dir)){
            $result = array('error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.");
        }
        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $targetPath = $dir;
            $targetFile = $targetPath . $_FILES['file']['name'];
            $fileSize = filesize($tempFile);
            if ($this->endsWith($targetFile, ".php")) {
                $result = array('error' => 'You are not allowed to upload PHP files for security reasons.');
            }
            if ($fileSize > $sizeLimit) {
                $result = array('error' => 'File is too large. Please ask server admin to increase the file upload limit.');
            }
            if (sizeof($result) == 0) {
                move_uploaded_file($tempFile, $targetFile);
            }
        } else {
            $result = array('error' => 'No files were uploaded.');
        }

        if (sizeof($result) > 0) {
        	// There was an issue with the upload
            $this->response->statusCode(400);
        } else {
        	// The upload was a success
        	$result = array('ok' => 'upload success');
        	$this->response->statusCode(200);
        }
        $this->response->type('json');
        $this->set("result", htmlspecialchars(json_encode($result), ENT_NOQUOTES));
	}

	/**
	 * delete a file
	 * Thanks to traedamatic @ github
	 */
	public function delete($file = null) {
		if(is_null($file)) {
			$this->Session->setFlash(__('File parameter is missing'));
			$this->redirect($this->referer());
		}
		$file = base64_decode($file);
		if(file_exists($file)) {
			if(unlink($file)) {
				$this->Session->setFlash(__('File deleted!'));				
			} else {
				$this->Session->setFlash(__('Unable to delete File'));					
			}
		} else {
			$this->Session->setFlash(__('File does not exist!'));					
		}
		
		$this->redirect($this->referer());	
	}

    // From http://stackoverflow.com/a/10473026/2033901
    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }
}

?>
