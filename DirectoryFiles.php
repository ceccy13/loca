<?php
class DirectoryFiles
{	
	private static $files_mp4 = [];
	private static $files_mp4_info = [];
	private static $years = [];
	
	public static function getFiles($path)
	{
		if(empty(self::$files_mp4))
		{
			if(true)
			{
				$current_directory = __DIR__.DIRECTORY_SEPARATOR;				
				$cmd = 'cd/ & cd '.$current_directory.$path.' & dir /s /b *.mp4 > '.$current_directory.'files.txt';
				$params = [
					'file_path' => 'files.cmd',
					'cmd' 		=> $cmd
				];
				File::write($params);
				unset($params);
				
				exec('SilentCMD & chcp 65001 & '.$current_directory.'files.cmd');
				
				$lines = file($current_directory.'files.txt');
		
				$files = [];
				$step = 0;
				foreach ($lines as $line_num => $line) {
					// Files path
					$file_path = trim(str_replace($current_directory, '', $line));
					//$real_file_path = substr_replace($file_path, ':', 1, 0);
					
					/* Files Info */
					
					// size in bytes
					$file_size = round(filesize($file_path)/1024/1024, 2);// size in MB
					
					// time of last access (Unix timestamp)
					$file_time_last_access =  date('d / M / Y H:i:s', fileatime($file_path));
					
					// file last changed
					$file_last_changed = date('d / M / Y H:i:s', filemtime($file_path));
				
					/* Files Info */
					$year = date('Y', filemtime($file_path));

					self::$years[] = $year;
					
					$years_search = isset($_REQUEST['years']) && !empty($_REQUEST['years']) ? in_array($year, $_REQUEST['years']) : true;
					if(!$years_search)
					{
						continue;
					}
								
					self::$files_mp4[] = $file_path;

					self::$files_mp4_info[$step]['file_size'] = '<b>Size:</b> '.@$file_size.' MB';
					self::$files_mp4_info[$step]['file_time_last_access'] = '<b>Last Played:</b> '.@$file_time_last_access;
					self::$files_mp4_info[$step]['file_last_changed'] = '<b>Last Changed:</b> '.@$file_last_changed;
					
					$step++;
				}													
			}
			else
			{
				$directory = new RecursiveDirectoryIterator($path);
				$iterator = new RecursiveIteratorIterator($directory);

				$step = 0;
				foreach ($iterator as $file) {
					/* Files Info */
					
					$file_path = $path.'/'.$file->getFilename();
					$stat = stat($file_path);
					
					// $stat[7] size in bytes
					$file_size = round($stat[7]/1024/1024, 2);// size in MB
					
					// time of last access (Unix timestamp)
					$file_time_last_access =  date('d / M / Y H:i:s', $stat[8]);
					
					// file last changed
					$file_last_changed = date('d / M / Y H:i:s', filemtime($file_path));
				
					/* Files Info */
					$year = date('Y', filemtime($file_path));

					self::$years[] = $year;
					
					$years_search = isset($_REQUEST['years']) && !empty($_REQUEST['years']) ? in_array($year, $_REQUEST['years']) : true;
					if($file->isDir() || $file->getExtension() != 'mp4' || !$years_search)
					{
						continue;
					}
					
					// self::$files_mp4[] = $file->getFilename();				
					self::$files_mp4[] = $file_path;

					self::$files_mp4_info[$step]['file_size'] = '<b>Size:</b> '.@$file_size.' MB';
					self::$files_mp4_info[$step]['file_time_last_access'] = '<b>Last Played:</b> '.@$file_time_last_access;
					self::$files_mp4_info[$step]['file_last_changed'] = '<b>Last Changed:</b> '.@$file_last_changed;
					
					$step++;
				}
			}
		}
		return self::$files_mp4;
	}
	
	public static function getFilesInfo()
	{
		return self::$files_mp4_info;
	}
	
	public static function getYears()
	{
		$years = array_unique(self::$years);
		sort($years);
		return $years;
	}
	
}
