<?php namespace App\Helpers;

use stdClass;

/**
 * Class ImagesHelper
 *
 * @package App\Helpers
 */
class ImagesHelper
{
    /**
     * Get Image , Images data type resource
     *
     * @param array $imageArray
     * 
     * @return \stdClass
     */
    public static function getImageResource($imageArray = [])
    {
        $imgObj = new stdClass();
        
        if (count($imageArray) > 0) {
            $imgObj->image = $imageArray[0]->image;
            $imgObj->caption = $imageArray[0]->caption;
            $imgObj->title = $imageArray[0]->title;
            $imgObj->alt = $imageArray[0]->alt;
            $imgObj->class = $imageArray[0]->class;
            $imgObj->media_code = $imageArray[0]->media_code;
        }

        return $imgObj;
    }

    public static function createImageObjectFromArray($imageArray) {
		$imgObj = new stdClass();

		if (count($imageArray) > 0) {
			$imgObj->image = isset($imageArray["image"]) ? $imageArray["image"] : "";
			$imgObj->caption = isset($imageArray["caption"]) ? $imageArray["caption"] : "";
			$imgObj->title = isset($imageArray["title"]) ? $imageArray["title"] : "";
			$imgObj->alt = isset($imageArray["alt"]) ? $imageArray["alt"] : "";
			$imgObj->class = isset($imageArray["class"]) ? $imageArray["class"] : "";
			$imgObj->media_code = isset($imageArray["media_code"]) ? $imageArray["media_code"] : "";
		}

		return $imgObj;
	}
}