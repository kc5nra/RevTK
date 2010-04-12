<?php

/**
 * Reviewing the Kanji - Application settings file.
 *
 * All settings here become available through coreConfig.
 *
 * @package    RevTK
 * @subpackage Config
 * @author     Fabrice Denis
 */

return array
(
  /**
   * TEST (local host, debug OFF, analytics tracking OFF)
   */
  'test' => array
  (
    'no_script_name' => true
  ),

  /**
   * DEV (local host, debug ON, analytics tracking OFF)
   */
  'dev' => array
  (
    'no_script_name' => false,

    'default_view_configuration' => array
    (
      'metas' => array
      (
        'robots' => 'NONE'
      )
    )
  ),

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
      'Partial'
    ),

    /**
     * Database connection parameters.
     */
    'database_connection' => array
    (
      'database'       => 'local_database_name',
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
      '/^rtk[A-Z]/'   => 'apps/revtk/lib',
      '/^ui[A-Z]/'    => 'lib/uiparts',
      'ExportCSV'     => 'lib/export'
    ),


    /**
     * Factories - here you can redefine (and extend) classes used by the framework.
     *
     * Remember to add your custom class to the "autoload_classes" setting.
     *
     * Configurable factories [default]:
     *     user   [coreBasicUserSecurity]
     */
    'core_factories'  => array(
      'user'       => array('class' => 'rtkUser'),
      'response'    => array('class' => 'rtkWebResponse')
    ),

    /**
     * Default Error 404 page.
     */
    'error_404_module' => 'default',
    'error_404_action' => 'error404',

    /**
     * Default login and secure pages:
     *
     * - If the user is not identified, he will be redirected to the default login action.
     * - If the user is identified but doesn't have the proper credentials,
     *   he will be redirected to the default secure action ("credentials required")
     */
    'login_module'  => 'home',
    'login_action'  => 'login',

    'secure_module' => 'default',
    'secure_action' => 'secure',

    /**
     * Application-level view configuration.
     *
     */
    'default_view_configuration' => array
    (
      'layout'              => 'layout',
      'title'               => 'Reviewing the Kanji',
      'metas' => array
      (
        'content-type'    => 'text/html',
        'Content-Language'  => 'en-us',
        'description'       => 'A flashcard reviewing application for Japanese learners, using James Heisig\'s "Remembering the Kanji" method.'
      ),
      'stylesheets'      => array
      (
        '/css/1.0/main.css'
      )
    ),

    /**
     * Routes rules for the front controller, and the link helpers.
     *
     */
    'routing_config' => array
    (
      'routes' => array
      (
        'manage' => array(
          'url'          => '/manage',
          'param'        => array( 'module' => 'manage', 'action' => 'index')
        ),

        'study_edit' => array(
          'url'          => '/study/kanji/:id',
          'param'        => array( 'module' => 'study', 'action' => 'edit', 'id' => ''),
          'requirements' => array( 'id' => '[^/]+' ) // matches dots in keywords
        ),

        'review_summary' => array(
          'url'          => '/review/summary',
          'param'        => array( 'module' => 'review', 'action' => 'summary')
        ),

        'review' => array(
          'url'          => '/review',
          'param'        => array( 'module' => 'review', 'action' => 'review')
        ),

        'overview' => array(
          'url'          => '/main',
          'param'        => array( 'module' => 'review', 'action' => 'index')
        ),

        'news_by_id' => array(
          'url'          => '/news/id/:id',
          'param'        => array( 'module' => 'news', 'action' => 'detail', 'id' => 0),
          'requirements' => array( 'id' => '\d+' )
        ),

        'news_by_yyyymm' => array(
          'url'          => '/news/:year/:month',
          'param'        => array('module' => 'news', 'action' => 'index', 'year' => 0, 'month' => 0),
          'requirements' => array('year' => '\d{4}', 'month' => '\d+')
        ),

        'go_to_backend' => array(
          'url'   => '/admin',
          'param' => array( 'module' => 'default', 'action' => 'GoToBackend')
        ),

        'sightreading' => array(
          'url'   => '/sightreading',
          'param' => array( 'module' => 'misc', 'action' => 'reading' )
        ),

        'progress'  => array(
          'url'   => '/progress',
          'param' => array( 'module' => 'member', 'action' => 'progress' )
        ),

        'profile'   => array(
          'url'   => '/profile/:username',
          'param' => array( 'module' => 'profile', 'username' => '' )
        ),

        'login'     => array(
          'url'   => '/login',
          'param' => array( 'module' => 'home', 'action' => 'login' )
        ),
        'logout'    => array(
          'url'   => '/logout',
          'param' => array( 'module' => 'home', 'action' => 'logout' )
        ),

        'forgot_password' => array(
          'url'   => '/forgot_password',
          'param' => array( 'module' => 'account', 'action' => 'forgotPassword' )
        ),

        'members_list' => array(
          'url'   => '/members',
          'param' => array( 'module' => 'home', 'action' => 'memberslist' )
        ),

        'contact'   => array(
          'url'   => '/contact',
          'param' => array( 'module' => 'home', 'action' => 'contact' )
        ),

        'about'     => array(
          'url'   => '/about',
          'param' => array( 'module' => 'about' )
        ),

        'learnmore'     => array(
          'url'   => '/learnmore',
          'param' => array( 'module' => 'about', 'action' => 'learnmore' )
        ),

        'homepage'   => array(
          'url'  => '/',
          'param'  => array( 'module' => 'home', 'action' => 'index' )
        ),

        'default_index' => array(
          'url'  => '/:module',
          'param'  => array( 'action' => 'index' )
        ),

        'default'   => array(
          'url'  => '/:module/:action/*'
        )
      )
    ),

    /**
     * RevTK custom application settings.
     *
     * Some settings are defaults for DEV and TEST environments and must
     * be redefined for the STAGING and PROD environments.
     *
     */

    // The full URL for the application
    'app_website_url'        => 'http://kanji.dev.localhost',

    // The full URL for the backend (appears in navigation when admin), comment out to disable
    //'app_backend_url'        => 'http://kanji.dev.localhost/backend',

    // The full URL for the community forums (appears in navigation), comment out to disable
    //'app_forum_url'          => 'http://forum.dev.localhost',

    // Server timezone to adjust MySQL time to the local time of the user
    'app_server_timezone'    => 0,

    // from (email, name) for automatic mailings (registration, password change, ...)
    'app_email_robot'        => array('email' => 'email-robot@localhost', 'name' => 'Reviewing the Kanji'),
    // to   (email, name) for contact page form
    'app_email_feedback_to'  => array('email' => 'feedback@localhost', 'name' => 'DestName')
  )
);