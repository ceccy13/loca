<?php
	session_start();
	require_once ('error_log.php');
	require_once ('DirectoryFiles.php');
	require_once('win_dir_picker/File.php');

	//chown('D:/Pics/Анабел', get_current_user());
	//echo get_current_user();  //Acer
	//echo exec('whoami');   //admin
	
	//$dir = 'files';
	$dir = isset($_COOKIE['dir_path']) ? $_COOKIE['dir_path'] : '';

	if($dir)
	{
		$files = DirectoryFiles::getFiles($dir);
		$files_info = DirectoryFiles::getFilesInfo();
		$short_files_list = array_slice($files, 0, 10);
	}

	$what_to_play = @$files[0];
	$key = 0;
	if(isset($_GET['v']))
	{
		$what_to_play = $_GET['v'];
		$key = array_search($what_to_play,$files);
	}

	$label = $what_to_play ? '<b>File: </b>' : '';

	//$pathinfo = pathinfo($what_to_play);

	$years = DirectoryFiles::getYears();

	$session = isset($_SESSION['years']) ? $_SESSION['years'] : array();
	$request = isset($_REQUEST['years']) ? $_REQUEST['years'] : array();
	if(($request && !$session) || ($request && array_diff(array_map('serialize', $session), array_map('serialize', $request))))
	{
		unset($_SESSION);
		$_SESSION['years'] = $request;
	}
				
	if(empty($request))
	{
		//$_SESSION['years'] = $years;
		//echo print_r($_SESSION, true);
	}
	
	// picker
	$directory = 'win_dir_picker/';
	// picker

?>

<!DOCTYPE html>
<html>
<head>
	<title>LocalTube mp4</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	-->

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

	<link href="https://vjs.zencdn.net/7.19.2/video-js.css" rel="stylesheet" />

	<link rel="stylesheet" href="style.css">

	<!-- cookie js -->
	<script src="cookies.js"></script>
	<!-- cookie js -->

	<script>
		//php array to js array
		var files_of_dir = <?= json_encode((object)$files)?>;
		var files_info = <?= json_encode((object)$files_info)?>;
	</script>

	<style>
		.result {
			margin: 10px 0;
			border-top: 1px dashed #ccc;
			padding-top: 10px;
		}
	</style>
	
	<!-- picker -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="<?= $directory ?>style.css">
	<script src="<?= $directory ?>mc_ajax.js"></script>
	<script src="<?= $directory ?>script.js"></script>
	<!-- picker -->

	<script src="script.js"></script>

</head>

<body>

	<div style="display: none">
		<div id="next_videos_list">
		</div>
	</div>

	<input id="key" type="hidden" value="<?= $key ?>"/>
	<input id="video_loader_step" type="hidden" value="10"/>
	
	<div class="fixed-header right">
		<div class="container-fluid">
			<div class="row">

				<div class="col-md-6">
					<div class="left">
						<h2>LocalTube mp4</h2>
					</div>

					<?php
						if(strlen($what_to_play) == 6)
						{
							echo '
								<div class="center error">
									<h5>
										Videos not found with selected Criterias!
									<h5>
								</div>
							';
						}
					?>
				</div>

				<div class="col-md-4">
					<input type="text" readonly id="symbolic_dir_path" value="<?= $dir; ?>"/>
				</div>
				
				<div class="col-md-2" syle="border: 1px solid red">
						<!-- picker -->
						<!-- Trigger the modal with a button -->
						<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#pickerModal">Dir</button>
						<!-- picker -->

						<!-- filter -->
						<!-- Trigger the modal with a button -->
						<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#filterModal">Filter</button>
						<!-- filter -->
				</div>

			</div>
		</div>
	</div>
	<div class="container-fluid">

		<div class="row">
			<div class="col-md-8">

				<div class="fixed">
					<video
						id="current_video"
						class="video-js vjs-big-play-centered mc-main-video-style"
						controls=""
						preload="auto"
						data-setup="{}"
						autoplay="true"
					>
						<source src="<?= $what_to_play ?>" type="video/mp4" />
					</video>

					<div class="div-main-info">
						<p><span id="main_video_name"><?= $label.$what_to_play ?></span></p>
						<p><?= @$files_info[$key]['file_size'] ?></p>
						<p><?= @$files_info[$key]['file_time_last_access'] ?></p>
						<p><?= @$files_info[$key]['file_last_changed'] ?></p>
					</div>
				</div>

			</div>

			<div class="col-md-4">
				<div class="files-list">
					<?php
						if($short_files_list)
						{
							foreach($short_files_list as $key => $file)
							{
								$id = trim($file);
								$id = str_replace(DIRECTORY_SEPARATOR, '', $id);
								$id = str_replace('.', '', $id);
								$id = str_replace(' ', '', $id);
								$id = preg_replace('/[^A-Za-z0-9]/', '', $id);
								echo '
									<div class="row">
										<div class="col-md-12">
											<div class="div-element">
												<a href="" class="load_to_paly_video">
													<video class="video-js mc-video-style" muted="muted">
														<source src="'.$file.'" type="video/mp4" />
													</video>
													<input id="'.$id.'" type="hidden" value="'.$key.'"/>
													<input name="file_name" type="hidden" value="'.$file.'"/>
												</a>
											</div>
											<div class="div-info">
												<p><b>File:</b> '.$file.'</p>
												<p>'.$files_info[$key]['file_size'].'</p>
												<p>'.$files_info[$key]['file_time_last_access'].'</p>
												<p>'.$files_info[$key]['file_last_changed'].'</p>
											</div>
										</div>
									</div>
									<br>
									<br>
								';
							}
						}
					?>
				</div>

				<div id="dest">
				</div>

			</div>
		</div>

		<br>
		<br>

	</div>

	<?php require_once('Modal.php'); ?>

	<!-- loader -->
	<?php require_once('Modal_loader.php');?>
	<!-- loader -->
	
	<!-- picker -->
	<?php require_once($directory.'Modal.php');?>
	<!-- picker -->
	
	<script src="https://vjs.zencdn.net/7.19.2/video.min.js"></script>

</body>
</html>
