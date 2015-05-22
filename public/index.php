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
				padding-bottom: 4em;
				padding-top: 10em;
			}
			.header {
				top: 0;
				width: 100%;
			}
			.footer {
				bottom: 0;
				width: 100%;
				text-align: center;
			}
			.header, .footer {
				position: fixed;
				box-shadow: 0px 0px 1em .4em #89a;
				background: #62B3E7;
				padding: 1em 0;
			}
			.header h1 {
				font-weight: bold;
			}
			.header h1 a, .footer a:hover {
				text-decoration: none;
			}
			.header h1 a:hover {
				text-decoration: underline;
			}
			.header h1 big {
				/* normalize browser difference */
				font-size: 1.8em;
			}
			.header h1 big, .footer, .footer a {
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
				<a href="https://github.com/m6w6/replicator"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>
			</header>
		</div>
		<div class="container">
			<?php 
			
			$packages = array_map("basename", glob("phars/*", GLOB_NOSORT|GLOB_ONLYDIR)); 
			sort($packages, SORT_NATURAL|SORT_FLAG_CASE);

			?>
			
			<?php if ($_SERVER["QUERY_STRING"] && in_array($_SERVER["QUERY_STRING"], $packages, true)) : ?>

			<h2><?= $package = $_SERVER["QUERY_STRING"]; ?></h2>
			<table class="table table-full">
				<thead>
					<tr>
						<th class="text-left" colspan="4">Package</th>
						<th class="text-left">Date</th>
						<th class="text-right">Pharext</th>
					</tr>
				</thead>
				<tbody>
					
					<?php
					
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
						$versions[$release][$enc] = [$phar, $meta];
						uksort($versions[$release], function($a, $b) {
							$al = strlen($a);
							$bl = strlen($b);
							if ($al < $bl) return -1;
							if ($al > $bl) return 1;
							return 0;
						});
					}
					uksort($versions, "version_compare");
					
					?>
					<?php foreach (array_reverse($versions) as $version => $phars) : ?>

					<tr>
						<td class="text-left">
							<?= htmlspecialchars($package) ?>
							<?= htmlspecialchars($version) ?>
						</td>
						
						<?php foreach ($phars as $ext => list($phar, $meta)) : ?>
						<td class="text-left">
							<a href="<?= htmlspecialchars($phar) ?>"
							   >ext.phar<?= htmlspecialchars($ext) ?></a><br>
							<small><?php

							$u = ["Bytes", "KB", "MB"];
							$s = filesize($phar);
							$l = floor(log10($s));
							printf("&#10507; %1.1F %s", $s/pow(10,$l-($l%3)), $u[$l/3]);

							?></small>
						</td>
						<?php endforeach; ?>
						<?php for($i = 0; $i < 3-count($phars); ++$i) : ?>
						
						<td></td>
						<?php endfor; ?>
						
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
							if ($meta) {
								printf("v%s\n", $meta["version"]);
							} else {
								print "v2.0.1\n";
							}
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
		<div class="footer">
			<footer>
				&copy; 2015 m6w6, Michael Wallner &mdash; Powered by <a href="//github.com/m6w6/pharext">pharext
					<?php
					require_once "../vendor/autoload.php";
					printf("v%s\n", pharext\Metadata::version());
					?>
				</a>
			</footer>
		</div>
	</body>
</html>
