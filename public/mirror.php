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
		if (($json = json_decode($request->getBody()))) {
			if (in_array($json->repository->owner->name, $owners, true)) {
				$repo = $json->repository->full_name;
				$path = $mirror . "/" . $repo;
				if (is_dir($path) && chdir($path)) {
					passthru("git fetch -p", $rv);
					if ($rv == 0) {
						$response->setResponseCode(200);
					}
				} elseif (mkdir($path, 0755, true) && chdir($path)) {
					passthru("git clone --mirror " . escapeshellarg($repo) . " .", $rv);
					if ($rv == 0) {
						$response->setResponseCode(200);
					}
				}
			} else {
				$response->setResponseCode(403);
				$response->getBody()->append("Invalid owner");
			}
		} else {
			$response->setResponseCode(415);
			$response->setContentType($request->getHeader("Content-Type"));
			$response->getBody()->append($request->getBody());
		}
		break;
}

$response->send();
