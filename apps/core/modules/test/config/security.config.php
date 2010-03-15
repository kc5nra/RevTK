<?php
/**
 * Sample security configuration file.
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

return array
(
  /**
   * Settings for the test/security action
   * 
   */
  'securitydemo' => array
  (
    'is_secure'    => false
  ),
  
  'securityadmin' => array
  (
    'is_secure'    => true,
    'credentials'  => array('admin')
  ),
  
);
