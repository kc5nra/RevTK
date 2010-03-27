<h2>Administrator Area</h2>

<p> This page requires the <b>admin</b> credential to be accessed.

<ul>
<li> You are authenticated: <samp><?php echo $_user->isAuthenticated() ? 'Yes' : 'No' ?></samp>.

<li> Your credentials: <samp><?php
  $credentials = $_user->listCredentials();
  echo empty($credentials) ? 'None' : implode(', ', $credentials) ?></samp>.

<li> Credentials required by this action: <samp><?php 
  $credentials = coreContext::getInstance()->getActionInstance()->getCredential();
  echo is_null($credentials) ? 'None' : implode(', ', $credentials) ?></samp>.
</ul>

<p> Go <?php echo link_to('back', 'test/securitydemo') ?>.