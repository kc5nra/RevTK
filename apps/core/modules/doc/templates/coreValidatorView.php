<h2>coreValidator</h2>

<p> The coreValidator class provides methods to facilitate validation of data (typically forms)
    through configuration and builtin validators. Validators are provided for the most
  common needs and it is very easy to add custom validators.

<p> The coreValidator methods:

<?php pre_start() ?>
// Initialize a validator, loads configuration from /modules/validate/[validatorName].php
public function __construct($validatorName)

// Validate an associative array of key => value data against all configured validators
// @return boolean  True if data is valid, false if one or more validations failed. 
public function validate($data)

// Builtin validator methods (not directly usable yet)
protected function StringValidator($value, $params)
protected function NumberValidator($value, $params)
protected function RegexValidator($value, $params)
protected function EmailValidator($value, $params)
protected function UrlValidator($value, $params)
protected function CompareValidator($value, $params)
protected function CallbackValidator($value, $params)
<?php pre_end() ?>

<h2>The Builtin Validators</h2>

<p> The standard Validators are:

<ul>
  <li>StringValidator
  <li>NumberValidator
  <li>EmailValidator
  <li>RegexValidator
  <li>UrlValidator
  <li>CompareValidator
  <li>CallbackValidator
</ul>


<h2>StringValidator</h2>

<ul>
  <li><b>min</b> : <samp>OPTIONAL</samp>
  <li><b>min_error</b> : 
  <li><b>max</b> : <samp>OPTIONAL</samp>
  <li><b>max_error</b> : 
</ul>  

<?php pre_start('info') ?>
      'StringValidator' => array
      (
        'min'           => 3,
        'min_error'     => 'Firstname is too short (min 3 characters)',
        'max'           => 10,
        'max_error'     => 'Firstname is too long (max 10 characters)'
      )
<?php pre_end() ?>

<h2>NumberValidator</h2>

<ul>
  <li><b>nan_error</b> : <samp>REQUIRED</samp>
  <li><b>min</b> : <samp>OPTIONAL</samp>
  <li><b>min_error</b> : 
  <li><b>max</b> : <samp>OPTIONAL</samp>
  <li><b>max_error</b> : 
  <li><b>type</b> : Type of number ("int", "integer", "decimal", "float"). <samp>OPTIONAL</samp>
  <li><b>type_error</b> : 
</ul>  

<?php pre_start('info') ?>
      'NumberValidator' => array
      (
        'nan_error'     => 'Please enter an integer',
        'min'           => 0,
        'min_error'     => 'The value must be at least zero',
        'max'           => 100,
        'max_error'     => 'The value must be less than or equal to 100',
        'type'          => 'int',
        'type_error'    => 'The value must be an integer'
      )
<?php pre_end() ?>


<h2>EmailValidator</h2>

<ul>
  <li><b>email_error</b> : <samp>REQUIRED</samp>
  <li><b>strict</b> : <samp>OPTIONAL</samp><br/>
      true  to match only emails in the form name@domain.extension (default)<br/>
      false to match emails against RFC822 (this will accept emails such as me@localhost)
</ul>  

<?php pre_start('info') ?>
      'EmailValidator'  => array
      (
        'email_error'   => 'Please enter a valid email address.',
        'strict'        => true
      )
<?php pre_end() ?>

<h2>RegexValidator</h2>

<ul>
  <li><b>match</b> : Whether to validate if it matches (true) or doesn't match (false) <samp>REQUIRED</samp>
  <li><b>pattern</b> : The regular expression <samp>REQUIRED</samp>
  <li><b>match_error</b> : Error message <samp>REQUIRED</samp>
</ul>  

<?php pre_start('info') ?>
      'RegexValidator'  => array
      (
        'match'         => true,
        'pattern'       => "/^[a-zA-Z ]+$/",
        'match_error'   => "Only letters and spaces"
      )
<?php pre_end() ?>

<h2>UrlValidator</h2>

<ul>
  <li><b>url_error</b> : <samp>REQUIRED</samp>
</ul>  

<?php pre_start('info') ?>
      'UrlValidator'    => array
      (
        'url_error'     => 'Value does not qualify as a valid URL'
      )
<?php pre_end() ?>

<h2>CompareValidator</h2>

<p> The CompareValidator compares two different request parameters. It is very useful for password checks.</p>

<ul>
  <li><b>check</b> : Required. Name of field to compare value to.
  <li><b>compare_error</b> : Required
</ul>  

<?php pre_start('info') ?>
      'CompareValidator' => array
      (
        'check'          => 'password',
        'compare_error'  => "Passwords don't match"
      )
<?php pre_end() ?>

<h2>CallbackValidator</h2>

<p> The CallbackValidator delegates the validation to a third-party callable method or function to
    do the validation. The callable method or function must return true or false.

<ul>
  <li><b>callback</b> : a function name or array($classname, $methodname) for a static method<br/>
      (this is the same as for call_user_func())
  <li><b>invalid_error</b> : the error message
</ul>

<?php pre_start('info') ?>
      'CallbackValidator' => array
      (
        'callback'        => 'is_numeric',
        'invalid_error'   => 'Validation failed'
      )
<?php pre_end() ?>


<h2>Validation File</h2>

<p> The validator configuration is declared as an associative array of fields and validation
    rules.

<p> Validation files are stored in <samp>apps/<var>[myApp]</var>/modules/<var>[module]</var>/validate/</samp>.

<p> It is recommended to align the => operators to make the configuration file more readable:

<?php pre_start('info') ?>
&lt;?php
/**
 * Example Validator configuration file
 * 
 * @package    SweetApp
 * @author     Fabrice Denis
 */

return array
(
  'fields' => array
  (
    'firstname' => array
    (
      'required'        => array
      (
        'msg'           => 'Firstname can not be left blank'
      ),
      'StringValidator' => array
      (
        'min'           => 3,
        'min_error'     => 'Firstname is too short (min 3 characters)',
        'max'           => 10,
        'max_error'     => 'Firstname is too long (max 10 characters)'
      )
    ),
    'url' => array
    (
      'UrlValidator'    => array
      (
        'url_error'     => 'This URL is invalid'
      )
    )
  )
);
<?php pre_end() ?>

<h2>Using Custom Validators</h2>

<p> Custom validators can be called through the builtin <b>CallbackValidator</b>:

<?php pre_start('info') ?>
&lt;?php
// In the validator configuration file:
return array
(
  'fields' => array
  (
    <em>...</em>

    // Call a custom validator method
    'description' => array
    (
      'CallbackValidator' => array
      (
        'callback'        => array('myappValidators', 'validateTextarea'),
        'invalid_error'   => 'Description can not use HTML tags.'
      )
    ),

    <em>...</em>
  )
);
<?php pre_end() ?>

<p> A custom validator class can regroup validators that are used by the application
    (remember to add this class to the 'autoload_classes' in <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?>):

<?php pre_start() ?>
&lt;?php
/**
 * Validators used by SweetApp application.
 * 
 * @package    SweetApp
 * @subpackage Validators
 * @author     Fabrice Denis
 */

class <var>myapp</var>Validators
{
    /**
     * Valid text content can not contain any HTML tags.
     * 
     * @param  mixed Data to validate
     * @return bool  True if validates, false otherwise.
     */
    public static function validate<var>Textarea</var>($value)
    {
        return  (strip_tags($value)==$value);
    }
}
<?php pre_end() ?>



<h2>Displaying Error Messages in the Form</h2>

<p> Here is an example of a simple helper function to output all errors that have been
  set on the Request object (errors can be set by validation, but also directly from
  the action):

<?php pre_start() ?>
// In a custom Helper file
function form_errors()
{
  $request = coreContext::getInstance()->getRequest();
  $s = '';
  if($request->hasErrors())
  {
    $s .= '&lt;div class="forms-errors">';
    foreach($request->getErrors() as $key => $message)
    {
      $s = $s . $message . "&lt;br/>\n";
    }
    $s .= '&lt;/div>';
  }
  return $s;
}

// In the template file
&lt;?php echo form_errors() ?>
<?php pre_end() ?>


<h2>Repopulating the Form</h2>
