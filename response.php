<?php
error_reporting(~E_ALL);
/*****************************
****respose example
<?xml version="1.0" encoding="UTF-8"?>
<up>
	<info>
		<li>版本号</li>
		<li>大小</li>
		<li>HASH</li>		
	</info>
	<urls>
		<li>a</li>
		<li>b</li>
	</urls>
</up>
******************************/
if($_SERVER['REQUEST_METHOD']=='GET'){
	header('Location:/',false,301);
	die();
	}
include 'chrome.lib.php';
$u = new Updater();
$u->setChannel($_REQUEST['channel']);
$u-> buildXML($_REQUEST['arch']);
$u->request();
$u->fetchUrls();
$dom = new DOMDocument;
$up = $dom->createElement('up');
$dom->appendChild($up);
$info = $dom->createElement('info');
$up->appendChild($info);
$info->appendChild($dom->createElement('li','版本号：'.$u->model->version));
$info->appendChild($dom->createElement('li','大小：'.$u->model->size));
$info->appendChild($dom->createElement('li','Hash：'.$u->model->hash));
$urls=$dom->createElement('urls');
$up->appendChild($urls);
foreach($u->model->urls as $url){
	$li = $dom->createElement('li');
	$a = $dom->createElement('a',$url);
	$urls->appendChild($li);
	$li->appendChild($a);
	$a -> setAttribute('href',$url);
	$a -> setAttribute('target','__blank');
	}
echo $dom->saveXML();	