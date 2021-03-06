<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	protected $fillable = [
		'image_name',
		'album_id',
	];

	public function album() {
		return $this->belongsTo('App\Album');
	}

}