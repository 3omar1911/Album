<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\ImageHasName;
use DB;
use App\Album;
use App\Services\AlbumGalleryManager;

class AlbumsController extends Controller
{

	private $albumGalleryManager;

	public function __costruct(AlbumGalleryManager $albumGalleryManager) {
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
			$this->createGallery($request, $album);
			DB::commit();
		} catch (\Exception $e) {
			
			DB::rollback();
			session()->flash('alert', $e->getMessage());
			return view('albums.create');
		}
	}

	public function createAlbum($data) {

		$album = Album::create([
			'name' => "album_name",
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
		$this->albumGalleryManager->createGallery();
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
