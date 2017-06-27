<!DOCTYPE html>
<html>
<head>
<title>PHP-Proxy</title>
<meta name="generator" content="php-proxy.com">
<meta name="version" content="<?=$version;?>">
<link rel="stylesheet" href="/licenta/crawlweb/web/assets/css/proxy/first_page.css">
</head>

<body>
<div id="container">
	<?php if(isset($error_msg)): ?>
		<div id="error">
			<p><?php echo $error_msg; ?></p>
		</div>
	<?php endif; ?>
	
	<div id="frm">
		<form action="proxy/confirm" method="post" style="margin-bottom:0;">
			<input name="url" type="text" style="width:400px;" autocomplete="off" placeholder="http://" />
			<input type="submit" value="Go" />
		</form>
	</div>
</div>

<div id="footer">
	Powered by <a href="//www.php-proxy.com/" target="_blank">PHP-Proxy</a> <?php echo $version; ?>
</div>

<script type="text/javascript">
</script>

</body>
</html>