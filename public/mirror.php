<?php

use http\Env\Request;
use http\Env\Response;
use http\Params;

$request = new Request;
$response = new Response;
$response->setResponseCode(500);
ob_start($response);

$owners = explode(",", getenv("owners") ?: "m6w6");
$mirror = getenv("mirror") ?: "/var/github";
$secret = getenv("secret") ?: trim(file_get_contents("$mirror/.secret"));

$sig = $request->getHeader("X-Hub-Signature");
$evt = $request->getHeader("X-Github-Event");

if (!$sig || !$evt) {
	$response->setResponseCode(400);
	$response->setContentType("message/http");
	$response->getBody()->append($request);
	return $response->send();
}

foreach ((new Params($sig))->params as $algo => $mac) {
	if ($mac["value"] !== hash_hmac($algo, $request->getBody(), $secret)) {
		$response->setResponseCode(403);
		$response->getBody()->append("Invalid signature");
		return $response->send();
	}
}

switch ($evt) {
	default:
		$response->setResponseCode(202);
		$response->getBody()->append("Not a configured event");
		break;
	case "ping";
		$response->setResponseCode(204);
		$response->setResponseStatus("PONG");
		break;
	case "push":
		if (!($json = json_decode($request->getBody()))) {
			$response->setResponseCode(415);
			$response->setContentType($request->getHeader("Content-Type"));
			$response->getBody()->append($request->getBody());
		} elseif (!in_array($json->repository->owner->name, $owners, true)) {
			$response->setResponseCode(403);
			$response->getBody()->append("Invalid owner");
		} else {
			$repo = $json->repository->full_name;
			$path = $mirror . "/" . $repo;
			if (is_dir($path) && chdir($path)) {
				passthru("git fetch -vp 2>&1", $rv);
				if ($rv == 0) {
					$response->setResponseCode(200);
				}
			} elseif (mkdir($path, 0755, true) && chdir($path)) {
				$source = escapeshellarg($json->repository->clone_url);
				$description = escapeshellarg($json->repository->description);
				passthru("git clone --mirror $source . 2>&1", $rv);
				passthru("git config gitweb.description $description 2>&1");
				unlink("description");
				if ($rv == 0) {
					$response->setResponseCode(200);
				}
			}
		}
		break;
}

$response->send();
