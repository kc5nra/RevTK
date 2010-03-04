<h2>coreJson</h2>

<p> coreJson uses the native functions if present, otherwise it uses the PEAR Json library which is very forgiving, but also slower.

<p> If having problems with decoding a JSON string, the native functions can be disabled by setting the <samp>USE_NATIVE</samp> constant to false.

<?php pre_start('info') ?>
// Option to pass to decode() to get associative arrays instead of objects.
const JSON_ASSOC

// Set this to true to use the native php json encoding & decoding.
const USE_NATIVE        // defaults to true
<?php pre_end() ?>

<?php pre_start() ?>
// Encode any number, boolean, string, array, or object into a JSON string
<em>string</em> function ::encode(mixed $value)

// Decode JSON string into an object or associative array (if using coreJson::JSON_ASSOC)
<em>mixed</em> function ::decode($json_string, $options = 0)
<?php pre_end() ?>

<h2>Caveats of the Native Json Implementation</h2>

<p> If <var>USE_NATIVE</var> is true (by default), coreJson uses the native php encoding and decoding
    functions. They are faster but have many drawbacks:
	
<p> Native JSON support does not allow for any kind of comments in the data.

<p> Native JSON requires ALL properties to be quoted.

<pre class="info">
""             Empty string
"1"            int(1)
true           bool(true)
"true"         bool(true)
TRUE           Error, use lowercase
null           NULL
NULL           Error, use lowercase
.5             Error, use 0.5
0xFF           Error, hexadecimal values apparently not supported
[1,]           Error, cannot skip values (same for [,1] [1,,3] etc)
\r\n           Error, use double backslash \\r will be \r in string then
               the ascii value after decoding
</pre>
