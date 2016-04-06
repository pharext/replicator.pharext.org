<?php
const INCLUDED = __FILE__;

require_once "index.php";

ob_start($res);

?>
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
				padding-bottom: 2em;
				padding-top: 7em;
			}
			.header {
				top: 0;
				width: 100%;
			}
			.footer {
				bottom: 0;
				width: 100%;
				text-align: center;
				font-size: .9em;
			}
			.header, .footer {
				position: fixed;
				box-shadow: 0px 0px .8em .4em #89a;
				background: #62B3E7;
				padding: .5em 0;
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
				font-size: 1.3em;
			}
			.header h1 big, .footer, .footer a {
				color: #fdfdfd;
				text-shadow: grey 0 0 .1em;
			}
			.header h1 small {
				color: #666;
				font-size: 1.3rem;
				text-shadow: white 0 0 .2em;
			}
			li {
				list-style-type: circle;
			}
			pre.publickey {
				font-size: .8rem;
				line-height: 1rem;
				word-wrap: none;
			}
			code {
				background: #EEE;
				padding: .1rem;
				border-radius: 4px;
			}
			pre.code {
				background: #333;
				color: #62B3E7;
				padding: 1em;
				border-radius: 4px;
				margin-right: 2em;
			}
			pre.code code {
				background: transparent;
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

			<?php if (!empty($package)) : ?>

			<h2><?= htmlspecialchars($package) ?></h2>
			<table class="table table-full">
				<thead>
					<tr>
						<th class="text-left" colspan="4">Package</th>
						<th class="text-left">Date</th>
						<th class="text-right">Pharext</th>
					</tr>
				</thead>
				<tbody>

					<?php foreach (array_reverse(package_versions($package)) as $version => $phars) : ?>

					<tr>
						<td class="text-left">
							<?= htmlspecialchars($package) ?>
							<?= htmlspecialchars($version) ?>
						</td>

						<?php foreach (array_map("array_values", $phars) as $ext => list($phar, $date, $size, $pharext)) : ?>
						<td class="text-left">
							&#10507;&nbsp;<a href="<?= htmlspecialchars($phar) ?>"
							   download>ext.phar<?= htmlspecialchars($ext) ?></a>&nbsp;<small>(<?= human_size($size) ?>)</small><br>

							<?php foreach (SIGS as $typ => $sig) : ?>
								<small>#&nbsp;<a href="<?= sigof($phar, $sig) ?>" download><?= "$typ.$sig" ?></a></small>
							<?php endforeach; ?>
						</td>
						<?php endforeach; ?>
						<?php for($i = 0; $i < 3-count($phars); ++$i) : ?>

						<td></td>
						<?php endfor; ?>

						<td class="text-left">
							<?= human_date($date); ?>

						</td>
						<td class="text-right <?= version_compare($pharext, "3.0.1", "<") ? "color-red":"" ?>">
							v<?= $pharext ?>
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
		<div class="container">
			<h3>Public Keys</h3>
			<div class="column-8">
				<h4>RSA <small><a href="replicator.pub" download>replicator.pub</a></small></h4>
				<p>Verify with:</p>
					<pre class="code"><code># openssl dgst \
	-verify replicator.pub \
	-signature <?= isset($phar) ? htmlspecialchars(basename($phar)).".sig" : "apfd-1.0.1.ext.phar.sig" ?> \
	           <?= isset($phar) ? htmlspecialchars(basename($phar)): "apfd-1.0.1.ext.phar" ?></code></pre>
				<pre class="publickey">
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnzsDXNox5V0V9GLcnXEu
kxnhFs9+/AMm//1qJAoNwP6sgmYShuyI3NDZzCmT7tOIcpqW0I4P8D1Psrftyqbt
spedAvyOLCLZXaOuE130aMlvfqEiO+s8ZVZL8aHLE/orLbpOexEs33a1j6shl5C6
MoojzK3uYccL4XJfj0t2nrC+XMfWE9oQGvyLZv3tNCzH4Oy7knWVVy10EKbKgPft
izCFR+0mPYw35RN3gAGrug+khnVRMRNpS7B0uZ6E29Bgsrud9l91mVbrL+DaoaSa
IFGeYuFGe2ZpHUfxf16S0w7ybPrrJJsD6cYOtwXjRZo+4ux6PdKZ+m3hnKWoj9IF
OwIDAQAB
-----END PUBLIC KEY-----</pre>
			</div>
			<div class="column-8">
				<h4>OpenPGP <small><a href="4093AEF6.pub" download>4093AEF6.pub</a></small></h4>
				<p>Verify with:</p>
					<pre class="code"><code># gpg --import 4093AEF6.pub

# gpg --verify <?= isset($phar) ? htmlspecialchars(basename($phar)).".asc" : "apfd-1.0.1.ext.phar.asc" ?> \
               <?= isset($phar) ? htmlspecialchars(basename($phar)): "apfd-1.0.1.ext.phar" ?></code></pre>
				</p>
				<pre class="publickey">
-----BEGIN PGP PUBLIC KEY BLOCK-----

mQENBFcBXgsBCACfOwNc2jHlXRX0YtydcS6TGeEWz378Ayb//WokCg3A/qyCZhKG
7Ijc0NnMKZPu04hympbQjg/wPU+yt+3Kpu2yl50C/I4sItldo64TXfRoyW9+oSI7
6zxlVkvxocsT+istuk57ESzfdrWPqyGXkLoyiiPMre5hxwvhcl+PS3aesL5cx9YT
2hAa/Itm/e00LMfg7LuSdZVXLXQQpsqA9+2LMIVH7SY9jDflE3eAAau6D6SGdVEx
E2lLsHS5noTb0GCyu532X3WZVusv4NqhpJogUZ5i4UZ7ZmkdR/F/XpLTDvJs+usk
mwPpxg63BeNFmj7i7Ho90pn6beGcpaiP0gU7ABEBAAG0K1BIQVJleHQgUmVwbGlj
YXRvciA8cmVwbGljYXRvckBwaGFyZXh0Lm9yZz6JATcEEwEKACEFAlcBXgsCGwMF
CwkIBwMFFQoJCAsFFgIDAQACHgECF4AACgkQZJhrlUCTrvbYGAgAi120YHruidld
uPTUS05/ZLoSn3orKkmkskOsjBrUqJvQHx1s8mqJpNJdbIrgPIxQPHauiE6Fj72q
uv6TsVRxM+7VjiCHTbHmDheP5Zcyac7Nd/e62DsCYP7LAAx7MHbQvki6XQg4EsQZ
cXMKRYuuizJxNGVUeZpusY5WXmc5PRIigsI4eh/2l96IK/eqTDSZiDUwv9ze+HMf
JxOunBZVebYUQ3RYEWx1NseInxbiAnEdGM7phZH43jkohxPLROr3nWBmrJbBqULn
m6M5fRucJoldU8VIzMdy0xxu+3PuX8aug96njK448r53wjb7yRf6WLonwjlFqTWq
0tZzZR3Nd4kBHAQTAQoABgUCVwFehAAKCRBIDj4UsKTHx8iNB/0dl+8T8zp0Pksc
jGo8WBA8sfdnMqaE/NkUCbMhT5wkAk+4JRlv/DUfokB6cF87yQCC/IjVAapPT2xS
h31QubsjfnfrqSiF4ls8JCTLp/xkafx+tFsJKJOEgCsoeFCOeZBfvhhLPwLyhHLZ
ZIsHmYX8YEeku+gsbQIVnWC06WJbJ5HuKByr8VEpgGBig4eRRMcDTJShgDQhn476
VLKah3xplnU6bgOzsLn1Ssv095DOouh7tZabkS4jtvDAQK/1g2VQ/d0sOrbKAugQ
IHEcyAQedGERU0JqXrXW9WdOqs1AZTl/YGWe94kZqJ1XSWibTSq1TUNCtTcrW558
yImBVgJx
=nFzc
-----END PGP PUBLIC KEY BLOCK-----</pre>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="footer">
			<footer>
				&copy; 2015 <a href="https://m6w6.name">m6w6</a>, Michael Wallner &mdash; Powered by <a href="//github.com/m6w6/pharext">pharext
					<?php
					require_once "../vendor/autoload.php";
					printf("v%s\n", pharext\Metadata::version());
					?>
				</a>
			</footer>
		</div>
	</body>
</html>
<?php
$res->send();
?>
