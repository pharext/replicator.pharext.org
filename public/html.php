<?php
const INCLUDED = __FILE__;
const NCURRENT = 2;

$css = "concise/css/concise.min.css";
$fnt = "//fonts.googleapis.com/css?family=Droid+Sans";

require_once "index.php";

ob_start($res);
$res->addHeader("Link", "<" . dirname((new http\Env\Url)->path) . "/" . $css . ">; rel=preload; as=style");

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Replicator</title>
		<link rel="stylesheet" href="<?=$css?>">
		<link rel="stylesheet" href="<?=$fnt?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="color-scheme" content="dark light">
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
				padding: .5em 0;
			}
			.header h1 {
				font-weight: bold;
				line-height:120%;
			}
			.header h1 a, .footer a:hover {
				text-decoration: none;
			}
			.header h1 a:hover {
				text-decoration: underline;
			}
			.header h1 a {
				/* normalize browser difference */
				font-size: 1.3em;
			}
			.header h1 small {
				font-size: 1.3rem;
			}
			li {
				list-style-type: circle;
			}
			pre.publickey {
				font-size: .8rem;
				line-height: 1rem;
			}
			pre.code {
				background: #333;
				color: #62B3E7;
				padding: 0 1.5em 1.5em 1em;
				border-radius: 4px;
				margin-right: 2em;
				display: inline-block;
			}
			pre.code>code {
				font-size: .9rem;
			}
			.row>h3 {
				margin-bottom: 0;
			}
			hr {
				margin: 2em 0;
			}
			@media(max-width: 80em) {
				.column-8 {
					float: none;
					width: auto;
				}
			}
			.old-version, #new-toggle {
				display: none;
			}
			p.package-description, div.package-description p {
				white-space: pre-line;
			}
			.package-list {
			}
			.package-ch {
			}
			form * {
				display: inline-block;
				margin-right: 1em;
			}
			form label input{
				margin-left: 1em;
				vertical-align: middle;
			}
			form input[type=reset] {
				padding: 0;
			}
			.header, .footer {
				box-shadow: 0px 0px .8em .4em #89a;
				background: #62B3E7;
			}
			.header h1 a, .footer, .footer a {
				color: #fdfdfd;
				text-shadow: grey 0 0 .1em;
			}
			.header h1 small {
				color: #666;
				text-shadow: white 0 0 .2em;
			}
			@media (prefers-color-scheme: dark) {
				.header, .footer {
					box-shadow: 0px 0px .8em .4em #123;
					background: #305872;
				}
				.header h1 a, .footer, .footer a {
					color: #bdbdbd;
					text-shadow: dimgrey 0 0 .1em;
				}
				.header h1 small {
					color: #aaa;
					text-shadow: black 0 0 .2em;
				}
				body, h2, h3, h4, h5, h6 {
					background: #3a3b3f;
					color: #bdbdbd;
				}
			}
		</style>
	</head>
	<body>
		<div class="header">
			<header>
				<h1 class="container">
					<a href="?">Replicator</a><br>
					<small>Replicating PECL releases as pharext packages since 2015</small>
				</h1>
				<a href="https://github.com/m6w6/replicator"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>
			</header>
		</div>
		<div class="container">

			<?php if (!empty($package)) : $versions = package_versions($package); $info = package_info($package); ?>

			<h2><?= htmlspecialchars($package) ?></h2>
			<?php if ($info) : ?>
				<h3><?= htmlspecialchars($info["title"]) ?><br>
					<small>License: <?= htmlspecialchars($info["license"]) ?><br>
						<a href="//pecl.php.net/package/<?= htmlspecialchars($package) ?>">View at PECL</a></small></h3>
				<?php if (extension_loaded("discount")) : ?>
				<div class="package-description">
					<?php
					$md = MarkdownDocument::createFromString($info["description"]);
					$md->compile(	MarkdownDocument::AUTOLINK |
									MarkdownDocument::ONE_COMPAT);
					echo $md->getHtml();
					?>
				</div>
				<?php else : ?>
				<p class="package-description">
					<?= htmlspecialchars($info["description"]) ?>
				</p>
				<?php endif; ?>
			<?php endif; ?>
			<table class="table table-full versions">
				<thead>
					<tr>
						<th class="text-left" colspan="2">Package</th>
						<th class="text-left" colspan="<?= count(SIGS) ?>">Signatures</th>
						<th class="text-left">Date</th>
						<th class="text-right">Pharext</th>
					</tr>
				</thead>
				<tbody>

					<?php $i = 0; foreach (array_reverse($versions) as $version => $phars) : ++$i; ?>
						<?php foreach (array_map("array_values", $phars) as $ext => list($phar, $date, $size, $pharext)) : ?>
					<tr <?php if ($i > NCURRENT) : ?>class="old-version"<?php endif; ?> <?php if ($i === NCURRENT) : ?>id="old"<?php endif; ?>>
						<?php if (empty($ext)) : ?>
						<td class="text-left" rowspan="<?= count($phars) ?>">
							<?= htmlspecialchars($package) ?>
							<?= htmlspecialchars($version) ?>
						</td>
						<?php endif ?>

						<td class="text-left">
							&#10507;&nbsp;<a href="<?= htmlspecialchars($phar) ?>"
							   download>phar<?= htmlspecialchars($ext) ?></a>&nbsp;<small>(<?= human_size($size) ?>)</small><br>
						</td>
							<?php foreach (SIGS as $typ => $sig) : ?>
						<td>
								#&nbsp;<a href="<?= sigof($phar, $sig) ?>" download><?= "$typ.$sig" ?></a>
						</td>
							<?php endforeach; ?>
						<td class="text-left">
							<?= human_date($date); ?>

						</td>
						<td class="text-right <?= version_compare($pharext, "3.0.1", "<") ? "color-red":"" ?>">
							v<?= $pharext ?>
						</td>
					</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>

				</tbody>
			</table>
				<?php if ($i >= 3) : ?>
					<p class="small">
						<a id="old-toggle" href="#old" onclick="toggleOldVersions(this)">Show
							<?=count($versions)-NCURRENT?> older version(s) &raquo;</a>
						<a id="new-toggle" href="#" onclick="toggleOldVersions(this)">Show
							less versions &laquo;</a>
					</p>
				<?php endif; ?>

			<?php else:	?>

			<h2>Available Packages</h2>
			<form name="search"></form>
			<ul class="list-inline package-list">
			<?php foreach (array_map("htmlspecialchars", $packages) as $index => $pkg) : ?>
				<?php $next = strtolower($pkg[0]); ?>
				<?php if (isset($prev) && $next != $prev) : ?>

			</ul>
			<ul class="list-inline package-list">
				<?php endif; ?>

				<li id="<?= strtolower($pkg) ?>"><a href="?<?= $pkg ?>"><?=  $pkg ?></a></li>
				<?php $prev = $next; ?>
			<?php endforeach; ?>

			</ul>
			<?php endif; ?>
			<hr>
			<div class="row">
		<?php if (empty($package)) : ?>
				<h3>Public keys</h3>
		<?php else : list($phar) = array_values(current(end($versions))); ?>
				<h3>Download latest version and signatures:</h3>
				<div class="column-16">
					<pre class="code fit-code"><code>
curl -sS \
     -O https://replicator.pharext.org/<?= htmlspecialchars($phar) ?><?php foreach (SIGS as $sig) : ?> \
     -O https://replicator.pharext.org/<?= htmlspecialchars(sigof($phar, $sig)) ?><?php endforeach; ?></code></pre>
				</div>
			</div>
			<div class="row">
				<h3>Verify with a public key:</h3>
		<?php endif; ?>

				<div class="column-8">
					<h4>RSA <small><a href="replicator.pub" download>replicator.pub</a></small></h4>
					<?php if (!empty($phar)) : ?>
					<pre class="code"><code>
curl -sSO https://replicator.pharext.org/replicator.pub

openssl dgst \
	-verify replicator.pub \
	-signature <?= htmlspecialchars(basename($phar)).".sig" ?> \
	           <?= htmlspecialchars(basename($phar)) ?></code></pre>
					<?php endif; ?>
					<pre class="publickey"><?php readfile("./replicator.pub") ?></pre>
				</div>
				<div class="column-8">
					<h4>OpenPGP <small><a href="4093AEF6.pub" download>4093AEF6.pub</a></small></h4>
					<?php if (!empty($phar)) : ?>
					<pre class="code"><code>
curl -sSO https://replicator.pharext.org/4093AEF6.pub

gpg --import 4093AEF6.pub

gpg --verify <?= htmlspecialchars(basename($phar)).".asc" ?> \
             <?= htmlspecialchars(basename($phar)) ?></code></pre>
					<?php endif; ?>
				<pre class="publickey"><?php readfile("./4093AEF6.pub") ?></pre>
				</div>
			</div>
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
		<script type="text/javascript">
		function searchPackages(search, regex) {
			console.log("searchPackages", search, regex);
			document.querySelectorAll("ul.package-list li").forEach(function(li) {
				if (regex) {
					if (li.id.match(search.toLowerCase())) {
						li.style.removeProperty("display");
					} else {
						li.style.display = "none";
					}
				} else {
					if (li.id.startsWith(search.toLowerCase())) {
						li.style.removeProperty("display");
					} else {
						li.style.display = "none";
					}
				}
			});
		}

		document.body.onload = function() {
			var form = document.querySelector("form[name=search]");

			if (!form) return;

			var input = document.createElement("input");
			var reset = document.createElement("input");
			var prefix_label = document.createElement("label");
			var prefix = document.createElement("input");
			var regex_label = document.createElement("label");
			var regex = document.createElement("input");

			form.onreset = function() {
				searchPackages("", false);
			};

			input.id = "s";
			input.autocomplete = "off";
			input.name = "s";
			input.type = "search";
			input.placeholder = "Search...";
			input.oninput = function() {
				searchPackages(input.value, regex.checked);
			};
			input.style.paddingRight = "4ch";
			form.appendChild(input);

			reset.id = "r";
			reset.name = "r";
			reset.type = "reset";
			reset.value = "☒";
			reset.title = "Reset";
			reset.style.marginLeft = "-4ch";
			reset.style.marginRight = "4ch";
			reset.style.border = "none";
			reset.style.background = "transparent";
			form.appendChild(reset);

			prefix.id = "prefix";
			prefix.name = "by";
			prefix.value = "prefix";
			prefix.type = "radio";
			prefix.defaultChecked = true;
			prefix.checked = true;
			prefix.onchange = function() {
				searchPackages(input.value, regex.checked);
			};
			//form.appendChild(prefix);
			prefix_label.innerText = "by Prefix";
			prefix_label.appendChild(prefix);
			form.appendChild(prefix_label);

			regex.id = "regex";
			regex.name = "by";
			regex.value = "regex";
			regex.type = "radio";
			regex.checked = false;
			regex.onchange = function() {
				searchPackages(input.value, regex.checked);
			};
			//form.appendChild(regex);
			regex_label.innerText = "by RegExp";
			regex_label.appendChild(regex);
			form.appendChild(regex_label);

			form.after(document.createElement("HR"));

			input.focus();
		};

		function toggleOldVersions(a) {
			var nodes, row_style;

			if (a.hash.substring(1) === "old") {
				row_style = "table-row";
				document.getElementById("old-toggle").style.display = "none";
				document.getElementById("new-toggle").style.display = "inline";
			} else {
				row_style = "none";
				document.getElementById("old-toggle").style.display = "inline";
				document.getElementById("new-toggle").style.display = "none";
			}

			nodes = document.querySelectorAll("table.versions>tbody>tr.old-version");

			for (var i = 0; i < nodes.length; ++i) {
				nodes.item(i).style.display = row_style;
			}
		}
		</script>
	</body>
</html>
<?php
$res->send();
?>
