<h2>TextHelper</h2>

<p> Useful helpers to format text.

<p> Note: only one helper now, more are added from Symfony as needed.

<?php pre_start() ?>
// Truncate text that may be too long to display in given layout
// @return string  Returns text as is, or truncated, supports multibyte strings
<em>string</em> truncate_text($text, $length = 30, $truncate_string = '...',
                     $truncate_lastspace = false) 
<?php pre_end() ?>
