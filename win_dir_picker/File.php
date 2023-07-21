<?php

class file
{
	private static $dirs;
	
	public static function write($params)
	{
		$file = fopen($params['file_path'], "w") or die("Unable to open file!");
		fwrite($file, '@echo off'.PHP_EOL);
		fwrite($file, $params['cmd'].PHP_EOL);
		fwrite($file, 'cls'.PHP_EOL);
		fclose($file);
	}
	
	public static function read($params)
	{
		$file = fopen($params['file_path'], "r") or die("Unable to open file!");
		if(filesize(realpath($params['file_path'])) > 0)
		{
			self::$dirs = fread($file, filesize($params['file_path']));
		}
		fclose($file);
		
		return self::$dirs;
	}
}