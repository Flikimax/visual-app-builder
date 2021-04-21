<?php
/*
@package   Visual_App_Builder
@author    Flikimax <info@flikimax.com>
@link      flikimax.com
@copyright 2021 Flikimax
@wordpress-plugin
Plugin Name:    Visual App Builder
Plugin URI:     flikimax.com
Description:    Visual application builder.
Version:        1.0
Author:         Flikimax
Author URI:     Flikimax.com
Text Domain:    visual-app-builder
Domain Path:    /languages
*/

if( !defined( 'ABSPATH') ) exit(); 

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
      // TODO: VALIDAR SI EL USUARIO ACTUAL fkm_get_currentUser() inicia con fkm_
      // remove_submenu_page('upload.php', 'upload.php');    


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
    define('FKM_VERSION', time());
  }

  # LINK INFO PLUGIN
  public function fkm_settings_link ($links){
    $settings_link = array(
        'settings' => '<a href="' . admin_url('admin.php?page=MyPluginPage' ) . '" style="font-weight: bold; color: green;">' .
                        __('Settings Page', 'visual-app-builder') .
                      '</a>',
    );
    return array_merge($links, $settings_link);
  }
  # LINKS EXTRA DESCRIPCION PLUGIN
  public function fkm_filter_row_meta_plugin($links, $plugin_file){
    if ($plugin_file == plugin_basename(__FILE__)){
      $links[] = "<a href='flikimax.com/docs' target='_blank'>" . __('Documentation') . "</a>";
      // $links[] = "<div><a href='flikimax.com/docs' target='_blank'>" . __('Documentation') . "</a></div>";
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
