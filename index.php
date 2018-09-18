<?php
require "system/config.php";
$pageInfo = explode("/", $_SERVER['REQUEST_URI']);

if($pageInfo[1] == ""){
	$pageInfo[1] = "main";
}

if(page::isLoggedIn() === false){
	$pageInfo[1] = "login";
}

if(file_exists("pages/" . $pageInfo[1] . ".php")){
	include_once("pages/" . $pageInfo[1] . ".php");	
}else{
	include_once("pages/" . default_page . ".php");	
}