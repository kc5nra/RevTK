<?php
/**
 * Core documentation configuration file.
 * 
 * All settings here become available through coreConfig.
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

return array
(
  'all' => array
  (
  /**
   * Controls the appearance of the front web controller in the url
   * It is usually on for the production environment of your main application and off for the others.
   */
  'no_script_name' => false,

    /**
     * Helpers included in all templates by default
     */
    'standard_helpers' => array(
    'Doc',    # Highlighting of code blocks in the documentation
    'Partial'
  ),

  /**
   * Database connection parameters (not used by the Core app).
   */
  'database_connection' => array
  (
    'database'       => 'dummy',
    'host'           => 'localhost',
    'username'       => 'root',
    'password'       => '',
    'set_names_utf8' => true
  ),

  /**
   * Specify where the libraries are to be loaded from
   * (for performance reason, saves scanning through lib/ directories)
   * 
   * For each classname, the subdirectory from the coreConfig::get('root_dir') without trailing slash.
   * The included filename will be the classname plus 'php' extension.
   * 
   * The class name can be a regular expression, which is very useful when
     * using a naming convention, for example: '/^my[A-Z]/' => 'apps/myApp/myLibs'
   */
  'autoload_classes'  => array
  (
    // other demo classes
    '/^demo[A-Z]/' => 'apps/Core/lib'
  ),

  /**
   * Factories - here you can redefine (and extend) classes used by the framework.
   * 
   * Remember to add your custom class to the "autoload_classes" setting.
   * 
   * Configurable factories [default]:
   *     user   [coreBasicUserSecurity]
   */
  'core_factories'  => array
  (
    'user'       => array('class' => 'demoUser'),
    'response'  => array('class' => 'demoWebResponse')
  ),

  /**
   * Default login and secure pages:
   * 
   * - If the user is not identified, he will be redirected to the default login action.
   * - If the user is identified but doesn't have the proper credentials,
   *   he will be redirected to the default secure action ("credentials required")
   */
  'login_module'  => 'test',
  'login_action'  => 'securitylogin',
  'secure_module' => 'test',
  'secure_action' => 'securitysecure',

  /**
   * Default Error 404 page.
   */
  'error_404_module' => 'default',
  'error_404_action' => 'error404',

  /**
   * Application-level view configuration.
   * 
   */
  'default_view_configuration' => array
  (
    'layout'              => 'layout',
    'title'               => 'Core framework',
    'metas'         => array
    (
      'content-type'      => 'text/html',
      'content-language'  => 'en'
    ),
    'stylesheets'    => array
    (
      '/css/core/core.css'
    )
  ),

  /**
   * Routes rules for the front controller, and the link helpers.
   *
   */
  'routing_config' => array
  (
    'routes' => array(
      'testurlencoding'   => array(
        'url'  => '/test/urlencoding',
        'param'  => array( 'module' => 'test', 'action' => 'urlEncoding' )
      ),
    
      'utf8_test' => array(
        'url'   => '/utf8/:japanese',
        'param'  => array( 'module' => 'utf8', 'action' => 'index', 'japanese' => '[empty]' )
      ),

      'news_archive_y_m' => array(
        'url'  => '/news/:year/:month/:day',
        'param'  => array( 'module' => 'news', 'action' => 'index', 'year' => 1984, 'month' => 1, 'day' => 1 )
      ),

      /**
       * coreJs documentation
       * 
       *  /core.php/doc/corejs/ui/ajaxrequest
       */ 
      'doc_corejs' => array(
        'url'   => '/documentation/corejs/:cat/:subcat',
        'param' => array( 'module' => 'corejs', 'action' => 'index', 'cat' => 'index', 'subcat' => '' )
      ),

      'doc_misc'   => array(
        'url'   => '/documentation/misc/:page_id',
        'param'  => array( 'module' => 'doc', 'action' => 'misc' )
      ),

      'doc_lib'   => array(
        'url'  => '/documentation/lib/:page_id',
        'param'  => array( 'module' => 'doc', 'action' => 'lib' )
      ),

      'doc_helper' => array(
        'url'  => '/documentation/helpers/:helper_name',
        'param'  => array( 'module' => 'doc', 'action' => 'helper', 'helper_name' => 'Index' )
      ),

      'doc_core'   => array(
        'url'  => '/documentation/core/:include_name',
        'param'  => array( 'module' => 'doc', 'action' => 'core', 'include_name' => 'Index' )
      ),

      'layout'   => array(
        'url'  => '/test/layout/:layout_name',
        'param'  => array( 'module' => 'test', 'action' => 'layout', 'layout_name' => 'default' )
      ),
      
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
