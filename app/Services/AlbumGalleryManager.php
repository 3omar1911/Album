<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

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

	/**
	 * Create the images and get their names
	 *
	 * @return array
	 */
	public function createGallery($cropParameters) {

		
		$index = 0;
		$fileNames = [];
		foreach($this->files as $file) {
			$this->checkSizes($file, $cropParameters);
			$originalFile = $this->createOriginalFile($file, $index);
			$this->createCroppedVersions($file, $originalFile, $cropParameters);
			$fileNames[] = $originalFile['name'];
			$index++;
		}

		return $fileNames;
	}

	/**
	 * Create Original File
	 * 
	 * @param  Object  $file
	 * @param  integer  $index
	 * @return array
	 */
	public function createOriginalFile($file, $index) {

		if(!isset($this->requestData['image_name_'. $index])) {
			throw new \Exception(trans('albums.error_occured'));
		}

		$requestFileName = $this->requestData['image_name_'. $index];
		$fileExtenstion = $file->getClientOriginalExtension();
		$fileName = $requestFileName. date('Y-m-d h-i-s', time() + $index). '.'. $fileExtenstion;
		$path = Storage::putFileAs(
		    'originals', $file, $fileName
		);

		return [
			'name' => $fileName,
			'path' => $path,
		];
	}

	/**
	 * Create cropped versions of the original file
	 *
	 * @param  object  $file
	 * @param  array  $fileParameters 
	 * @param  array  $cropParameters
	 * @return void
	 */
	public function createCroppedVersions($file, $fileParameters, $cropParameters) {
		
		$functions = $this->getCroppingFunctionName($file);
		$create = $functions['create_original'];
		$cropped = $functions['create_crop'];

		$image = $create(Storage::path($fileParameters['path']));

		if(!$image) {
			throw new \Exception(trans('albums.error_occured'));
		}

		foreach($cropParameters as $param) {
			$croppedImage = imagecrop($image, ['x' => $param['x'], 'y' => $param['y'], 'width' => $param['width'], 'height' => $param['height']]);

			if(!file_exists(Storage::path($param['width']. 'x'. $param['height']))) {
				Storage::makedirectory($param['width']. 'x'. $param['height']);
			}

			// save the cropped version
			$croppedPath = '../storage/app/' . $param['width']. 'x'. $param['height']. '/'. $fileParameters['name'];
			$cropped($croppedImage, $croppedPath);
		}
	}

	/**
	 * Get the function name we should use to crop and the function to reaad the image
	 * 
	 * @param  object  $file
	 * @return array
	 * @throws  \Exception
	 */
	public function getCroppingFunctionName($file) {

		$mimeType = $file->getMimeType();
		$params = explode('/', $mimeType);

		if(count($params) < 2) {
			throw new \Exception(trans('albums.cropping_failed', [
				'type' => $mimeType,
			]));
		}

		$type = $params[count($params)-1];

		$createImageFunction = 'imagecreatefrom'. $type;
		$createCroppedImageFunction = 'image'. $type;
		if(!function_exists($createImageFunction) || !function_exists($createCroppedImageFunction)) {
			throw new \Exception(trans('albums.cropping_failed', [
				'type' => $mimeType,
			]));
		}

		return [
			'create_original' => $createImageFunction,
			'create_crop' => $createCroppedImageFunction,
		];
	}

	public function checkSizes($file, $cropParameters) {

		$size = getimagesize($file);

		foreach($cropParameters as $param) {

			if($param['width'] >= $size[0] || $param['height'] >= $size[1]) {
				throw new \Exception("All files must have a minimum width and height of 400");
			}
		}
	}
}