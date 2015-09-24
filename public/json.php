<?php
const INCLUDED = __FILE__;

require_once "index.php";

$res->setContentType("application/json");
$res->getBody()->append(
	json_encode(empty($package) ? $packages : package_versions($package))
);
$res->send();
