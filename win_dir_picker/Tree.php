<?php
error_reporting(E_ALL); // Error/Exception engine, always use E_ALL

ini_set('ignore_repeated_errors', TRUE); // always use TRUE

ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', 'error_log.txt'); // Logging file path

require_once('Dirs.php');
require_once('File.php');

class Tree
{

	private static $prefix_dir_back = 'dir_back###';
	
	private function __construct()
	{
		
	}
	
	public static function getBreadcrumbHtml()
	{
		$dirs = Dirs::getDirectory();
		$html = '<i class="bi-folder distance"></i>';
		$html .= '<span class="separator distance"> > </span>';
		if($dirs)
		{
			foreach($dirs as $key => $dir)
			{
				$html .= self::createHtmlBreadcrumb($key, $dir);
			}
		}
		
		return $html;
	}
	
	public static function getDirsHtml($dir)
	{
		$dirs = Dirs::getSubDirs($dir);
		$dir_current = Dirs::getCurrentDirectory();
		$dirs_in_depth = Dirs::getDirectory();

		// return dirs and paths in html
		$html = '';
		if($dirs)
		{
			$html .= '<input type="hidden" readonly id="path" value="'.$dir_current['path'].'"/>';
			// isSubDir			
			if(!empty($dirs_in_depth))
			{
				$dir_key_prev = array_key_last($dirs_in_depth) - 1;
				$html .= self::createHtml(self::$prefix_dir_back.$dir_key_prev); // back
			}
			
			foreach($dirs as $dir)
			{
				$html .= self::createHtml($dir);
			}
		}
		
		return $html;
	}
	
	public static function createHtml($dir)
	{
		// show list of new dirs tree in html
		if(strpos($dir, self::$prefix_dir_back) !== false){
			$dir_name = explode('###', $dir)[0];
			$dir = self::$prefix_dir_back.$dir;
			$is_dir_back = true;
		} else{
			$is_dir_back = false;
			$dir_name = $dir;
		}
		$html = '';
		if($dir || is_numeric($dir))
		{
			$html = '
				<div>
					<button type="button" class="btn btn-primary folder directory_list" style="padding: 5px" value="'.$dir.'">
						<i class="bi-folder"></i> 
					</button>
					'.$dir = ($is_dir_back) ? '' : ((strlen($dir) > 40) ? mb_substr($dir, 0, 40).'...' : $dir).'
				</div>
			';
		}
		
		return $html;
	}
	
	public static function createHtmlBreadcrumb($key, $dir)
	{
		$html = '';
		if($dir)
		{
			$html .= '
				<span>
					<button type="button" class="btn btn-light folder" style="padding: 5px" value="'.$key.'">
						'.((strlen($dir['name']) > 20) ? mb_substr($dir['name'], 0, 40).'...' : $dir['name']).' <span class="separator"> > </span>
					</button>
				</span>
			';
		}
		
		return $html;
	}
	
}