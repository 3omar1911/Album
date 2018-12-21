<?php

namespace App\Services;

class AlbumGalleryManager {

	protected $files;
	protected $requestData;

	public function __construct($files = [], $requestData = []) {
		$this->files = $files;
		$this->requestData = $requestData;
	}

	public function setFiles($files) {
		$this->files = $files;
	}

	public function setRequestData($requestData) {
		$this->requestData = $requestData;
	}

	public function createGallery() {
	}
}