<?php
/** 
 * 将未知编码的字符串转换为期望的编码（配置文件中设置的编码） 
 * @param string $str 
 * @param string||null $toEncoding 
 * @return string 
 */  
function convertStr(string $str, ?string $toEncode = 'utf-8') : string
{  
    $strCode = mb_detect_encoding($str);  

    if (strtolower($strCode) != strtolower($toEncode)) {  
        $str = mb_convert_encoding($str, $toEncode, $strCode);  
    }  
    return $str;  
} 