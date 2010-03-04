<h2>coreUserBasicSecurity</h2>

<p> The coreUserBasicSecurity class extends <b>coreUser</b> with methods for
    manipulating credentials and authenticating users. See the <?php echo link_to('Security demo', 'test/securitydemo') ?>.

<p> This class extends <?php echo link_to('coreUser', 'doc/core?include_name=user') ?> with the following methods:

<?php pre_start() ?>
// Clears all credentials.
function clearCredentials()

// Returns an array containing the user's credentials
function listCredentials()

// Removes a credential.
function removeCredential($credential)

// Adds credentials.
// Both functions can take one or multiple seaprated arguments, or an array
function addCredential($credential, <em>...</em>)
function addCredentials($credential, <em>...</em>)

// Returns true if user has credential.
// If only one credential in the given array is required, set $useAnd to false
function hasCredential($credentials, $useAnd = true)

// Returns true if user is authenticated.
function isAuthenticated()

// Sets authentication for user.
function setAuthenticated($authenticated)
<?php pre_end() ?>

<h2>Action Security</h2>

<p> Before being executed, every action passes by a special filter that checks if the
    current user has the privileges to access the requested action.
	The privileges are composed of two parts:
<ul>
	<li>Secure actions require users to be authenticated.
	<li>Credentials are named security privileges that allow organizing security by group.
</ul>

<p> Restricting access to an action is simply made by creating and editing a configuration
    file called <var>security.config.php</var> in the module <var>config/</var> directory.
	
<p> Here is a sample security config file. The keys are action names, and the <b>all</b>
    key applies to all actions in the module:

<?php pre_start('info') ?>
&lt;?php
return array
(
  'read' => array
  (
     'is_secure'   => false           # All users can request the read action
  ),

  'update' => array
  (
    'is_secure'   => true             # The update action is only for authenticated users
  ),
  
  'delete' => array
  (
    'is_secure'   => true,            # Only for authenticated users
    'credentials' => array('admin')   # With the admin credential
  ),
  
  'all' => array
  (
    'is_secure'   => false            # It is off by default anyway
  )
);
<?php pre_end() ?>

<h2>Default Security Actions</h2>

<p> What happens when a user tries to access a restricted action depends on his credentials:

<ul>
	<li>If the user is authenticated and has the proper credentials, the action is executed.
	<li>If the user is not identified, he will be redirected to the default <b>login action</b>.
	<li>If the user is identified but doesn't have the proper credentials,
	    he will be redirected to the default <b>secure action</b>.
</ul>

<p> The default security actions are defined in the <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?> file:

<?php pre_start('info') ?>
# Forward here when a non-authenticated user tries to access a secure page
'login_module'  => 'default',
'login_action'  => 'login',

# Forward here when a user doesn't have the credentials required for an action
'secure_module' => 'default',
'secure_action' => 'secure',
<?php pre_end() ?>


<h2>Granting Access</h2>

<p> The authenticated status of the user is set by the <b>setAuthenticated()</b> method
    and can be checked with <b>isAuthenticated()</b>:
	
<?php pre_start() ?>
class myAccountActions extends coreActions
{
  public function executeLogin($request)
  {
    if ($request->getParameter('login') == 'foobar')
    {
      $this->getUser()->setAuthenticated(true);
    }
  }
 
  public function executeLogout()
  {
    $this->getUser()->setAuthenticated(false);
  }
}
<?php pre_end() ?>

<h2>Managing Credentials</h2>

<p> Credentials are a bit more complex to deal with, since you can check, add, remove, and clear credentials:

<?php pre_start() ?>
class myAccountActions extends coreActions
{
  public function executeDoThingsWithCredentials()
  {
    $user = $this->getUser();
 
    // Add one or more credentials
    $user->addCredential('foo');
    $user->addCredentials('foo', 'bar');
 
    // Check if the user has a credential
    echo $user->hasCredential('foo');                      =>   true
 
    // Check if the user has both credentials
    echo $user->hasCredential(array('foo', 'bar'));        =>   true
 
    // Check if the user has one of the credentials
    echo $user->hasCredential(array('foo', 'bar'), false); =>   true
 
    // Remove a credential
    $user->removeCredential('foo');
    echo $user->hasCredential('foo');                      =>   false
 
    // Remove all credentials (useful in the logout process)
    $user->clearCredentials();
    echo $user->hasCredential('bar');                      =>   false
  }
}
<?php pre_end() ?>

<h2>Dealing with User Credentials in a Template</h2>

<p> Credentials can also be used to display only authorized content in a template:

<?php pre_start('info') ?>
&lt;ul>
  &lt;li>&lt;?php echo link_to('section1', 'content/section1') ?>&lt;/li>
  &lt;li>&lt;?php echo link_to('section2', 'content/section2') ?>&lt;/li>
  &lt;?php if ($_user->hasCredential('section3')): ?>
    &lt;li>&lt;?php echo link_to('section3', 'content/section3') ?>&lt;/li>
  &lt;?php endif; ?>
&lt;/ul>
<?php pre_end() ?>
