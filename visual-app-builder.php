<?php
/*
@wordpress-plugin
Plugin Name:    Visual App Builder
Plugin URI:     flikimax.com
Description:    Visual application builder.
Version:        1.0
Author:         Flikimax
Author URI:     Flikimax.com 
*/

if (!defined('ABSPATH')) exit(); 

class Fkm_VisualAppBuilder {
  function __construct(){
    $this->fkm_create_paths();
    add_action('init', array($this, 'fkm_init'), 0);
  }

  function fkm_init(){
    require_once FKM_PLUGIN_PATH . '/admin/admin_page.php';  # CREAR LAS ADMIN PAGE
    require_once FKM_PLUGIN_PATH . '/admin/userManager.php'; # GESTIONA LOS USUARIOS (ROLES - CAPACIBILITY)
    require_once FKM_PLUGIN_PATH . '/admin/post_type.php';   # CREAR EL POST TYPE
   
    if (is_admin()) {
      new Fkm_AdminPage(); # SE CREAN LAS ADMIN PAGE
      add_action('admin_init', array($this, 'fkm_admin_init'));
    } 
  }


  function fkm_admin_init() {
    # DATOS EXTRA PARA EL PLUGIN
    add_filter('plugin_row_meta', array($this, 'fkm_filter_row_meta_plugin'), 10, 2); # LINKS EXTRA DESCRIPCION PLUGIN
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'fkm_settings_link')); # LINK INFO PLUGIN
  }


  # CONFIGURACIONES / ACTIVACION / DESACTIVACION / PLUGINS
  public function fkm_create_paths(){
    define('FKM_VAB', 'Visual App Builder');
    define('FKM_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define('FKM_TEXT_DOMAIN', 'visual-app-builder');
    # PLUGIN VERSION
    $plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
    $plugin_version = $plugin_data['Version'];
    define('FKM_VERSION', time()); # $plugin_version
  }

  # LINK INFO PLUGIN
  public function fkm_settings_link ($links){
    $settings_link = array(
        'settings' => '<a href="' . admin_url('admin.php?page=' . FKM_TEXT_DOMAIN) . '" style="font-weight: bold; color: green;">' . __('VAB', 'visual-app-builder') . '</a>',
    );
    return array_merge($links, $settings_link);
  }
  # LINKS EXTRA DESCRIPCION PLUGIN
  public function fkm_filter_row_meta_plugin($links, $plugin_file){
    if ($plugin_file == plugin_basename(__FILE__)){
      $links[] = "<a href='https://flikimax.com/visual-app-builder' target='_blank'>" . __('Documentation') . "</a>";
    }
    return $links;
  }


  # ACCION ACTIVAR EL PLUGIN
  public function fkm_activation (){
    flush_rewrite_rules();
  }
  # ACCION DESACTIVAR EL PLUGIN
  public function fkm_deactivation (){
    flush_rewrite_rules();
  }
}

new Fkm_VisualAppBuilder;
