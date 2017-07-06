<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CmsTypes\GoogleType;
use \Input;
use App\ContentObject;

class CmsType extends Model
{
    public static function processTypeWithDefault($properties, $input, $site_id, $method, $options, $initialData)
    {
        $tmp = array();
        $google_i = 0;

        foreach ($properties as $property) {
            $value = '';

            switch ($property->dataType->name) {
                case 'google_coordinate':

                    $google = new GoogleType();

                    if (!empty($input['latitude'][$google_i]) && !empty($input['longitude'][$google_i])) {
                        $google->setType($input['latitude'][$google_i], $input['longitude'][$google_i]);
                    } else {
                        $google->setType($initialData['latitude'][$google_i],
                            $initialData['longitude'][$google_i]);
                    }

                    $value = $google->getValue();
                    $google_i++;
                    break;
                case 'image':
                case 'images':

                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $arr_value = json_decode($input[$property->variable_name], true);
                    } else {
                        $arr_value = json_decode($initialData[$property->variable_name], true);
                    }

                    $t_value = array();
                    $value = array();
                    for ($i = 0; $i < sizeof($arr_value); $i++) {
                        $arr_image = array();
                        $arr_image['caption'] = '';
                        $arr_image['order'] = 100;
                        if (isset($input[$property->variable_name . '_order'][$i])) {
                            $arr_image['order'] = $input[$property->variable_name . '_order'][$i];
                        }
                        if (isset($input[$property->variable_name . '_caption'][$i])) {
                            $arr_image['caption'] = $input[$property->variable_name . '_caption'][$i];
                        }
                        if (isset($input[$property->variable_name . '_class'][$i])) {
                            $arr_image['class'] = $input[$property->variable_name . '_class'][$i];
                        }
                        $arr_image['title'] = '';
                        $arr_image['alt'] = '';
                        if (isset($input[$property->variable_name . '_title'][$i])) {
                            $arr_image['title'] = $input[$property->variable_name . '_title'][$i];
                        }
                        if (isset($input[$property->variable_name . '_alt'][$i])) {
                            $arr_image['alt'] = $input[$property->variable_name . '_alt'][$i];
                        }
                        $arr_image['image'] = $arr_value[$i];

                        $t_value[] = $arr_image;
                    }
                    $value['value'] = json_encode($t_value);
                    break;
                case 'file':
                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $value['value'] = $input[$property->variable_name];
                    } else {
                        $value['value'] = $initialData[$property->variable_name];
                    }
                    break;
                case 'singlepage':

                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $singlepage = $input[$property->variable_name];
                    } else {
                        $singlepage = $initialData[$property->variable_name];
                    }
                    $value['value'] = $singlepage[0];
                    break;
                case 'multiplepage':
                    $array = array();
                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $multiplepage = $input[$property->variable_name];
                        foreach ($multiplepage as $key => $value) {
                            $array[] = $value;
                        }
                    } elseif (isset($initialData[$property->variable_name])) {
                        $multiplepage = $initialData[$property->variable_name];
                        foreach ($multiplepage as $key => $value) {
                            $array[] = $value;
                        }
                    }
                    $value = array('value' => json_encode($array));
                    break;
                case 'checkbox':
                    $array = array();
                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $checkbox = $input[$property->variable_name];
                        foreach ($checkbox as $key => $value) {
                            $array[] = $value;
                        }
                    } else {
                        $checkbox = $initialData[$property->variable_name];
                        foreach ($checkbox as $key => $value) {
                            $array[] = $value;
                        }
                    }

                    $value = array('value' => json_encode($array));
                    break;
                case 'radio':
                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $value = array('value' => $input[$property->variable_name]);
                    } else if (isset($initialData[$property->variable_name]) && !empty($initialData[$property->variable_name])) {
                        $value = array('value' => $initialData[$property->variable_name]);
                    }
                    break;
                default:
                    if (isset($input[$property->variable_name]) && !empty($input[$property->variable_name])) {
                        $value = array('value' => $input[$property->variable_name]);
                    } else {
                        $value = array('value' => $initialData[$property->variable_name]);
                    }
                    break;
            }

            if ($options) {
                $tmp_options = CmsType::setOptions($options);
                $value = array_merge($value, $tmp_options);
            }
            $tmp[$property->id] = $value;
        }

        return $tmp;
    }

    public static function processType($properties, $input, $site_id = false, $method = false, $options = false, $initialData = [])
    {
        $tmp = array();
        $google_i = 0;
        if (empty($initialData)) {
            foreach ($properties as $property) {
                $value = '';

                switch ($property->dataType->name) {
                    case 'google_coordinate':
                        $google = new GoogleType();
                        $google->setType($input['latitude'][$google_i], $input['longitude'][$google_i]);
                        $value = $google->getValue();
                        $google_i++;
                        break;
                    case 'image':
                    case 'images':
                        $arr_value = json_decode($input[$property->variable_name], true);
                        $t_value = array();
                        $value = array();
                        for ($i = 0; $i < sizeof($arr_value); $i++) {
                            $arr_image = array();
                            $arr_image['caption'] = '';
                            $arr_image['order'] = 100;
                            if (isset($input[$property->variable_name . '_order'][$i])) {
                                $arr_image['order'] = $input[$property->variable_name . '_order'][$i];
                            }
                            if (isset($input[$property->variable_name . '_caption'][$i])) {
                                $arr_image['caption'] = $input[$property->variable_name . '_caption'][$i];
                            }
                            /**
                             * Add Title and Alt tag
                             *
                             * @edited maris
                             * @date   2015/11/20
                             */
                            $arr_image['title'] = '';
                            $arr_image['alt'] = '';
                            if (isset($input[$property->variable_name . '_title'][$i])) {
                                $arr_image['title'] = $input[$property->variable_name . '_title'][$i];
                            }
                            if (isset($input[$property->variable_name . '_alt'][$i])) {
                                $arr_image['alt'] = $input[$property->variable_name . '_alt'][$i];
                            }
                            /**
                             * Add class tag
                             * @edited by Piyaphong
                             * @date 2016/04/05
                             */
                            if (isset($input[$property->variable_name . '_class'][$i])) {
                                $arr_image['class'] = $input[$property->variable_name . '_class'][$i];
                            }
                            /**
                             * Add class link_url
                             * @edited by Piyaphong
                             * @date 2016/04/05
                             */
                            if (isset($input[$property->variable_name . '_link_url'][$i])) {
                                $arr_image['link_url'] = $input[$property->variable_name . '_link_url'][$i];
                            }
                            if (isset($input[$property->variable_name . '_position'][$i])) {
                                $arr_image['position'] = $input[$property->variable_name . '_position'][$i];
                            }
                            if (isset($input[$property->variable_name . '_video_url'][$i])) {
                                $arr_image['video_url'] = $input[$property->variable_name . '_video_url'][$i];
                            }
                            if (isset($input[$property->variable_name . '_detail'][$i])) {
                                $arr_image['detail'] = $input[$property->variable_name . '_detail'][$i];
                            }
                            $arr_image['image'] = $arr_value[$i];

                            $t_value[] = $arr_image;
                        }
                        $value['value'] = json_encode($t_value);
                        break;
                    /**
                     * New images data-type
                     *
                     * @edit Maris
                     * @date 2015/12/03
                     */
                    case 'new_images':
                        $arr_value = json_decode($input[$property->variable_name], true);
                        $t_value = array();
                        $value = array();
                        for ($i = 0; $i < sizeof($arr_value); $i++) {
                            $arr_image = array();
                            $arr_image['caption'] = '';
                            $arr_image['order'] = 100;
                            $arr_image['title'] = '';
                            $arr_image['alt'] = '';
                            if (isset($input[$property->variable_name . '_order'][$i])) {
                                $arr_image['order'] = $input[$property->variable_name . '_order'][$i];
                            }
                            if (isset($input[$property->variable_name . '_caption'][$i])) {
                                $arr_image['caption'] = $input[$property->variable_name . '_caption'][$i];
                            }
                            if (isset($input[$property->variable_name . '_title'][$i])) {
                                $arr_image['title'] = $input[$property->variable_name . '_title'][$i];
                            }
                            if (isset($input[$property->variable_name . '_alt'][$i])) {
                                $arr_image['alt'] = $input[$property->variable_name . '_alt'][$i];
                            }
                            $arr_image['image'] = $arr_value[$i];
                            $t_value[] = $arr_image;
                        }
                        $value['value'] = json_encode($t_value);

                        break;
                    case 'file':
                        /**
                         * Ignore File Type Rule because new upload file use string to store value
                         *
                         * @date   2016/01/08
                         * @author maris kheawsaad
                         */
//                    $upload = new Upload();
//                    $file_path = '';
//                    if ($method == '') {
//                        if (isset($input[$property->variable_name])) {
//                            $file_path = $upload->uploadFile($property->variable_name);
//                        }
//                    } else if ($method == "update") {
//
//                        if ($input['delete_' . $property->variable_name] == 1) {
//                            // unlink
//                            $file_path = \App\Helpers\CmsHelper::getDataByPropertyID($property->id, $options['content_id']);
//                            \File::delete($file_path);
//                            $file_path = '';
//                        } else {
//                            $file_path = \App\Helpers\CmsHelper::getDataByPropertyID($property->id, $options['content_id']);
//                            if (isset($input[$property->variable_name])) {
//                                $file_path = $upload->uploadFile($property->variable_name);
//                            }
//                        }
//                    } else {
//                        $file_path = '';
//                    }
//                    $value['value'] = $file_path;
                        $value['value'] = $input[$property->variable_name];
//                    dd($value);
                        break;
                    case 'singlepage':

                        if (isset($input[$property->variable_name])) {
                            $singlepage = $input[$property->variable_name];
                        }
                        $value['value'] = $singlepage[0];
                        break;
                    case 'multiplepage':
                        $array = array();
                        if (isset($input[$property->variable_name])) {
                            $multiplepage = array_shift($input[$property->variable_name]);
                            
                            foreach (json_decode($multiplepage) as $key => $value) {
                                $array[] = $value;
                            }
                        }
                        $value = array('value' => json_encode($array));
                        
                        break;
                    case 'checkbox':
                        $array = array();
                        if (isset($input[$property->variable_name])) {
                            $checkbox = $input[$property->variable_name];
                            foreach ($checkbox as $key => $value) {
                                $array[] = $value;
                            }
                        }
                        $value = array('value' => json_encode($array));
                        break;
                    case 'radio':
                        if (isset($input[$property->variable_name])) {
                            $value = array('value' => $input[$property->variable_name]);
                        } else {
                            $value = array('value' => '');
                        }
                        break;
                    default:
                        if (isset($input[$property->variable_name])) {
                            $value = array('value' => $input[$property->variable_name]);
                        } else {
                            $value = array('value' => '');
                        }
                        break;
                }

                if ($options) {
                    $tmp_options = CmsType::setOptions($options);
                    $value = array_merge($value, $tmp_options);
                }
                $tmp[$property->id] = $value;
            }
        } else {
            $tmp = self::processTypeWithDefault($properties, $input, $site_id, $method, $options, $initialData);
        }

        return $tmp;
    }

    public static function setOptions($options)
    {
        $return = array();
        foreach ($options as $key => $value) {
            $return[$key] = $value;
        }

        return $return;
    }

    public function UploadImagesAjax($input_name)
    {

        $input = Input::all();
        $upload = new Upload();
        $return = $upload->upload($input_name);

        return $return;
    }

    public function UploadFileByUrl($url)
    {
        $upload = new Upload();
        $upload->uploadCropImage($url);
    }
}

?>