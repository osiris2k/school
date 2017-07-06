<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Media;

class Upload extends Model
{

    public $path;

    function upload($input_name)
    {
        $input = \Input::file($input_name);

        $files = [];
        if (!is_array($input)) {
            $files[0] = $input;
        } else {
            $files = $input;
        }
        $ret_files = [];
        foreach ($files as $file) {

            if (isset($file)) {
                $extenstion = $file->getClientOriginalExtension();
                $path = $this->getUploadPath();
                $file_name = strtolower(str_random(32) . '.' . $extenstion);
                while (file_exists($path . $file_name)) {
                    $file_name = strtolower(str_random(32) . '.' . $extenstion);
                }

                $this->createThumbnailSize($path . 'thumbnail_' . $file_name, $file, $extenstion);
                $this->createFullSize($path . $file_name, $file, $extenstion);
                $ret_files[] = $path . $file_name;
            }
        }
        $return = '';
        if (!is_array($input)) {
            $return = $ret_files[0];
        } else {
            $return = json_encode($ret_files);
        }

        return $return;
    }

    function uploadFile($input_name)
    {
        $input = \Input::file($input_name);

        $files = [];
        if (!is_array($input)) {
            $files[0] = $input;
        } else {
            $files = $input;
        }
        $ret_files = [];
        foreach ($files as $file) {
            if (isset($file)) {
                $extenstion = $file->getClientOriginalExtension();
                $path = $this->getUploadPath();
                $file_name = $file->getClientOriginalName();
                $file_name = explode('.', $file_name);
                unset($file_name[sizeof($file_name) - 1]);
                $file_name = implode('.', $file_name);
                // echo $path.$file_name.'.'.$extenstion;
                $file_name = \App\Helpers\CmsHelper::createSlug($file_name);
                while (file_exists($path . $file_name . '.' . $extenstion)) {
                    $file_name = strtolower($file_name . '_' . strtolower(str_random(4)));
                }
                $uploadSuccess = $file->move($path, $file_name . '.' . $extenstion);
                $ret_files[] = $path . strtolower($file_name . '.' . $extenstion);
            }
        }
        $return = '';
        if (!is_array($input)) {
            $return = $ret_files[0];
        } else {
            $return = json_encode($ret_files);
        }

        return $return;
    }

    public function getUploadPath()
    {
        $target_folder = env('UPLOAD_PATH');
        if (substr($target_folder, -1) != '/')
            $target_folder .= '/';

        //Check Year folder
        $target_folder .= date('Y-m/');
        if (!file_exists($target_folder)) {
            if (!is_dir($target_folder)) {
                mkdir($target_folder);
            }
        }

        return $target_folder;
    }

    function createThumbnailSize($destination_path, $file, $extenstion)
    {
        if (!is_null(env('SIZE_THUMBNAIL'))) {
            $width = \Image::make($file)->width();
            $height = \Image::make($file)->height();
            if ($width >= $height) {
                $resize_width = $width / env('SIZE_THUMBNAIL');
                $height = $height / $resize_width;
            } else {
                $resize_height = $height / env('SIZE_THUMBNAIL');
                $width = $width / $resize_height;
            }
            \Image::make($file)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destination_path);
        } else {
            $this->createFullSize($destination_path, $file, $extenstion, true);
        }
    }

    function createFullSize($destination_path, $file, $extenstion, $is_thumbnail = false, $input = false)
    {
        if (!$is_thumbnail) {
            $media = new Media();
            $media->path = $destination_path;
            $media->type = $extenstion;
            if ($input) {
                $media->width = $input['width'];
                $media->height = $input['height'];
                if ($input['width'] == "*") {
                    $media->width = \Image::make($file)->width() / (\Image::make($file)->height() / $input['width']);
                }
                if ($input['height'] == "*") {
                    $media->height = \Image::make($file)->height() / (\Image::make($file)->width() / $input['width']);
                }
            } else {
                $media->width = \Image::make($file)->width();
                $media->height = \Image::make($file)->height();
            }
            $media->save();
        }
        if ($input) {
            \Image::make($file)->resize($media->width, $media->height)->save($destination_path);
        } else {
            /*
             *  If no input option don't decrease image quality by Image::make facade function
             *  just move it to upload path for preserve image quality
             */
            $fileName = last(explode('/', $destination_path));
            $file->move($this->getUploadPath(), $fileName);
        }
    }

    function deleteFile($file_path)
    {
        unlink($file_path);
        $file_name = substr($file_path, strrpos($file_path, '/'));
        $thumbnail_filename_path = 'thumbnail_' . $file_name . substr($file_path, 0, strrpos($file_path, '/'));
        unlink($thumbnail_filename_path);
    }
}

?>