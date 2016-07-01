<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Csv类库
 *
 * @package utility
 */
class Util_Csv
{
	/**
	 * 输出csv头部数据
	 * @param string $filename
	 * @param string $charset [UTF-8|GB2312]
	 */
	public static function putHead($filename = 'csv-export', $charset="UTF-8"){
		header("Content-type:application/vnd.ms-excel; charset=" . $charset);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition:attachment; filename=\"" . $filename . ".csv\"");
		header("Content-Transfer-Encoding: binary ");
		//utf8添加BOM防止excel打开乱码
		echo "\xEF\xBB\xBF";
	}
	
	/**
	 * 输出csv中间数据
	 * @param array $array
	 */
	public static function putData($array, $delimiter = ','){
		$line = "";
		foreach ($array as $k => $v){
			$line = implode($delimiter, $v). "\n";
			echo $line;
		}
		ob_flush();
		flush();
	}
	
	/**
	 * 输出csv中间数据
	 * @param array $array
	 */
	public static function putSaveData($dir, $filename, $array, $delimiter = ','){
	    $line = "";
	    setlocale(LC_ALL, 'zh_CN GB2312');
	    header("Content-type:text/html;charset=gb2312");
	    foreach ($array as $k => $v){
	        $line = implode($delimiter, $v). "\n";
	        //$line = iconv("UTF-8", "GB2312//IGNORE", $line);
	        $line = iconv("UTF-8", "GBK", $line);
	        file_put_contents($dir.$filename.'.csv', $line, FILE_APPEND);
	        echo $line;
	    }
	    ob_flush();
	    flush();
	}
}