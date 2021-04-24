<?php
class Fkm_AdminPage {
  function __construct(){
    add_action('admin_menu', array($this, 'fkm_admin_menu'));
    add_action('parent_file', array($this, 'fkm_add_pageTaxonomy_to_vab') );
    # DO IN THE FUTURE: CREAR EL POST TYPE PARA EL FrontEnd USANDO - rewrite - with_front = TRUE
  }

  public function fkm_vab(){ ?>
    <h1 id="<?=FKM_TEXT_DOMAIN; ?>-root">
      <?=FKM_VAB; ?>
    </h1>
    <?php
  }

  public function fkm_vab_settings(){
    ?>
    <div class="">
      <h1>Settings</h1>
    </div>
    <?php
  }


  # SE ENCARGA DE CARGAR METODOS DEL ADMIN PAGE PRINCIPAL
  public function fkm_vab_admin_page_content() {
    $admin_page = str_replace('-', '_', $_GET['page']);
    (method_exists($this, $admin_page)) ? $this->{$admin_page}() : $this->fkm_vab();
  }

  # MUESTRA EL CONTENIDO DE LAS ADMIN PAGE USER
  public function fkm_user_admin_page_content(){
    // echo $slug = (isset($_GET['content'])) ? $_GET['content'] : $_GET['page'];
    
    # CARGAR OTRA PAGINA
    if (isset($_GET['content'])){
      $args = array(
        'name'        => $_GET['content'],
        'post_type'   => 'visual_app_builder',
        'post_status' => 'publish',
        'numberposts' => 1,
      );
      $admin_page_content = get_posts($args);
      # GET POST - CONTENT
      $content = (isset($admin_page_content[0]->post_content)) ? $admin_page_content[0]->post_content : null;
    } else { # CARGAR LA PAGINA PRE - DEFINIDA EN CREATE ADMIN PAGES
      $page = get_terms([
        'taxonomy'   => 'admin_page_category',
        'hide_empty' => false,
        'slug'       => $_GET['page']
      ])[0];
      $admin_page_content = get_term_meta($page->term_id, 'admin_page_content', true);
      # GET POST - CONTENT
      $content_post = get_post($admin_page_content);
      $content = (isset($content_post->post_content)) ? $content_post->post_content : null;
    }

    if ($content){  
      $content = apply_filters('the_content', $content); ?>
      <div class="contenedor-<?=FKM_TEXT_DOMAIN; ?>">
        <?=$content; ?>
      </div>
      <?php
    } else { 
      $user = wp_get_current_user();
      if (in_array('administrator', (array) $user->roles)) { ?>
        <div>
          <h3>Puedes agregar contenido <a href="<?=admin_url('edit.php?post_type=visual_app_builder'); ?>" >aquí</a>.</h3>
        </div>
        <?php
      } else {
        ?>
        <div>
          <h3>No hay contenido asignado a esta admin page.</a></h3>
        </div>
        <?php
      }
      
      
    } 
  }



  # CREAR ADMIN PAGE
  public function fkm_admin_menu() {
    $this->fkm_remove_admin_page();
    $this->fkm_vab_admin_page(); # ADMIN PAGE VAB
    $this->fkm_user_admin_page(); # ADMIN PAGE USER
  }

  # CREACION DE LAS ADMIN PAGE PRINCIPALES
  public function fkm_vab_admin_page(){
    add_menu_page(
      __( FKM_VAB, 'visual-app-builder' ),
      __( FKM_VAB, 'visual-app-builder' ),
      'administrator', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
      FKM_TEXT_DOMAIN,
      array($this, 'fkm_vab_admin_page_content'),
      'dashicons-schedule',
      3
    );
    add_submenu_page(
      FKM_TEXT_DOMAIN,
      __( FKM_VAB, 'visual-app-builder' ),
      __( FKM_VAB, 'visual-app-builder' ),
      'administrator', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
      FKM_TEXT_DOMAIN,
      array($this, 'fkm_vab_admin_page_content'),
      -1
    );

    add_submenu_page(
      FKM_TEXT_DOMAIN,
      __( 'Create admin pages', FKM_TEXT_DOMAIN ),
      __( 'Create admin pages', FKM_TEXT_DOMAIN ),
      'administrator', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
      'edit-tags.php?taxonomy=admin_page_category',
      null,
      3
    );
    # TODO: MEJORA A FUTURO
    // add_submenu_page(
    //   FKM_TEXT_DOMAIN,
    //   __('Settings', 'visual-app-builder'),
    //   __('Settings', 'visual-app-builder'),
    //   'administrator', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
    //   FKM_TEXT_DOMAIN . '-settings',
    //   array($this, 'fkm_vab_admin_page_content'),
    //   4
    // );
  }
  # SE AGREGA LA SUB ADMIN PAGE TAXONOMY A LA ADMIN PAGE PRICIPAL DE VAB
  public function fkm_add_pageTaxonomy_to_vab($parent_file){
    global $current_screen;
    $taxonomy = $current_screen->taxonomy;
    if ($taxonomy == 'admin_page_category'){
      $parent_file = FKM_TEXT_DOMAIN;
    }
    return $parent_file;
  }

  # CREACION DE LAS ADMIN PAGES CREADAS POR EL USUARIO ATRAVES DE LA TAXONOMIA
  public function fkm_user_admin_page(){
    # ADMIN PAGE USER
    add_action('admin_enqueue_scripts', array($this, 'fkm_user_admin_page_content_style'));
    $parent_admin_pages = get_terms([
      'taxonomy' => 'admin_page_category',
      'hide_empty' => false,
      'parent' => 0
    ]);

    foreach ($parent_admin_pages as $parent_admin_pages){
      # SI EXISTE EL VALOR, SE TRAE
      $admin_page_permissions = get_term_meta($parent_admin_pages->term_id, 'admin_page_permissions', true);
      $admin_page_icon        = get_term_meta($parent_admin_pages->term_id, 'admin_page_icon', true);
      $admin_page_position    = get_term_meta($parent_admin_pages->term_id, 'admin_page_position', true);
      # SE PREPARAN LOS DATOS
      $admin_page_permissions = (!empty($admin_page_permissions)) ? $admin_page_permissions : 'administrator';
      $admin_page_icon        = (!empty($admin_page_icon)) ? "dashicons-{$admin_page_icon}" : '';
      $admin_page_position    = (!empty($admin_page_position)) ? $admin_page_position : null; 

      add_menu_page(
        __( $parent_admin_pages->name, 'visual-app-builder' ), # PAGE TITLE
        __( $parent_admin_pages->name, 'visual-app-builder' ), # MENU TITLE - BAR
        $admin_page_permissions, # CAPACIBILITY TODO: AL USAR ROLES, SI SE ELIGE EDITOR, ADMIN NO PODRIA VERLO, BUSCAR ALTERNATIVA EN CAPACIBITYS QUE INCLUYAN A AMBOS
        $parent_admin_pages->slug, # MENU URL
        array($this, 'fkm_user_admin_page_content'), # CALL TO ACTION
        $admin_page_icon, # ICON
        intval($admin_page_position) # POSITION
      );
      
      # GET SUB ADMIN PAGE
      $sub_admin_pages = get_terms([
        'taxonomy' => 'admin_page_category',
        'hide_empty' => false,
        'parent' => $parent_admin_pages->term_id
      ]);
      if ($sub_admin_pages){
        foreach ($sub_admin_pages as $sub_admin_page) {
          # SI EXISTE EL VALOR, SE TRAE
          $subadmin_page_permissions = get_term_meta($sub_admin_page->term_id, 'admin_page_permissions', true);
          $subadmin_page_position    = get_term_meta($sub_admin_page->term_id, 'admin_page_position', true);
          # SE PREPARAN LOS DATOS
          $subadmin_page_permissions = (!empty($subadmin_page_permissions)) ? $subadmin_page_permissions : 'administrator';
          $subadmin_page_position    = (!empty($subadmin_page_position)) ? $subadmin_page_position : null; 

          add_submenu_page(
            $parent_admin_pages->slug,
            __( $sub_admin_page->name, FKM_TEXT_DOMAIN ),
            __( $sub_admin_page->name, FKM_TEXT_DOMAIN ),
            $subadmin_page_permissions,
            $sub_admin_page->slug,
            array($this, 'fkm_user_admin_page_content'),
            intval($subadmin_page_position) // $val
          );
        }
      }
    } # FOREACH
    # ADMIN PAGE USER
  }

  # SE REMUEVEN DETERMINADAS ADMIN PAGE A LOS ROLES CREADOS
  public function fkm_remove_admin_page() {
    $pos = strpos(fkm_get_currentUser(), 'fkm_');
    if ($pos !== false && $pos == 0){
      if (strpos($_SERVER['REQUEST_URI'], 'upload.php') !== false || strpos($_SERVER['REQUEST_URI'], 'media-new.php')) {
        wp_redirect(admin_url('index.php') );
        exit;
      }
      remove_menu_page('upload.php');
    }
  }

  # SE CARGAN LOS SCRIPTS / STYLES DEL ADMIN
  public function fkm_user_admin_page_content_style() {
    // TODO: CARGAR ESTILO PARA DARLE FORMA A LOS WIDGETS CARGADOS EN EL CONTENT DE LAS ADMIN PAGE USER
    
    // echo ABSPATH . 'wp-admin/load-styles.php';
    // require ABSPATH . 'wp-admin/load-styles.php';

  //  echo site_url('/wp-includes/js/mediaelement/mediaelementplayer-legacy.min.css');

  //   wp_register_style('wp_admin_style', site_url('/wp-includes/js/mediaelement/mediaelementplayer-legacy.min.css'), array(), time());
  //   wp_enqueue_style('wp_admin_style');

  //   wp_register_style('mediaelement', site_url('/wp-includes/js/mediaelement/wp-mediaelement.min.css'), array(), time());
  //   wp_enqueue_style('mediaelement');

  //   wp_register_script('fkm_block', site_url('/wp-includes/js/dist/blocks.min.js'), array(), time());
  //   wp_enqueue_script('fkm_block');


    // $style_path = get_stylesheet_directory_uri() . '/style.css';
    // wp_register_style( 'wp_admin_style',site_url('/wp-admin/css/wp-admin.min.css'), array(), time());
    // wp_enqueue_style('wp_admin_style');
    
  }
}


