<?php
$page = new page("test", 1);

$realex = new Realex;

$realex->createRequest(array(
	"merchantid" => "2100535408",
	"secret" => "",
	"account" => "",
	"orderid" => "test-123",
	"amount" => "0.01",
	"currency" => "GBP",
	"cardnumber" => "4658585300119002",
	"cardname" => "M R Zvirbulis",
	"cardtype" => "visa",
	"expdate" => "0717",
	"autosettleflag" => "1",
	));

$response = $realex->send();