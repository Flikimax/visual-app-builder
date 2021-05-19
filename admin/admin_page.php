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
    $admin_page = str_replace('fkm-', '', $_GET['page']);
    $admin_page = str_replace('-', '_', $admin_page);
    (method_exists($this, $admin_page)) ? $this->{$admin_page}() : $this->fkm_vab();
  }

  # MUESTRA EL CONTENIDO DE LAS ADMIN PAGE USER
  public function fkm_user_admin_page_content(){
    if (isset($_GET['content']) && !empty($_GET['content'])){
      $page_slug = $_GET['content'];
    } else {
      $page_slug = $_GET['page'];
      $main = true;
    }
    $content = $this->get_menu_page_content($page_slug, $main ?? false);

    if ($content){ 
      $content = apply_filters('the_content', $content); ?>
      <div class="contenedor-<?=FKM_TEXT_DOMAIN; ?>">
        <?=$content; ?>
      </div>
      <?php
    } else { 
      $user = wp_get_current_user();
      if (in_array('administrator', (array) $user->roles)) { # TODO: O USUARIO PRINCIAPL SETEADO ?>
        <div>
          <h3><?=__('You can add content', 'visual-app-builder'); ?> <a href="<?=admin_url('edit.php?post_type=visual_app_builder'); ?>" style="font-weight: bold;"><?=__('here.', 'visual-app-builder'); ?></a></h3>
        </div>
        <?php
      } else {
        ?>
        <div>
          <h3><?=__('No content assigned.', 'visual-app-builder'); ?></a></h3>
        </div>
        <?php
      }
    } 
  }

  public function get_menu_page_content($slug_page, $main = false) {
    $content = null;
    $slug_page = ltrim($slug_page, 'fkm-');
    if ($main) {
      $page = get_terms([
        'taxonomy'   => 'admin_page_category',
        'hide_empty' => false,
        'slug'       => "$slug_page",
      ])[0];
      $args = get_term_meta($page->term_id, 'admin_page_content', true);
      # GET POST
      $post = get_post($args);
      if ($post) $content = $post->post_content;
    } else {
      $args = array(
        'name'        => $slug_page,
        'post_type'   => 'visual_app_builder',
        'post_status' => 'publish',
        'numberposts' => 1,
      );
      # GET POST
      $post = get_posts($args);
      if (isset($post[0])) $post = $post[0];
      if ($post){
        $the_terms = json_decode(json_encode(get_the_terms($post->ID, 'admin_page_category')), true);
        if (!$the_terms) $the_terms = array();
        $main_page = ltrim($_GET['page'], 'fkm-');

        if (array_search($main_page, array_column($the_terms, 'slug')) === false){
          wp_die(__('Sorry, you are not allowed to access this page.'), __('No permits.', 'visual-app-builder'), [
            'link_url'  => admin_url("admin.php?page=fkm-$main_page"),
            'link_text' => 'Back to ' . get_admin_page_title()
          ] );
        }
        $content = $post->post_content;
      }
    }

    return $content;
  }



  # CREAR ADMIN PAGE
  public function fkm_admin_menu() {
    $this->fkm_remove_admin_page();
    $this->fkm_vab_admin_page(); # ADMIN PAGE VAB
    $this->fkm_user_admin_page(); # ADMIN PAGE USER
  }

  # CREACION DE LAS ADMIN PAGE PRINCIPALES
  public function fkm_vab_admin_page(){
    // remove_submenu_page(FKM_TEXT_DOMAIN, 'edit.php?post_type=visual_app_builder');

    add_menu_page(
      __( FKM_VAB, 'visual-app-builder' ),
      __( FKM_VAB, 'visual-app-builder' ),
      'publish_posts', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
      FKM_TEXT_DOMAIN,
      array($this, 'fkm_vab_admin_page_content'),
      'dashicons-schedule',
      3
    );
    add_submenu_page(
      FKM_TEXT_DOMAIN,
      __( FKM_VAB, 'visual-app-builder' ),
      __( FKM_VAB, 'visual-app-builder' ),
      'publish_posts', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
      FKM_TEXT_DOMAIN,
      array($this, 'fkm_vab_admin_page_content'),
      -1
    );
    # PAGE TAXONOMY
    add_submenu_page(
      FKM_TEXT_DOMAIN,
      __( 'Create menu pages', 'visual-app-builder' ),
      __( 'Create menu pages', 'visual-app-builder' ),
      'publish_posts', # TODO: EN LAS CONFIGURACIONES, ELEGIR EL USUARIO PRINCIPAL PARA VAB
      'edit-tags.php?taxonomy=admin_page_category',
      null,
      2
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
    if ($current_screen->taxonomy == 'admin_page_category'){
      $parent_file = FKM_TEXT_DOMAIN;
    }
    return $parent_file;
  }

  # CREACION DE LAS ADMIN PAGES CREADAS POR EL USUARIO ATRAVES DE LA TAXONOMIA
  public function fkm_user_admin_page(){
    # SE AGREGAN LOS ESTILOS DEL TEMA
    add_action('admin_enqueue_scripts', array($this, 'fkm_user_admin_page_content_style'));

    # ADMIN PAGE USER
    $parent_admin_pages = get_terms([
      'hide_empty' => false,
      'taxonomy' => 'admin_page_category',
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
        $admin_page_permissions,
        'fkm-' . $parent_admin_pages->slug, # MENU URL
        array($this, 'fkm_user_admin_page_content'), # CALL TO ACTION
        $admin_page_icon, # ICON
        intval($admin_page_position) # POSITION
      );
      
      # GET SUB ADMIN PAGE
      $sub_admin_pages = get_terms([
        'hide_empty' => false,
        'taxonomy' => 'admin_page_category',
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
            'fkm-' . $parent_admin_pages->slug,
            __( $sub_admin_page->name, FKM_TEXT_DOMAIN ),
            __( $sub_admin_page->name, FKM_TEXT_DOMAIN ),
            $subadmin_page_permissions,
            'fkm-' . $sub_admin_page->slug,
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
        wp_redirect(admin_url('index.php'));
        exit;
      }
      remove_menu_page('upload.php');
    }
  }

  # SE CARGAN LOS SCRIPTS / STYLES DEL ADMIN
  public function fkm_user_admin_page_content_style() {
    if (isset($_GET['page'])) {
      $pos = strpos($_GET['page'], 'fkm-');

      if ($pos !== false && $pos == 0){
        if (file_exists(get_template_directory() . '/style.css')) {
          wp_register_style('fkm_theme_styles', get_stylesheet_directory_uri() . '/style.css', array(), time());
          wp_enqueue_style ('fkm_theme_styles');
        }
      }
    }
      
    }
}


