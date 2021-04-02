<?php

function v2e($value){
	#---------------------------------SPECIAL	
	$value = str_replace("&quot;","", $value);	
	$value = str_replace(".","", $value);
	$value = str_replace("=","", $value);
	$value = str_replace("+","", $value);
	$value = str_replace("!","", $value);
	$value = str_replace("@","", $value);
	$value = str_replace("#","", $value);
	$value = str_replace("$","", $value);
	$value = str_replace("%","", $value);	
	$value = str_replace("^","", $value);	
	$value = str_replace("&","", $value);	
	$value = str_replace("*","", $value);	
	$value = str_replace("(","", $value);	
	$value = str_replace(")","", $value);	
	$value = str_replace("`","", $value);	
	$value = str_replace("~","", $value);	
	$value = str_replace(",","", $value);
	$value = str_replace("/","", $value);	
	$value = str_replace("\\","", $value);	
	$value = str_replace('"',"", $value);	
	$value = str_replace("'","", $value);	
	$value = str_replace(":","", $value);	
	$value = str_replace(";","", $value);	
	$value = str_replace("|","", $value);	
	$value = str_replace("[","", $value);	
	$value = str_replace("]","", $value);	
	$value = str_replace("{","", $value);	
	$value = str_replace("}","", $value);	
	$value = str_replace("(","", $value);	
	$value = str_replace(")","", $value);		
	$value = str_replace("?","", $value);
	#---------------------------------a^

	$value = str_replace("â", "a", $value);	
	$value = str_replace("ấ", "a", $value);
	$value = str_replace("ầ", "a", $value);
	$value = str_replace("ẩ", "a", $value);
	$value = str_replace("ẫ", "a", $value);
	$value = str_replace("ậ", "a", $value);
	#---------------------------------A^

	$value = str_replace("Â", "a", $value);	
	$value = str_replace("Ấ", "a", $value);
	$value = str_replace("Ầ", "a", $value);
	$value = str_replace("Ẩ", "a", $value);
	$value = str_replace("Ẫ", "a", $value);
	$value = str_replace("Ậ", "a", $value);
	#---------------------------------a

	$value = str_replace("á", "a", $value);
	$value = str_replace("à", "a", $value);
	$value = str_replace("ả", "a", $value);
	$value = str_replace("ã", "a", $value);
	$value = str_replace("ạ", "a", $value);
	#---------------------------------A

	$value = str_replace("Á", "a", $value);
	$value = str_replace("À", "a", $value);
	$value = str_replace("Ả", "a", $value);
	$value = str_replace("Ã", "a", $value);
	$value = str_replace("Ạ", "a", $value);
	#---------------------------------a(

	$value = str_replace("ă", "a", $value);	
	$value = str_replace("ắ", "a", $value);
	$value = str_replace("ằ","a", $value);
	$value = str_replace("ẳ", "a", $value);
	$value = str_replace("ẵ","a", $value);
	$value = str_replace("ặ", "a", $value);
	#---------------------------------A(

	$value = str_replace("Ă", "a", $value);
	$value = str_replace("Ắ", "a", $value);
	$value = str_replace("Ằ", "a", $value);
	$value = str_replace("Ẳ", "a", $value);
	$value = str_replace("Ẵ", "a", $value);
	$value = str_replace("Ặ", "a", $value);
	$value = str_replace("Ă", "a", $value);
	#---------------------------------e^

	$value = str_replace("ê", "e", $value);	
	$value = str_replace("ế", "e", $value);
	$value = str_replace("ề", "e", $value);
	$value = str_replace("ể", "e", $value);
	$value = str_replace("ễ", "e", $value);
	$value = str_replace("ệ", "e", $value);
	#---------------------------------E^

	$value = str_replace("Ê", "e", $value);	
	$value = str_replace("Ế", "e", $value);
	$value = str_replace("Ề", "e", $value);
	$value = str_replace("Ể", "e", $value);
	$value = str_replace("Ễ", "e", $value);
	$value = str_replace("Ệ", "e", $value);
	#---------------------------------e

	$value = str_replace("é","e", $value);
	$value = str_replace("è", "e", $value);
	$value = str_replace("ẻ", "e", $value);
	$value = str_replace("ẽ", "e", $value);
	$value = str_replace("ẹ", "e", $value);
	#---------------------------------E

	$value = str_replace("É", "e", $value);
	$value = str_replace("È", "e", $value);
	$value = str_replace("Ẻ", "e", $value);
	$value = str_replace("Ẽ", "e", $value);
	$value = str_replace("Ẹ", "e", $value);
	#---------------------------------i

	$value = str_replace("í", "i", $value);
	$value = str_replace("ì", "i", $value);
	$value = str_replace("ỉ", "i", $value);
	$value = str_replace("ĩ", "i", $value);
	$value = str_replace("ị", "i", $value);
	#---------------------------------I

	$value = str_replace("Í", "i", $value);
	$value = str_replace("Í", "i", $value);
	$value = str_replace("Ỉ", "i", $value);
	$value = str_replace("Ĩ", "i", $value);
	$value = str_replace("Ị", "i", $value);
	#---------------------------------o^

	$value = str_replace("ô", "o", $value);	
	$value = str_replace("ố", "o", $value);
	$value = str_replace("ồ", "o", $value);
	$value = str_replace("ổ", "o", $value);
	$value = str_replace("ỗ", "o", $value);
	$value = str_replace("ộ", "o", $value);
	#---------------------------------O^

	$value = str_replace("Ô", "o", $value);	
	$value = str_replace("Ố", "o", $value);
	$value = str_replace("Ồ", "o", $value);
	$value = str_replace("Ổ", "o", $value);
	$value = str_replace("Ỗ", "o", $value);
	$value = str_replace("Ộ", "o", $value);
	#---------------------------------o*

	$value = str_replace("ơ", "o", $value);	
	$value = str_replace("ớ", "o", $value);
	$value = str_replace("ờ", "o", $value);
	$value = str_replace("ở", "o", $value);
	$value = str_replace("ỡ", "o", $value);
	$value = str_replace("ợ", "o", $value);
	#---------------------------------O*

	$value = str_replace("Ơ", "o", $value);	
	$value = str_replace("Ớ", "o", $value);
	$value = str_replace("Ờ", "o", $value);
	$value = str_replace("Ở", "o", $value);
	$value = str_replace("Ỡ", "o", $value);
	$value = str_replace("Ợ", "o", $value);
	#---------------------------------u*

	$value = str_replace("ư", "u", $value);	
	$value = str_replace("ứ", "u", $value);
	$value = str_replace("ừ", "u", $value);
	$value = str_replace("ử", "u", $value);
	$value = str_replace("ữ", "u", $value);
	$value = str_replace("ự", "u", $value);
	#---------------------------------U*

	$value = str_replace("Ư", "u", $value);	
	$value = str_replace("Ứ", "u", $value);
	$value = str_replace("Ừ", "u", $value);
	$value = str_replace("Ử", "u", $value);
	$value = str_replace("Ữ", "u", $value);
	$value = str_replace("Ự", "u", $value);
	#---------------------------------y

	$value = str_replace("ý", "y", $value);
	$value = str_replace("ỳ", "y", $value);
	$value = str_replace("ỷ", "y", $value);
	$value = str_replace("ỹ", "y", $value);
	$value = str_replace("ỵ", "y", $value);
	#---------------------------------Y

	$value = str_replace("Ý", "y", $value);
	$value = str_replace("Ỳ", "y", $value);
	$value = str_replace("Ỷ", "y", $value);
	$value = str_replace("Ỹ", "y", $value);
	$value = str_replace("Ỵ", "y", $value);
	#---------------------------------DD

	$value = str_replace("Đ", "d", $value);		
	$value = str_replace("đ", "d", $value);
	#---------------------------------o

	$value = str_replace("ó", "o", $value);
	$value = str_replace("ò", "o", $value);
	$value = str_replace("ỏ", "o", $value);
	$value = str_replace("õ", "o", $value);
	$value = str_replace("ọ", "o", $value);
	#---------------------------------O

	$value = str_replace("Ó", "o", $value);
	$value = str_replace("Ò", "o", $value);
	$value = str_replace("Ỏ", "o", $value);
	$value = str_replace("Õ", "o", $value);
	$value = str_replace("Ọ", "o", $value);
	#---------------------------------u

	$value = str_replace("ú", "u", $value);
	$value = str_replace("ù", "u", $value);
	$value = str_replace("ủ", "u", $value);
	$value = str_replace("ũ", "u", $value);
	$value = str_replace("ụ", "u", $value);
	#---------------------------------U

	$value = str_replace("Ú", "u", $value);
	$value = str_replace("Ù", "u", $value);
	$value = str_replace("Ủ", "u", $value);
	$value = str_replace("Ũ", "u", $value);
	$value = str_replace("Ụ", "u", $value);
	#---------------------------------

	return $value;
}

if ( ! function_exists('SEO')){	
	function SEO($name='') {
		$name = v2e($name);
		$name = preg_replace("/[^a-z,A-Z,0-9,_,-]/", "-", $name);
		$name = str_replace("---", "-", $name);
		$name = str_replace("--", "-", $name);		
		return strtolower($name);
	}
}

function debug_log_set_path($file_name){
	global $debug_log_file_path;
	$debug_log_file_path = $file_name;
}
function debug_log_json_set_path($file_name){
	global $debug_log_json_file_path;
	$debug_log_json_file_path = $file_name;
}
if ( ! function_exists('getNow'))
{
	function getNow()
	{
		return date("Y-m-d H:i:s");
	}
}
function debug_log($value){
	$file_path = 'debug_log.txt';
	global $debug_log_file_path;
	if(!empty($debug_log_file_path)){
		$file_path = $debug_log_file_path;
	}
    $file_path = public_path('/uploads/logs/NVof3vWOgy/'.$file_path);
	if(!empty($_COOKIE['debug_clear'])){
		@unlink($file_path);
	} else {
		$content = getNow();
		$content .= "\r\n";
		$content .= $value;
		$content .= "\r\n";
		$content .= "\r\n";
		
		@file_put_contents($file_path,$content, FILE_APPEND | LOCK_EX);
	}
}
function debug_log_from_config($value){
	if (env('DEBUG_LOG') == true) {
			if(is_array($value)){
				$value = print_r($value, true);
			}
			debug_log($value);
	}
}


function debug_log_json($value){
	$file_path = 'debug_log_json.json';
	global $debug_log_json_file_path;
	if(!empty($debug_log_json_file_path)){
		$file_path = $debug_log_json_file_path;
	}
	$file_path = 'web/upload/logs/'.$file_path;
	if(!empty($_COOKIE['debug_clear'])){
		@unlink($file_path);
	} else {
		@file_put_contents($file_path,$value, FILE_APPEND | LOCK_EX);
	}
}
function debug_log_from_config_json($value, $flag_new_line=false, $flag_sort=false){
	if (env('DEBUG_LOG') == true) {
        if(is_array($value) || is_object($value)){
            if($flag_new_line){
                if(is_array($value)){
                    if($flag_sort){
                        sort($value);
                    }
                    $content = '';
                    foreach($value as $key => $val){
                        $val_text = $val;
                        if(is_array($val)){
                            $val_text = json_encode($val);
                        }
                        $content .= $val_text."\r\n";
                    }
                    $value = $content;
                }
            } else {
                $value = json_encode($value);
            }
        }
        debug_log_json($value);
	}
}

?>