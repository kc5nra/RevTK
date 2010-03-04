<h2>DateHelper</h2>

<p> A helper to output dates in templates, uses the <?php echo link_to('php date() format', 'http://be.php.net/date') ?>.

<p> Currently output english dates. Always use this helper in view templates instead of using the
    php function directly, as it will help upgrading the code when internationalization is added
	to the framework.

<?php pre_start() ?>
// Formats a date, the input date can be a string or a timestamp, uses php date() format
function format_date($date, $format = 'd-m-Y')

// A user-friendly representation of a time interval, eg "about 1 month ago"
function distance_of_time_in_words($from_time, $to_time = null, $include_seconds = false)

// Like distance_of_time_in_words, but where to_time is fixed to time()
function time_ago_in_words($from_time, $include_seconds = false)

<?php pre_end() ?>
