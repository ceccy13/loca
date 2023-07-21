<?php

class Dirs{
	
	private static $dirs = array('A', 'B', 'C:', 'D:', 'E:', 'F', 'G', 'H');
	private static $search_string = '###';
		
	private static function getDrives()
	{
		foreach(self::$dirs as $dir)
		{
			$dir = shell_exec('IF EXIST '.$dir.' (echo '.$dir.')');

			// if drive exist and have no symlink dir for it

			$symlink_drive = str_replace(':', '', $dir);
			if(strlen($dir) > 0 && !is_dir('../'.$symlink_drive))
			{
				// error_log('mklink /d '.__DIR__.'\\'.$symlink_drive.' '.$dir);
				shell_exec('RUNAS /savecred /user:administrator mklink /d '.__DIR__.'\\'.$symlink_drive.' '.$dir);
			}

			
			$dir = trim($dir);
			$dirs[] = $dir;
		}
		
		return $dirs;
	}
	
	public static function setDirectory($dir, $path)
	{
		$exist = false;
		if(isset($_SESSION['dir']))
		{
			foreach($_SESSION['dir'] as $key => $arr)
			{
				if($arr['name'] == $dir)
				{
					$_SESSION['dir'][$key] = [
						'name' => $dir,
						'path' => $path
					];
					$exist = true;
				}
			}
		}
		
		if(!$exist)
		{
			$_SESSION['dir'][] = [
				'name' => $dir,
				'path' => $path
			];
		}
	}
	
	public static function getDirectory()
	{
		$dir = isset($_SESSION['dir']) ? $_SESSION['dir'] : '';
		return $dir;
	}
	
	public static function getCurrentDirectory()
	{
		if(isset($_SESSION['dir'][0]))
		{
			$dir['name'] = $_SESSION['dir'][array_key_last($_SESSION['dir'])]['name'];
			$dir['path'] = $_SESSION['dir'][array_key_last($_SESSION['dir'])]['path'];
		}
		else
		{
			$dir['name'] = '';
			$dir['path'] = '';
		}
		return $dir;
	}
	
	public static function getPreviosDirectory()
	{
		if(isset($_SESSION['dir']) && array_key_last($_SESSION['dir']) > 0)
		{
			$dir['name'] = $_SESSION['dir'][array_key_last($_SESSION['dir']) - 1]['name'];
			$dir['path'] = $_SESSION['dir'][array_key_last($_SESSION['dir']) - 1]['path'];
		}
		else
		{
			$dir['name'] = '';
			$dir['path'] = '';
		}
		return $dir;
	}

	public static function getBreadCrumbDirectory($dir_selected)
	{
		$dir = [];
		if(isset($_SESSION['dir']) && array_key_last($_SESSION['dir']) > 0)
		{
			$dir['name'] = $_SESSION['dir'][$dir_selected]['name'];
			$dir['path'] = $_SESSION['dir'][$dir_selected]['path'];
		}
		else
		{
			$dir['name'] = '';
			$dir['path'] = '';
		}
		return $dir;
	}
	
	public static function getCurrentDrive()
	{
		$dir = (isset($_SESSION['dir'][0])) ? $_SESSION['dir'][0]['name'] : '';
		return $dir;
	}
	
	public static function destroyLastDirectory()
	{
		unset($_SESSION['dir'][array_key_last($_SESSION['dir'])]);
	}
	
	public static function destroyDirs($dir_key)
	{
		$dirs_in_depth = self::getDirectory();
		$dirs_in_depth = array_slice($dirs_in_depth, 0, $dir_key + 1);
		unset($_SESSION['dir']);
		$_SESSION['dir'] = $dirs_in_depth;
	}
	
	public static function getSubDirs($dir)
	{
		// prepare list of new dirs
		// check if current post dir is request to go back
		$dirs = [];
		$is_dir_back = false;
		$is_dir_bread_crumb = false;
		if(strpos($dir, self::$search_string) !== false){
			$is_dir_back = true;
			$dir = explode('###', $dir)[1];
		}

		// TODO REFACTORING
		if($is_dir_back)
		{	// TODO
			self::destroyDirs($dir);
			$current_dir = self::getCurrentDirectory();
			$dir = $current_dir['name'];
		}
		elseif($dir && is_numeric($dir) && $dir > 0)
		{   // TODO
			self::destroyDirs($dir);
			$current_dir = self::getBreadCrumbDirectory($dir);
			$dir = $current_dir['name'];
			$is_dir_bread_crumb = true;
		}
		
		// TODO REFACTORING
		if(!$dir)
		{	// TODO
			session_destroy();
			$_SESSION = array();
			$dirs = self::getDrives();
		}
		else
		{
			$current_dir = self::getCurrentDirectory();

			$is_dir = $current_dir['name'] ? true : false;
			$drive = self::getCurrentDrive() ? self::getCurrentDrive() : $dir;
			
			$path = $current_dir['path'];
			if(!$is_dir_back && !$is_dir_bread_crumb)
			{
				$path = $current_dir['path'].$dir.'/';
				self::setDirectory($dir, $path);
			}
			
			// END TODO
			
			// make cmd command in file dirs.cmd and run it to saved dirs in file dirs.txt
			// that will convert unicode from ASCII to UTF-8 of returned dirs data from command in cmd
			$add_cmd_1 ='cd/';
			$add_cmd_2 = ' & '.$drive;
			$add_cmd_3 = ($is_dir) ? ' & cd '.$path : '';
			$add_cmd_4 =' & dir /B /AD-h';
			$directory = dirname(realpath(__CLASS__.'.php'));
			//cmd command save cmd response to file
			$add_cmd_5 = ' > '.$directory.'/dirs.txt';
			$cmd = $add_cmd_1.$add_cmd_2.$add_cmd_3.$add_cmd_4.$add_cmd_5;

			// save cmd command to file dir.cmd
			$params = [
				'file_path' => $directory.'/dir.cmd',
				'cmd' 		=> $cmd
			];
			File::write($params);
			unset($params);
			
			$dirs = exec('cmd & chcp 65001 & '.$directory.'/dir.cmd');
			
			// read file dirs.txt
			$params = [
				'file_path' => 'dirs.txt',
			];
			$dirs = File::read($params);
			
			// put dirs content in array of dirs
			$dirs = preg_replace("/[\r\n]/", "#", $dirs);
			// remove the last character from any string
			$dirs = substr($dirs, 0, -1);	
			$dirs = explode('#', $dirs);

			// normalaize array of dirs in array of dirs
			if($dirs)
			{
				$dirs_real = [];
				foreach($dirs as $key => $dir)
				{
					if(empty($dir)) continue;
					unset($dirs[$key]);
					$dirs[] = trim($dir);					
				}				
			}
		}
		
		return $dirs;
	}
}