<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Replicator</title>
		<link rel="stylesheet" href="concise/css/concise.min.css">
		<link href="//fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			body {
			}
			.header {
				background: rgba(0,128,196,0.5);
				padding: 1em 0;
			}
			.header h1 {
				font-weight: bold;
			}
			.header h1 a {
				text-decoration: none;
			}
			.header h1 big {
				color: #fdfdfd;
				text-shadow: grey 0 0 .1em;
			}
			.header h1 small {
				color: #666;
				text-shadow: white 0 0 .2em;
			}
			li {
				list-style-type: circle;
			}
		</style>
	</head>
	<body>
		<div class="header">
			<header>
				<h1 class="container">
					<a href="?"><big>Replicator</big></a><br>
					<small>Replicating PECL releases as pharext packages since 2015</small>
				</h1>
			</header>
		</div>
		<div class="container">
			<?php $packages = array_map("basename", glob("phars/*")); ?>
			
			<?php if ($_SERVER["QUERY_STRING"] && in_array($_SERVER["QUERY_STRING"], $packages, true)) : ?>

			<h2><?= $package = $_SERVER["QUERY_STRING"]; ?></h2>
			<table class="table table-full">
				<thead>
					<tr>
						<th class="text-left">Package</th>
						<th class="text-left">Date</th>
						<th class="text-right">Size</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (array_reverse(glob("phars/$package/*.ext.phar*")) as $phar) : ?>
					<tr>
						<td class="text-left"><a href="<?= htmlspecialchars($phar) ?>"
							   ><?= htmlspecialchars(basename($phar)) ?></a>
						</td>
						<td class="text-left">
							<?php

							$time = time();
							$dsec = 86400;
							$lmod = filemtime($phar);
							$days = [1 => "today", "yesterday"];
							do {
								for ($i = 1; $i < 7; ++$i) {
									if ($lmod > $time - $dsec * $i) {
										switch ($i) {
											case 1:
												echo "today";
												break 3;
											case 2:
												echo "yesterday";
												break 3;
											default:
												echo "$i days ago";
												break 3;
										}
									}
								}
								echo date("Y-m-d", $lmod);
							} while (false);
							
							?>
						</td>
						<td class="text-right">
							<?php

							$u = ["Bytes", "KB", "MB"];
							$s = filesize($phar);
							$l = floor(log10($s));
							printf("%1.1F %s\n", $s/pow(10,$l-($l%3)), $u[$l/3]);

							?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php else:	?>

			<h2>Available Packages</h2>
			<ul class="list-inline">
			<?php foreach (array_map("htmlspecialchars", $packages) as $index => $package) : ?>
				<?php $next = strtolower($package{0}); ?>
				<?php if (isset($prev) && $next != $prev) : ?>
			
			</ul>
			<ul class="list-inline">
				<?php endif; ?>

				<li><a href="?<?= $package ?>"><?=  $package ?></a></li>
				<?php $prev = $next; ?>
			<?php endforeach; ?>
			
			</ul>
			<?php endif; ?>

		</div>
	</body>
</html>
