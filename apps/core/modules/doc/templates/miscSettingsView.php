<h2>Settings</h2>

<p> Each parameter defined in <b>settings.php</b> is accessible from inside the PHP code via the <?php echo link_to('coreConfig', 'doc/core?include_name=config') ?> class.

<p> Here are all the settings recognized by the Core framework.
    Note how all the settings are grouped under the key <samp>all</samp>, see <a href="#environments">Environments</a>.

<?php pre_start() ?>
&lt;?php
return array
(
  'all' => array
  (
    # Controls the appearance of the front controller script in the url
    'no_script_name' => false,
    
    # Helpers included in all templates by default
    'standard_helpers' => array
    (
      'Partial', 'Form'
    ),
    
    # Configure application classes ready for autoload
    # For each class, specify a path relative to the config 'root_dir', without trailing slash.
    # The included filename will be the classname plus 'php' extension.
    # The class name can be a regular expresion, which is very useful when
    # using a naming convention, for example: '/my[A-Z]\w+/' => 'apps/myApp/myLibs'
    'autoload_classes'  => array
    (
      'utf8'      => 'lib/core/lib'
    ),
    
    # Factories - here you can redefine (and extend) classes used by the framework.
    # Remember to add your custom class to the "autoload_classes" setting.
    # 
    # Configurable factories [default]:
    #     user   [coreBasicUserSecurity]
    'core_factories' => array
    (
      'user'         => array
      (
        'class'      => 'myUser'
      )
    ),

    # Database connection, see coreDatabase
    'database_connection' => array
    (
      'database'       => 'dummy',
      'host'           => 'localhost',
      'username'       => 'root',
      'password'       => '',
      
      # If true, execute "SET NAMES 'utf8'" after opening the connection
      'set_names_utf8' => true,
    
      # Used to get SQL Date/Time adjusted to the timezone of the users
      'server_timezone' => -6
    ),

    # Session cookie parameters (options for coreSessionStorage).
    'core_session_params' => array
    (
      # the session cookie name (define it if using multiple applications
      # from the same subdomain/domain range)
      'session_name'          => 'core',
      # calls session_id() if defined
      'session_id'            => null,
      # call session_start() always or when needed
      'auto_start'            => true,
      
      # Parameters for session_set_cookie_params()
      # Defaults to session_get_cookie_params() (php.ini settings)
      
      # lifetime of the cookie in seconds (integer)
      'session_cookie_lifetime' => 0,
      # (string) cookie path
      'session_cookie_path'     => '/',
      # (string) use a subdomain to restrict cookie to a subdomain
      'session_cookie_domain'   => 'subdomain.domain.com',
      # (boolean) true = cookies sent only over https connection
      'session_cookie_secure'   => false,
      # (boolean) true = http only cookie, not readable by javascript
      'session_cookie_httponly' => false
    ),
    
    # To be called when a 404 error is raised,
    # or when the requested URL doesn't match any route
    'error_404_module' => 'default',
    'error_404_action' => 'error404',
    
    # To be called when a non-authenticated user
    # tries to access a secure page
    'login_module'  => 'test',
    'login_action'  => 'securitylogin',
    
    # To be called when a user doesn't have
    # the credentials required for an action
    'secure_module' => 'test',
    'secure_action' => 'securitysecure',
    
    # Default view configuration settings for all pages
    'default_view_configuration' => array
    (
      'layout'              => 'layout',
      'title'               => 'App default page title',
      'metas' => array
      (
        'content-type'    => 'text/html',
        'content-language'  => 'en',
        'description'    => 'Core project description',
      ),
      'stylesheets'      => array
      (
        '/css/core/core.css'
      )
    ),
    
    # Routing configuration
    'routing_config' => array
    (
      'routes' => array(
        'homepage'   => array(
          'url'  => '/',
          'param'  => array( 'module' => 'default', 'action' => 'index' )
        ),
    
        'default_index' => array(
          'url'  => '/:module',
          'param'  => array( 'action' => 'index' )
        ),
        
        'default'   => array(
          'url'  => '/:module/:action/*'
        )
      )
    )
  )
);
<?php pre_end() ?>


<h2>Custom Application Settings</h2>

<p> Additional settings can be added to the settings file and will be available through the coreConfig class.

<p> The convention is to add these at the end of the settings file, and use a <b>app_</b> prefix
  for each setting, for example:

<?php pre_start() ?>
  /**
   * SweetApp application settings.
   * 
   */
   
  // cookie salt
  'app_cookie_salt'      => 'tHiS.iz.SpaRta!',
  
  // cookie lifetime (in seconds)
  'app_cookie_expire'    => 60*60*24*15
<?php pre_end() ?>


<h2 id='environments'>Environments</h2>

<p> An application can run in various environments.
    The different environments share the same PHP code (apart from the front controller),
  but can have completely different configurations.

<p> For example, the dev and test environments may need test data, stored in a database distinct from 
    the one used in the production environment. So the database configuration will be different between 
  the two environments.

<p> To change the environment in which you're browsing your application, just change the front controller:

<?php pre_start() ?>
// Call the development front controller:
http://localhost/frontend_dev.php/mymodule/index

// Call the production front controller:
http://localhost/index.php/mymodule/index

// With mod_rewrite, .htaccess file and the 'no_script_name' setting set to true,
// the production front controller becomes the default execution script:
http://localhost/mymodule/index
<?php pre_end() ?>

<h3>The configuration cascade</h3>

<p> By default, all settings should be grouped under the <samp>all</samp> property.

<p> To create environment configurations, simply add a property with the environment name
    and redefine any of the settings, or add new ones:

<p> The constant <samp>CORE_ENVIRONMENT</samp> in the front controller will determine what
    settings are available to the application.
  
<?php pre_start('info') ?>
return array
(
  # PRODUCTION environment
  'prod' => array
  (
    'no_script_name' => true
    # etc...
  ),
  
  # DEVELOPMENT environment
  'dev' => array
  (
    # change the database settings
    'database_connection' => array
    (
      'database'       => 'test',
      'host'           => 'localhost',
      # etc...
    )
  ),

  'all' => array
  (
    'no_script_name' => false
    # etc...
  )
);
<?php pre_end() ?>



<h2>Factories</h2>

<p> A factory is the definition of a class for a certain task. The default Core framework
  factories are defined in <b>coreContext</b>. You can override the default factories
  with the <b>core_factories</b> setting. For each configurable factory, you can provide
  a custom class that will be used by the framework instead of the Core class.

<p> These are the configurable factories, the setting name, and the class that you need
    to extend if replacing the Core class:

<ul>
  <li><b>user</b> : extend <em>coreUserBasicSecurity</em></li>
  <li><b>request</b> : extend <em>coreWebRequest</em></li>
  <li><b>response</b> : extend <em>coreWebResponse</em></li>
</ul>

<p> Here we replace the user factory (coreUserBasicSecurity) with myUser:

<?php pre_start('info') ?>
// myUser.php
class myUser extends coreUserBasicSecurity
{
  public function initialize(coreSessionStorage $storage, $options = array())
  {
    parent::initialize($storage, $options);
  
    // Add our initialization code...
  }

  // Add our methods...

  public function signIn($signInName, $credentials = array())
  {
    $this->setAttribute('name', $signInName);
    $this->addCredentials($credentials);
    $this->setAuthenticated(true);
  }
  
  public function signOut()
  {
    $this->clearCredentials();
    $this->setAuthenticated(false);
  }
}

// settings.php
'core_factories' => array
(
  'user' => array
  (
    'class' => '<em>myUser</em>'
  )
)
<?php pre_end() ?>
