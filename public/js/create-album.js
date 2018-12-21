$(document).ready(function() {


	$('#album_images').change(function() {
		$('.images-container').html('');
		readURL(this);
	});

	$('#submit-btn').click(function(e) {

		e.preventDefault();
		$('#validation-errors').addClass('hide');
		$('#validation-errors ul').html('');
		var validationResult = validateAlbum();
		
		if(!validationResult) {
			scrollToValidationErrors();
		    return;
		}

		$('#album-form').submit();
	});

	function readURL(input) {

		if(!input.files)
			return;

		var files = input.files;
		for(var i = 0; i<files.length; i++) {

			var added = addGalleryImage(files[i], i);
			if(added == false) {
				showInvalidFileMessage();
				break;
			}

			$('#validation-errors').addClass('hide');
		}
	}

	function showInvalidFileMessage() {
		$('#validation-errors ul').html('<li>Only Images are allowed</li>');
		$('#validation-errors').removeClass('hide');
		scrollToValidationErrors();
		$('#album_images').val('');
		$('.images-container').html('');
	}

	/**
	 * Add an image to the gallery
	 *
	 * @param  object  image  the DOM element
	 * @param  integer  index
	 */
	function addGalleryImage(image, index) {
		if(!checkImageFile(image))
			return false;

		var indexStr = index.toString();
		var inputName = 'image_name_' + indexStr ;

		var html = '<div class="gallery-image">'
			+ '<div class="row">'
				+ '<img src="" ' + 'id="image_' + index + '" class="col-md-4"' + '/>'
				+ '<div class="col-md-4">'
					+ '<input type="text" placeholder="Enter Image Name" class="single-image-gallery-name" name="'
					+ inputName + '"'
					+ '/>'
				+ '</div>'
			+ '</div>'
			+ '</div>';

		$('.images-container').append(html);
		addImageSrc( image, $('#image_' + index) );
	}

	/**
	 * check that the file we are trying to attach is an actual image
	 * 
	 * @param  object  file
	 */
	function checkImageFile(file) {
		var type = file.type;

		if(!type.includes('image'))
			return false;
		return true;
	}

	/**
	 * Read the image parmaters
	 *
	 * @param  object  image
	 * @param  object  targetElement
	 */
	function addImageSrc(image, targetElement) {

		var imageSrc = "";

		var reader = new FileReader();

	    reader.onload = function(e) {
	    	targetElement.attr('src', e.target.result);
	    }
	    reader.readAsDataURL(image);
	}

	/**
	 * Validate the form
	 */
	function validateAlbum() {
		var valid = true;

		// validate the album title
		if($('#album_name').val() == "") {
			valid = false;
			$('#validation-errors ul').append('<li>Album must have a name</li>');
		}

		// validate the gallery
		if($('#album_images').val() == "") {
			valid = false;
			$('#validation-errors ul').append('<li>Add at least one image to the gallery</li>');
		} else {

			$(document).find('.single-image-gallery-name').each(function() {
				if($(this).val() == "") {
					valid = false;
					$('#validation-errors ul').append('<li>Make sure to name each image</li>');
					return false;
				}
			});
		}

		if(!valid) {
			$('#validation-errors').removeClass('hide');
			return false;
		}

		return true;
	}

	function scrollToValidationErrors() {
		$([document.documentElement, document.body]).animate({
	        scrollTop: $("#validation-errors").offset().top
	    }, 200);
	}
});