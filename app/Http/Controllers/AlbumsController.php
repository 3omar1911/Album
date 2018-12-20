<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlbumsController extends Controller
{

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

		dd('store');
	}
}
