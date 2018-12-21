<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\ImageHasName;
use DB;
use App\Album;
use App\Image as ImageModel;
use App\Services\AlbumGalleryManager;

class AlbumsController extends Controller
{

	private $albumGalleryManager;

	public function __construct(AlbumGalleryManager $albumGalleryManager) {
		$this->albumGalleryManager = $albumGalleryManager;
	}

	/**
	 * Get the view to create new album
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		return view('albums.create');
	}

	/**
	 * Store New Album
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		$this->validateAlbum($request);
		try {

			DB::beginTransaction();
			$album = $this->createAlbum($request->all());
			$imagesNames = $this->createGallery($request, $album);
			$this->attachImagesToAlbum($album, $imagesNames);
			DB::commit();
		} catch (\Exception $e) {
			
			DB::rollback();
			session()->flash('alert', trans('albums.creation_failed'). $e->getMessage());
			return view('albums.create');
		}

		session()->flash('success', trans('albums.created_successfully'));
		return redirect()->route('albums.create');
	}

	public function createAlbum($data) {

		$album = Album::create([
			'name' => $data["album_name"],
		]);

		if(!$album)
			throw new \Exception(trans('albums.creation_failed'));

		return $album;
	}

	/**
	 * Create the images and attach them the the album
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Album  $album
	 * @return void
	 * @throws \Exception
	 */
	public function createGallery($request, $album) {

		$this->albumGalleryManager->setFiles($request->file('album_images'));
		$this->albumGalleryManager->setRequestData($request->all());

		$cropParameters = [
			[
				'width' => 300,
				'height' => 300,
				'x' => 0,
				'y' => 0,
			], [
				'width' => 400,
				'height' => 400,
				'x' => 0,
				'y' => 0,
			]
		];
		return $this->albumGalleryManager->createGallery($cropParameters);
	}

	/**
	 * Attach Images to Album
	 *
	 * @param  \App\Album $album
	 * @param  array  $imagesNames
	 */
	public function attachImagesToAlbum($album, $imagesNames) {

		foreach($imagesNames as $name) {

			$image = ImageModel::create([
				'image_name' => $name,
				'album_id' => $album->id,
			]);

			if(!$image) {
				throw new \Exception(trans('albums.error_occured'));
			}
		}
	}

	/**
	 * Validate the album
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 * @throws \Exception
	 */
	public function validateAlbum($request) {
		$rules = [
			'album_name' => 'required|string',
			'album_images.*' => ["required", "image", new ImageHasName($request)]
		];

		$this->validate($request, $rules);
	}
}
