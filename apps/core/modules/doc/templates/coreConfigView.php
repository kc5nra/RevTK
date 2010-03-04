<h2>coreConfig</h2>

<p> coreConfig stores all configuration information for the application.

<p> Note that all settings configured in <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?> are available through coreConfig.

<?php pre_start() ?>
// Retrieves a config parameter
::get($name, $default = null)

// Indicates whether or not a config parameter exists.
::function has($name)

// Sets a config parameter.
::set($name, $value)

// Sets an array of config parameters.
::add($parameters = array())

// Retrieves all configuration parameters.
::getAll()

// Clears all current config parameters.
::clear()
<?php pre_end() ?>

<h2>Default Configuration Values</h2>

<p> These values are set during the application initialization. All of these settings can be
    specified in settings.php and will overwrite the default values!

<?php pre_start('info') ?>
# Toggle between development and production modes
debug                      # defaults to CORE_DEBUG (front controller)

# All directories are derived from the front controller's CORE_ROOT_DIR value
root_dir                   # defaults to CORE_ROOT_DIR (front controller)
core_dir                   # defaults to the path of <b>core.php</b>
apps_dir                   # defaults to CORE_ROOT_DIR/apps
lib_dir                    # defaults to CORE_ROOT_DIR/lib
config_dir                 # defaults to CORE_ROOT_DIR/config

# Application directories
app_dir                    # defaults to CORE_ROOT_DIR/apps/[appname]
app_config_dir             # defaults to CORE_ROOT_DIR/apps/[appname]/config
app_lib_dir                # defaults to CORE_ROOT_DIR/apps/[appname]/lib
app_module_dir             # defaults to CORE_ROOT_DIR/apps/[appname]/modules
app_template_dir           # defaults to CORE_ROOT_DIR/apps/[appname]/templates

sf_web_dir                 # defaults to CORE_ROOT_DIR/web
sf_upload_dir              # defaults to CORE_ROOT_DIR/web/uploads
sf_cache_dir               # cache root directory (sfCache)
sf_root_dir                # project root directory
sf_symfony_lib_dir         # where to autoload symfony classes from
sf_charset                 # defaults to 'utf-8'
sf_test                    # defaults to CORE_DEBUG (front controller)
sf_debug                   # defaults to CORE_DEBUG (front controller)
sf_compressed              # defaults to enabled if support detected
<?php pre_end() ?>
<h2>Example</h2>

<p> These are all current coreConfig settings:
<?php pre_start('printr'); print_r(coreConfig::getAll()); pre_end() ?>

