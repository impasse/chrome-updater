<?php
class UpdaterModel{
//Update result model
public $name;
public $version;
public $size;
public $hash;
public $urls;
public function __construct($name,$version,$size,$hash,$urls){
	$this->name =$name;
	$this->version = $version;
	$this->size =number_format($size/1024/1024,2).'M';
	$this->hash =$hash;
	foreach($urls as $url){
		$this->urls[] = $url.$name;
	}
}
}

class Updater{
private $channel ;
public $xml;
private $url ='https://tools.google.com/service/update2';
private $userAgent="Google Update/1.3.23.9;winhttp";
//if use proxy
private $useProxy = false;
private $proxy_host="127.0.0.1";
private $proxy_port="8087";
public $response;
public  $model;

public function __construct(){
  $this->channel="stable";
}
public function setChannel($ch){
//channel:stable,beta,dev,canary
  $this->channel=$ch;
  }
  
 public function buildXML($arch="x86"){
 //arch :x86 or x64
  $conf=array(
    "stable"=>array(
	  "appid"=>"4DC8B4CA-1BDA-483E-B5FA-D3C12E15B62D",
	  "ap" => "-multi-chrome",
	  "ap64" => "x64-multi-chrome"
	 ),
	"beta"=>array(
	 "appid" => "4DC8B4CA-1BDA-483E-B5FA-D3C12E15B62D",
	 "ap" => "1.1-beta",
	 "ap64" => "1.1-beta-x64-beta-multi-chrome",
	 "ap64" => "x64-beta-multi-chrome"
	 ),
	 "dev"=>array(
	 "appid" => "4DC8B4CA-1BDA-483E-B5FA-D3C12E15B62D",
	  "ap" => "2.0-dev",
	  "ap64" => "x64-dev-multi-chrome"
	  ),
	  "canary"=>array(
	  "appid" => "4EA16AC7-FD5A-47C3-875B-DBF4A2008C20",
	  "ap" => "",
	  "ap64" => "x64-canary"
	  ));
	  // reconfirm channel
if(!in_array($this->channel,array_keys($conf))){
	  $this->channel="stable";
	}
	  //start build xml query
	  if($arch=="x86"){
	  $this->xml=<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request protocol="3.0" version="1.3.23.9" shell_version="1.3.21.103" ismachine="0"  sessionid="{3597644B-2952-4F92-AE55-D315F45F80A5}" installsource="ondemandcheckforupdate"  requestid="{CD7523AD-A40D-49F4-AEEF-8C114B804658}" dedup="cr">
<os platform="win" version="6.1" sp="Service Pack 1" arch="x86"/>
<app appid="{{$conf[$this->channel]['appid']}}" version="" nextversion="" ap="{$conf[$this->channel]['ap']}" lang="" brand="GGLS" client="">
<updatecheck/>
</app>
</request>
XML;
	  }else{
	  $this->xml=<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request protocol="3.0" version="1.3.23.9" shell_version="1.3.21.103" ismachine="0"  sessionid="{3597644B-2952-4F92-AE55-D315F45F80A5}" installsource="ondemandcheckforupdate" requestid="{CD7523AD-A40D-49F4-AEEF-8C114B804658}" dedup="cr">
<os platform="win" version="6.1" sp="Service Pack 1" arch="x64"/>
<app appid="{{$conf[$this->channel]['appid']}}" version="" nextversion="" ap="{$conf[$this->channel]['ap64']}"  lang="" brand="GGLS" client="">
<updatecheck/>
</app>
</request>
XML;
	  }
}
public function setProxy($host,$port){
 $this->proxy_host=$host;
 $this->proxy_port=$port;
 $this->useProxy=true;
 }
public function request(){
$head=array('Content-type:application/x-www-form-urlencoded');
$c=curl_init();
curl_setopt($c, CURLOPT_URL,$this->url);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_USERAGENT,$this->userAgent);
curl_setopt($c,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($c, CURLOPT_TIMEOUT, 300);
curl_setopt($c,CURLOPT_HTTPHEADER,$head);
if($this->useProxy){
curl_setopt($c, CURLOPT_PROXY,$this->proxy_host);
curl_setopt($c, CURLOPT_PROXYPORT,$this->proxy_port );
}
curl_setopt($c, CURLOPT_RETURNTRANSFER,1);
curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false); 
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c, CURLOPT_POSTFIELDS, $this->xml);
$this->response=curl_exec($c) or die("CURL_ERROR:".curl_error($c));
curl_close($c);
}
private function getAttribute($name){
//get information from response
preg_match_all('%'.$name.'="([^"]+)"'.'%',$this->response,$result);
switch($name){
	case 'codebase':
	return $result[1];
	case 'version':
	return $result[1][1];	
	default:
	return $result[1][0];
	}
}
public function fetchUrls(){
	$this->model = new UpdaterModel($this->getAttribute('name'),$this->getAttribute('version'),$this->getAttribute('size'),$this->getAttribute('hash'),$this->getAttribute('codebase'));
	}
}
/*****************************
**FOR　TEST 
$u = new Updater();
$u-> buildXML();
$u->request();
$u->fetchUrls();
var_dump($u->model);
*****************************/