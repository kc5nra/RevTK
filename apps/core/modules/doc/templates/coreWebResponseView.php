<h2>coreResponse</h2>

<p> The Response object manipulates client response information such as headers, cookies and content.

<h2>coreWebResponse</h2>

<p> coreWebResponse extends coreResponse with the following methods:

<?php pre_start() ?>
// If set, any content will be skipped and only headers sent
function setHeaderOnly($value = true)

// Sets response status code.
function setStatusCode($code, $name = null)
// Retrieves status code for the current web response.
function getStatusCode()

// Sets a cookie.
function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)

// Sets a HTTP header. If $value is null, the HTTP header is removed.
function setHttpHeader($name, $value, $replace = true)
// Gets HTTP header current value.
function getHttpHeader($name, $default = null)

// Sets response content type. Note that <var>sf_charset</var> is always appended.
function setContentType($value)
// Gets response content type.
function getContentType()

// Adds a http-equiv meta tag, and sets a corresponding HTTP header.
// If $value is null, the HTTP header is removed.
function addHttpMeta($key, $value, $replace = true)

// Adds a meta header.
function addMeta($key, $value, $replace = true, $escape = true)

// Retrieves title for the current web response.
function getTitle()
// Sets title for the current web response.
function setTitle($title, $replace = true, $escape = true)

// Adds a stylesheet to the current web response.
function addStylesheet($css, $position = '', $options = array())

// Adds javascript code to the current web response.
function addJavascript($js, $position = '', $options = array())
<?php pre_end() ?>

<h2>Cookie getter and setter</h2>

<p> To get a cookie, you inspect the request that was sent to the server, thus using the <?php echo link_to('coreWebRequest', 'doc/core?include_name=request') ?> object.
    On the other hand, to set a cookie, you modify the response that will be sent to the user, thus using the <b>coreWebResponse</b> object.
	
<p> To manipulate cookies from within an action, use the following shorcuts:

<?php pre_start() ?>
// cookie getter
$string = $this->getRequest()->getCookie('mycookie');
 
// cookie setter
$this->getResponse()->setCookie('mycookie', $value);
 
// cookie setter with options
$this->getResponse()->setCookie('mycookie', $value, $expire, $path, $domain, $secure);
<?php pre_end() ?>

<p> Hint: you can see cookies with the FireBug extension in two ways:
<ul>
	<li>Type <samp>document.cookie</samp> in the Console.
	<li>In the <b>Net</b> tab, click the document link, under <b>Response Headers</b> look for the "Set-Cookie" property.
</ul>

<h2>Setting the HTML Head Tags</h2>

<p> The HTML document head tags can be set from the web response by using the <?php echo link_to('AssetHelper','doc/helper?helper_name=asset') ?>
    helpers in the layout template.
