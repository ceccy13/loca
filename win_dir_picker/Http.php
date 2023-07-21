<?php
session_start();

require_once('Tree.php');

if(isset($_REQUEST['dir']) && $_REQUEST['dir'] == 'session_destroy')
{
	//unset($_REQUEST['dir']);
	session_destroy();
	return;
}

$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';

$dirs['window'] = Tree::getDirsHtml($dir);
$dirs['breadcrumb'] = Tree::getBreadcrumbHtml();

echo json_encode($dirs);