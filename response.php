<?php
error_reporting(E_ALL);
set_time_limit(60);
/*****************************
****respose example
<?xml version="1.0" encoding="UTF-8"?>
<update>
	<infos>
		<li>版本号</li>
		<li>大小</li>
		<li>HASH</li>		
	</infos>
	<urls>
		<li>a</li>
		<li>b</li>
	</urls>
</update>
if($_SERVER['REQUEST_METHOD']=='GET'){
	header('Location:/',false,301);
	die();
	}
******************************/

include 'chrome.lib.php';
$u = new Updater();
$u->setChannel($_REQUEST['channel']);
$u-> buildXML($_REQUEST['arch']);
$u->request();
$u->fetchUrls();
$dom = new DOMDocument;
$up = $dom->createElement('update');
$dom->appendChild($up);
$infos = $dom->createElement('infos');
$up->appendChild($infos);
$infos->appendChild($dom->createElement('info','版本号：'.$u->model->version));
$infos->appendChild($dom->createElement('info','大小：'.$u->model->size));
$infos->appendChild($dom->createElement('info','Hash：'.$u->model->hash));
$urls=$dom->createElement('urls');
$up->appendChild($urls);
foreach($u->model->urls as $url){
	$_url = $dom->createElement('url',$url);
	$urls->appendChild($_url);
	}
echo $dom->saveXML();	