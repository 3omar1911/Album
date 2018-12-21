@extends('layouts.app')

@section('additional-css')
    <link rel="stylesheet" href="{{ asset('css/create-album.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-danger {{$errors->any()? '': 'hide'}}" id="validation-errors">
                <ul>
                    {{-- Backend validation errors --}}
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="card">
                <div class="card-header">Create Album</div>

                <div class="card-body">

                    <form action="{{ route('albums.store') }}" method="post" enctype="multipart/form-data" id="album-form">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="album_name">{{ trans('albums.name') }}</label>
                            <input type="text" name="album_name" id="album_name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="album_images">{{ trans('albums.name') }}</label>
                            <input type="file" name="album_images[]" id="album_images" class="form-control" multiple>
                        </div>

                        <div class="form-group">
                            <button class="form-control btn btn-primary" name="submit-btn" id="submit-btn">{{ trans('albums.submit') }}</button>
                        </div>

                        <div class="images-container">
                        </div>
                        {{-- <img src="" alt="test" style="width: 200px; height: 200px; border: 1px solid;" id="test" class="col-md-2">
                        <img src="" alt="test" style="width: 200px; height: 200px; border: 1px solid;" id="test" class="col-md-2">
                        <img src="" alt="test" style="width: 200px; height: 200px; border: 1px solid;" id="test" class="col-md-2">
                        <img src="" alt="test" style="width: 200px; height: 200px; border: 1px solid;" id="test" class="col-md-2"> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/create-album.js') }}"></script>
@endsection