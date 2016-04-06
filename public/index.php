<?php

use http\Env\Request;
use http\Env\Response;
use http\Header;

const UNITS = ["Bytes", "KB", "MB"];
const SIGS = ["rsa" => "sig", "gpg" => "asc"];

function human_size($s) {
	$l = floor(log10($s));
	return sprintf("%1.1F %s", $s/pow(10,$l-($l%3)), UNITS[$l/3]);
}

function human_date($t) {
	$d = date_create("@".$_SERVER["REQUEST_TIME"]);
	for ($i = 1; $i < 7; ++$i) {
		if ($t > $d->modify("-$i days")->format("U")) {
			switch ($i) {
			case 1:
				return "today";
			case 2:
				return "yesterday";
			default:
				return "$i days ago";
			}
		}
	}
	return gmdate("Y-m-d", $t);
}

function sigof($phar, $typ) {
	return str_replace("phars/", "sigs/", $phar) . ".$typ";
}

function package_versions($package) {
	$versions = [];
	foreach (glob("phars/$package/*.ext.phar*") as $phar) {
		list($name, $enc) = explode(".ext.phar", basename($phar));
		$data = new Phar($phar);
		$meta = $data->getMetadata();
		if ($meta) {
			$release = $meta["release"];
		} else {
			$release = substr($name, strlen($package)+1);
		}

		foreach (SIGS as $sigtyp => $sigext) {
			if (file_exists($sigdat = sigof($phar, $sigext))) {
				$sigs[$sigtyp] = $sigdat;
			}
		}
		$size = filesize($phar);
		$date = isset($meta["date"]) ? strtotime($meta["date"]) : filemtime($phar);
		$pharext = isset($meta["version"]) ? $meta["version"] : "2.0.1";
		$versions[$release][$enc] = compact("phar", "date", "size", "pharext", "sigs");
		uksort($versions[$release], function($a, $b) {
			$al = strlen($a);
			$bl = strlen($b);
			if ($al < $bl) return -1;
			if ($al > $bl) return 1;
			return 0;
		});
	}
	uksort($versions, "version_compare");
	return $versions;
}

$packages = array_map("basename", glob("phars/*", GLOB_NOSORT|GLOB_ONLYDIR));
sort($packages, SORT_NATURAL|SORT_FLAG_CASE);

if (in_array($_SERVER["QUERY_STRING"], $packages, true)) {
	$package = $_SERVER["QUERY_STRING"];
}

$res = new Response;
$req = new Request;

if (!defined("INCLUDED")) {
	if (($acc = $req->getHeader("Accept", Header::class))) {
		$neg = basename($acc->negotiate(["text/html", "application/json"]));
	} else {
		$neg = "html";
	}
	include_once "$neg.php";
}
