<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php $path = coreConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="title" content="Oops! An Error Occurred" />
	<meta name="robots" content="index, follow" />
	<meta name="language" content="en" />
	<title>Oops! An Error Occurred</title>
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/sf/sf_default/css/screen.css" />
</head>
<body>
	<h1>Oops! An Error Occurred</h1>
	<h5>The server returned a "500 Internal Server Error".</h5>

	<p>What's next</p>

	<ul>
		<li><a href="javascript:history.go(-1)">Back to previous page</a></li>
		<li><a href="/">Go to Homepage</a></li>
	</ul>

</body>
</html>
