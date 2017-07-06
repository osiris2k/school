<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{

    public static function prepareRule($objs, $input)
    {
        $rule = array();
        foreach ($objs as $obj) {
            // check type if we want to split functions like ruleText(), ruleNumber()
            if ($obj['type'] != 'image' && $obj['type'] != 'images') {
                if ($obj['type'] != 'file') {
                    $txt_rule = '';
                    $json_option = json_decode($obj['option'], true);
                    if ($obj['is_mandatory'] == 1) {
                        $txt_rule = 'required|';

                        $i = 0;
                        foreach ($json_option as $the_rule) {
                            if ($the_rule['name'] !== 'target') {
                                $txt_rule .= $the_rule['name'] . ':' . $the_rule['value'][0];
                                $i++;
                                if (sizeof($json_option) != $i) {
                                    $txt_rule .= '|';
                                }
                            }

                        }

                        /** Match rules with content properties */
                        if (isset($input[$obj['variable_name']])) {
                            if (is_array($input[$obj['variable_name']])) {
                                $num = count($input[$obj['variable_name']]);
                                for ($i = 0; $i < $num; $i++) {
                                    $rule[$obj['variable_name'] . '.' . $i] = $txt_rule;
                                }
                            } else {
                                $rule[$obj['variable_name']] = $txt_rule;
                            }
                        } else {
                            // default
                            $rule[$obj['variable_name']] = $txt_rule;
                        }
                    }

                } else {
                    /**
                     * Ignore File Type Rule because new upload file use string to store value
                     *
                     * @date   2016/01/08
                     * @author maris kheawsaad
                     */
//					$txt_rule = '';
//					$json_option = json_decode($obj['option'],true);
//					if($obj['is_mandatory']==1)
//					{
//						$txt_rule = 'required|';
//					}
//					$i = 0;
//					foreach ($json_option as $the_rule)
//					{
//						$txt_rule .= $the_rule['name'].':'.$the_rule['value'][0];
//						$i++;
//						if(sizeof($json_option)!=$i)
//						{
//							$txt_rule .= '|';
//						}
//					}
//					// array
//					if(isset($input[$obj['variable_name']])){
//						if(is_array($input[$obj['variable_name']])){
//							$num = count($input[$obj['variable_name']]);
//							for ($i=0; $i < $num; $i++) {
//								$rule[$obj['variable_name'].'.'.$i] = $txt_rule;
//							}
//						}else{
//							$rule[$obj['variable_name']] = $txt_rule;
//						}
//					}else{
//						// default
//						$rule[$obj['variable_name']] = $txt_rule;
//					}
                }
            } else {
                // ajax prepare image rule
                // $json_option = json_decode($obj['option'],true);
                // $rule['image_rule'] = Rule::ruleImage($obj);
                // $rule['width'] = $json_option[0]['value'][0];
                // $rule['height'] = $json_option[1]['value'][0];
                // if($rule['height']=='*'&&$rule['width']=='*'){
                // 	$rule['ratio'] = 1;
                // }else if($rule['height']=='*'||$rule['width']=='*'){
                // 	$rule['ratio'] = 1;
                // }else{
                // 	$rule['ratio'] = $rule['width']/$rule['height'];
                // }
                // $rule['width'] = Rule::ruleImage($obj);
                // $rule['ratio'] = Rule::ruleImage($obj);
                // array
                // if(isset($input[$obj['variable_name']])){
                // 	if(is_array($input[$obj['variable_name']])){
                // 		$num = count($input[$obj['variable_name']]);
                // 		for ($i=0; $i < $num; $i++) {
                // 			$rule[$obj['variable_name'].'.'.$i] = Rule::ruleImage($obj);
                // 		}
                // 	}else{
                // 		$rule[$obj['variable_name']] = Rule::ruleImage($obj);
                // 	}
                // }else{
                // 	// default
                // 	$rule[$obj['variable_name']] = Rule::ruleImage($obj);
                // }
            }
        }

        return $rule;
    }

    public static function prepareRuleImage($objs, $input)
    {
        $rule = array();
        foreach ($objs as $obj) {
            // ajax prepare image rule
            $json_option = json_decode($obj['option'], true);
            $rule['image_rule'] = Rule::ruleImage($obj);
            $rule['width'] = $json_option[0]['value'][0];
            $rule['height'] = $json_option[1]['value'][0];
            if ($rule['height'] == '*' && $rule['width'] == '*') {
                $rule['ratio'] = 0;
            } else if ($rule['height'] == '*' || $rule['width'] == '*') {
                $rule['ratio'] = 0;
            } else {
                $rule['ratio'] = $rule['width'] / $rule['height'];
            }
        }

        return $rule;
    }

    public static function ruleImage($obj)
    {
        $txt_rule = '';
        $json_option = json_decode($obj['option'], true);
        if ($obj['is_mandatory'] == 1) {
            $txt_rule .= 'required|';
        }
        $i = 0;
        foreach ($json_option as $the_rule) {
            $i++;
            if ($the_rule['name'] != 'caption') {
                // height
                if ($the_rule['name'] == 'height') {
                    $txt_rule .= ',' . $the_rule['value'][0];
                } else if ($the_rule['name'] == 'width') {
                    $txt_rule .= 'image_size:' . $the_rule['value'][0];
                } else {
                    $txt_rule .= $the_rule['name'] . ':' . $the_rule['value'][0];
                }
                if (sizeof($json_option) != $i && $the_rule['name'] != 'width') {
                    $txt_rule .= '|';
                }
            }
        }

        return $txt_rule;
    }
}

?>