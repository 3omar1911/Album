<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class ImageHasName implements Rule
{
    private $request;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $imageIndex = $this->getImageIndex($attribute);

        if($imageIndex === false)
            return false;

        if(!$this->request->filled('image_name_'. $imageIndex))
            return false;

        return true;
    }

    /**
     * Get the image index
     *
     * @param  string  $imageName
     * @return integer
     */
    public function getImageIndex($imageName) {

        $params = explode('album_images.', $imageName);
        if(count($params) < 2)
            return false;

        return $params[1];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
