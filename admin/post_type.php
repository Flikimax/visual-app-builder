<?php
# SE CREA EL POST TYPE VISUAL APP BUILDER
function Fkm_postType(){
    # FIELDS
    #  SE AGREGA EL FIELD CUSTOM FIELD AL ADD
    add_action('admin_page_category_add_form_fields', 'admin_page_category_addform_termmeta', 10, 2 );
    # SE AGREGA EL FIELD CUSTOM FIELD AL EDIT
    add_action('admin_page_category_edit_form_fields', 'admin_page_category_editform_termmeta');
    # PARA PODER GUARDAR LOS CAMPOS CUSTOM
    add_action('edit_admin_page_category',   'fields_save_data_admin_page_category', 10, 2);
    add_action('create_admin_page_category', 'fields_save_data_admin_page_category', 10, 2);

    add_action('delete_admin_page_category', 'fields_save_data_admin_page_category', 10, 2);
    

    # COLUMNAS 
    # SE EDITAN LAS COLUMNAS CUSTOM PARA POST TYPE CATEGORY ADMIN PAGE 
    add_filter('manage_edit-admin_page_category_columns', 'theme_columns');
    # COLUMNAS CUSTOM PARA POST TYPE CATEGORY ADMIN PAGE
    add_filter('manage_edit-admin_page_category_columns', 'custom_category_columns');
    # GET VALORES A SUS RESPECTIVAS COLUMNAS 
    add_filter('manage_admin_page_category_custom_column', 'custom_category_columns_value', 10, 3 );

    # SE AGREGAN ALGUNOS CSS Y JS
    add_action('admin_head', 'wpse344725_taxonomy_css');
    # SE LIMITAN LAS JERARQUIAS
    add_filter( 'taxonomy_parent_dropdown_args', 'limit_parents_wpse_106164', 10, 2 );

    # SE REGISTRA LA TAXONOMIA
    register_taxonomy( 
      'admin_page_category',  
      'visual_app_builder', # post type name
      array(
        'labels' => array(
  				'name' => __( 'Create Menu Pages', 'visual-app-builder' ),
  				'all_items' => __( 'All', 'visual-app-builder' ),
  				'edit_item' => __( 'Edit' ),
  				'menu_name' => __( 'Menu Pages', 'visual-app-builder' ), # TOOLBAR
  				'update_item' => __( 'Update', 'visual-app-builder' ),
  				'search_items' => __( 'Search', 'visual-app-builder' ),
  				'add_new_item' => __( 'Add' ),
  				'new_item_name' => __( 'Add new', 'visual-app-builder' ),
  				'singular_name' => __( 'Create', 'visual-app-builder' ),
  				'popular_items' => __( 'Popular', 'visual-app-builder' ),
  				'add_or_remove_items' => __( 'Add or remove', 'visual-app-builder' ),
  				'choose_from_most_used' => __( 'Choose from the most popular' ),
          'not_found' => __('No found', 'visual-app-builder'),
        ),
        'public' => true,
        'show_ui' => true,
        'query_var' => true,
        'show_in_rest' => true,
        'capabilities' => array('publish_posts'),
        'default_term' => array(),
        'hierarchical' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => true,
        'rewrite' => array(
            'slug' => '',           // This controls the base slug that will display before each term
            'with_front' => false,  // Don't display the category base before
        ),
      )
    );

    # LABELS PARA EL CUSTOM  POST TYPE 
  	$labels = array(
  		'name' => __( 'Admin Menus Content'),
      'singular_name'      => __( 'Admin Menu Content' ),
      'add_new'            => __( 'Add new', 'visual-app-builder' ),
      'add_new_item'       => __( 'Add new', 'visual-app-builder' ), # Add new admin page
      'edit_item'          => __( 'Edit', 'visual-app-builder' ),
      'new_item'           => __( 'New', 'visual-app-builder' ),
      'view_item'          => __( 'Show', 'visual-app-builder' ),
      'search_items'       => __( 'Search', 'visual-app-builder' ),
      'not_found'          => (isset($_GET['s'])) ? __('No found: ' . $_GET['s'], 'visual-app-builder') :  __('No found', 'visual-app-builder'),
      'not_found_in_trash' => __( 'No content page has been found in the recycle garbage can', 'visual-app-builder' ),
    );

    $args = array(
      'supports'=> array('title','editor','excerpt', 'custom-fields'),
      'label' => __('visual_app_builder'),
      'labels' => $labels,
      'public' => true,
      'show_ui' => true,
      '_builtin' => false,
      'can_export' => true,
      'show_in_rest' => true,
      'map_meta_cap' => true,
      'hierarchical' => false,
      'show_in_nav_menus' => true,
      'show_in_menu' => FKM_TEXT_DOMAIN,
      'menu_icon' => 'dashicons-schedule',
      'capabilities' => array('publish_posts'),
      'taxonomies' => array('admin_page_category'),
      'rewrite' => array(
        "slug"       => '', # SE DEJA VACIO PARA QUE NO APAREZCA EN EL FrontEnd
        'with_front' => false,
        'pages'      => false
      ),
    );

    register_post_type('visual_app_builder', $args); # SE REGISTRA
}


# CUSTOM FIELDS
# SE AGREGAN LOS CUSTOM FIELD DEL USUARIO
function admin_page_category_addform_termmeta() {
  # ADMIN PAGE CONTENT
  $posts_visual_app_builder = get_posts( 
    array(
      'posts_per_page' => -1,
      'post_type'      => 'visual_app_builder',
    )
  ); 
  # GET WP ROLES 
  $roles = wp_roles()->role_objects; ?>
  <!-- ADMIN PAGE CONTENT -->
  <div class="form-field term-admin_page_content-wrap">
      <label for="admin_page_content">Post Content</label>
      <select name="admin_page_content" id="admin_page_content" class="postform">
      <option class="level-1" value=""><?=esc_html__('None', 'visual-app-builder'); ?></option>
        <?php foreach ($posts_visual_app_builder as $key => $post_visual_app_builder): ?>
          <option class="level-1" value="<?=$post_visual_app_builder->ID; ?>"><?=esc_html__($post_visual_app_builder->post_title, 'visual-app-builder'); ?></option>
        <?php endforeach; ?>
      </select>

      <p class="description"><?=esc_html__('Post for the content of the menu page.', 'visual-app-builder'); ?></p>
  </div>

  <!-- PERMISOS -->
  <div class="form-field term-admin_page_permissions-wrap">
    <label for="admin_page_permissions">Permissions</label>
    <select name="admin_page_permissions" id="admin_page_permissions" class="postform" required>
      <option class="level-0" value="0" selected><?='Add new role'; ?></option>
      <?php foreach ($roles as $key => $role): ?>
        <option class="level-0" value="<?=$role->name; ?>"><?=fkm_conver_role_to_display($role->name); ?></option>
      <?php endforeach; ?>
    </select>

    <p class="description"><?=__('Required permission for the menu page.<br/>
    Note: If you add a new role, you will only see its menu page and its respective sub items.', 'visual-app-builder'); ?>
    </p>
  </div>

  <!-- PERMITIR CAP AL ROL ADMIN -->
  <div class="form-field term-admin_page_hide_admin-wrap">
    <label for="admin_page_hide_admin">Hide administrator</label>
    <input type="checkbox" name="admin_page_hide_admin" id="admin_page_hide_admin">

    <p class="description"><?=esc_html__('If you enable this option, only the selected role will have access to the menu page.', 'visual-app-builder'); ?></p>
  </div>

  <!-- ICONOS -->
  <div class="form-field form-required term-admin_page_icon-wrap">
    <label for="admin_page_icon">Icon</label>
    <?php require_once 'src/icons.php'; ?>
    <p class="description"><?=esc_html__('Icon for the menu page.', 'visual-app-builder'); ?></p>
  </div>
  <!-- POSICION -->
  <div class="form-field form-required term-admin_page_position-wrap">
    <div>
      <label for="admin_page_position">Position</label>
      <input type="number" name="admin_page_position" id="admin_page_position">

      <p class="description"><?=esc_html__('Position of the menu page in the toolbar.', 'visual-app-builder'); ?></p>
    </div>
  </div> <?php  
}
# SE AGREGAN LOS CUSTOM FIELD AL EDITAR
function admin_page_category_editform_termmeta($term){
  $admin_page_content = get_term_meta($term->term_id, 'admin_page_content', true);
  $admin_page_permissions = get_term_meta($term->term_id, 'admin_page_permissions', true);
  $admin_page_hide_admin = get_term_meta($term->term_id, 'admin_page_hide_admin', true);
  $icon = get_term_meta($term->term_id, 'admin_page_icon', true);
  $position = get_term_meta($term->term_id, 'admin_page_position', true);

  $posts_visual_app_builder = get_posts(
    array(
      'posts_per_page' => -1,
      'post_type'      => 'visual_app_builder',
    )
  ); 
  # GET WP ROLES 
  $roles = wp_roles()->role_objects; ?>

  <table class="form-table" role="presentation">
    <tbody>
      <!-- ADMIN PAGE CONTENT -->
      <tr class="form-field term-admin_page_content-wrap">
        <th scope="row"><label for="admin_page_content">Post Content</label></th>
        <td>
          <select name="admin_page_content" id="admin_page_content" class="postform">+
            <option class="level-1" value=""><?=esc_html__('None', 'visual-app-builder'); ?></option>
            <?php foreach ($posts_visual_app_builder as $key => $post_visual_app_builder): ?>
              <option class="level-0" value="<?=esc_html__($post_visual_app_builder->ID, 'visual-app-builder'); ?>" <?=($admin_page_content == $post_visual_app_builder->ID) ? 'selected' : ''; ?>><?=esc_html__($post_visual_app_builder->post_title, 'visual-app-builder'); ?></option>
            <?php endforeach; ?>
          </select>
          <p class="description"><?=esc_html__('Post for the content of the menu page.', 'visual-app-builder'); ?></p>
        </td>
      </tr>
      <!-- PERMISOS -->
      <tr class="form-field form-required term-admin_page_permissions-wrap">
        <th scope="row"><label for="admin_page_permissions"><?=__('Permissions', 'visual-app-builder'); ?></label></th>
        <td>
          <select name="admin_page_permissions" id="admin_page_permissions" class="postform" required><?php
            if (!isset($roles["fkm_{$term->slug}"])){ ?>
              <option class="level-0" value="0" selected><?='Add new role'; ?></option> <?php 
            }
            foreach ($roles as $key => $role): ?>
              <option class="level-0" value="<?=$key; ?>" <?=($admin_page_permissions == $key) ? 'selected' : ''; ?>><?=fkm_conver_role_to_display($key); ?></option>
            <?php endforeach; ?>
          </select>

          <p class="description"><?=__('Required permission for the menu page.<br/>
            Note: If you add a new role, you will only see its menu page and its respective sub items.', 'visual-app-builder'); ?>
          </p>
        </td>
      </tr>


      <!-- NO PERMITIR CAP AL ROL ADMIN -->
      <tr class="form-field form-required term-admin_page_hide_admin-wrap">
        <th scope="row"><label for="admin_page_hide_admin">Hide administrator</label></th>
        <td>
          <input type="checkbox" name="admin_page_hide_admin" id="admin_page_hide_admin" <?=($admin_page_hide_admin) ? 'checked' : ''; ?>>

          <p class="description"><?=esc_html__('If you enable this option, only the selected role will have access to the menu page.', 'visual-app-builder'); ?></p>
        </td>
      </tr>


      <!-- ICONOS -->
      <tr class="form-field form-required term-admin_page_icon-wrap">
        <th scope="row"><label for="admin_page_icon">Icon</label></th>
        <td>
          <?php require_once 'src/icons.php'; ?>
          <p class="description"><?=esc_html__('Icon for the menu page.', 'visual-app-builder'); ?></p>
        </td>
      </tr>
      <!-- POSICION -->
      <tr class="form-field form-required term-admin_page_position-wrap">
        <th scope="row"><label for="admin_page_position">Position</label></th>
        <td>
          <input type="number" name="admin_page_position" id="admin_page_position" value="<?=($position || $position == 0) ? $position : ''; ?>">
          <p class="description"><?=esc_html__('Position of the menu page in the toolbar.', 'visual-app-builder'); ?></p>
        </td>
      </tr>
    </tbody>
  </table><?php
}

# PARA GUARDAR LOS CUSTOM FIELDS DE LA TAXONOMIA
function fields_save_data_admin_page_category($term_id){
  $roles = wp_roles()->role_objects;
  # DELETE
  if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['slug']) && $_GET['slug']){
    # SE REMUEVE EL ROL
    remove_role("fkm_" . $_GET['slug']); 

    # SE REMUEVE CAP DEL ADMIN
    $GLOBALS['wp_roles']->remove_cap('administrator', "fkm_" . $_GET['slug']);

    # SE REMUEVE LA CAP DE LA PAGINA SUPERIOR
    $parent_admin_pages = get_terms([
      'taxonomy' => 'admin_page_category',
      'hide_empty' => false,
      'parent' => 0
    ]);
    foreach ($parent_admin_pages as $key => $parent_admin_page) {
      if ( isset($roles["fkm_{$parent_admin_page->slug}"]->capabilities["fkm_{$_GET['slug']}"]) ){
        $GLOBALS['wp_roles']->remove_cap("fkm_{$parent_admin_page->slug}", "fkm_{$_GET['slug']}");
      }
    }
    return;
  }


  # SI EXISTE EL VALOR, SE TRAE
  $old_admin_page_content     = get_term_meta($term_id, 'admin_page_content', true);
  $old_admin_page_permissions = get_term_meta($term_id, 'admin_page_permissions', true);

  $old_admin_page_hide_admin  = get_term_meta($term_id, 'admin_page_hide_admin', true);

  $old_admin_page_icon        = get_term_meta($term_id, 'admin_page_icon', true);
  $old_admin_page_position    = get_term_meta($term_id, 'admin_page_position', true);

  # SE SANEA LO INTRUDUCIDO POR EL USUARIO
  $admin_page_content     = sanitize_text_field($_POST['admin_page_content']);
  $admin_page_permissions = sanitize_text_field($_POST['admin_page_permissions']);
  
  $admin_page_hide_admin = (isset($_POST['admin_page_hide_admin']) && $_POST['admin_page_hide_admin']) ? true : false;

  $admin_page_icon        = (isset($_POST['admin_page_icon'])) ? sanitize_text_field($_POST['admin_page_icon']) : null;
  $admin_page_position    = sanitize_text_field($_POST['admin_page_position']);


  # PERMISOS
  if ($admin_page_permissions == 0 || $admin_page_permissions == '0'){
    $roles = array_keys($roles);
    $display_role = ($_POST['action'] == 'add-tag') ? trim($_POST['tag-name']) : $_POST['slug'];
    $slug = str_replace(' ', '-', strtolower(remove_accents($display_role)));
    if (in_array($slug, $roles)){
      $display_role = 'Fkm ' . $display_role;
    }
    $admin_page_permissions = "fkm_{$slug}";

    add_role(
      $admin_page_permissions,
      $display_role,
      array( # CAPACIBILITY
      'read' => true, 
      'upload_files' => true, 
      ) 
    );
  }

  if ($_POST['action'] == 'add-tag' || $_POST['action'] == 'editedtag'){
    if (!isset($_POST['admin_page_hide_admin'])){ # AGREGAR CAP AL ADMIN
      $GLOBALS['wp_roles']->add_cap('administrator', $admin_page_permissions);
    } else { 
      $GLOBALS['wp_roles']->remove_cap("administrator", $admin_page_permissions);
    }
    
    # AGREGAR CAP A LA PAGINA SUPERIOR
    if ($_POST['parent'] > 0){
      $parent = get_term_by('ID', $_POST['parent'], 'admin_page_category');
      $GLOBALS['wp_roles']->add_cap("fkm_{$parent->slug}", $admin_page_permissions);
    }
  }

  # SE ACTUALIZA EL NOMBRE DEL ROLE
  if ($_POST['action'] == 'editedtag' && !fkm_is_role_default($_POST['slug'])){
    $wp_user_roles = get_option('wp_user_roles');
    $wp_user_roles["fkm_{$_POST['slug']}"]['name'] = $_POST['name'];
    update_option('wp_user_roles', $wp_user_roles);
  }

  # SE ACTUALIZA EL CAMPO META EN LA BASE DE DATOS
  if ($admin_page_content != $old_admin_page_content){
    update_term_meta($term_id, 'admin_page_content', $admin_page_content, $old_admin_page_content);
  }
  if ($admin_page_permissions != $old_admin_page_permissions){
    update_term_meta($term_id, 'admin_page_permissions', $admin_page_permissions, $old_admin_page_permissions);
  }
  if ($admin_page_hide_admin != $old_admin_page_hide_admin){
    update_term_meta($term_id, 'admin_page_hide_admin', $admin_page_hide_admin, $old_admin_page_hide_admin);
  }
  if($admin_page_icon && $admin_page_icon != $old_admin_page_icon){
    update_term_meta($term_id, 'admin_page_icon', $admin_page_icon, $old_admin_page_icon);
  }
  if ($admin_page_position != $old_admin_page_position){
    update_term_meta($term_id, 'admin_page_position', $admin_page_position, $old_admin_page_position);
  }
}
# CUSTOM FIELDS


# CUSTOM COLUMNS
# SE EDITAN LAS COLUMNAS CUSTOM PARA POST TYPE CATEGORY ADMIN PAGE 
function theme_columns($theme_columns) {
  unset(
    $theme_columns['slug'],
    $theme_columns['description'],
    $theme_columns['posts'],
  );
 
  $new_columns = array(
    'cb'                    => '<input type="checkbox" />',
    'name'                  => __('Admin Page Name', 'visual-app-builder'),
    'admin_page_content'    => __('Admin Page Content', 'visual-app-builder'),
    'admin_page_icon'       => __('Icon', 'visual-app-builder'),
    'admin_page_position'   => __('Position', 'visual-app-builder'),
    'admin_page_hide_admin' => __('Hide administrator', 'visual-app-builder'),
    'slug' => __('Slug'),
    // 'description' => __('Description'),
    // 'posts' => __('Posts')
  );
  return $new_columns;
}
# COLUMNAS CUSTOM PARA POST TYPE CATEGORY ADMIN PAGE
function custom_category_columns( $columns ) {
  $columns['admin_page_content']     = __('Admin Page Content', 'visual-app-builder');
  $columns['admin_page_permissions'] = __('Permissions', 'visual-app-builder');
  $columns['admin_page_icon']        = __('Icon', 'visual-app-builder');
  $columns['admin_page_position']    = __('Position', 'visual-app-builder');
  $columns['admin_page_hide_admin']  = __('Hide administrator', 'visual-app-builder');
  return $columns;
}
# GET VALORES A SUS RESPECTIVAS COLUMNAS 
function custom_category_columns_value($out, $column, $term_id){
  if ($column === 'admin_page_content'){ # PAGE CONTENT 
    $admin_page_content = get_term_meta( $term_id, 'admin_page_content', true);
    if (!empty($admin_page_content)) return get_the_title($admin_page_content);
  } else if ($column === 'admin_page_permissions'){ # PERMISOS
    $admin_page_permissions = get_term_meta($term_id, 'admin_page_permissions', true);
    if (!empty($admin_page_permissions)) return ucfirst(fkm_conver_role_to_display($admin_page_permissions));
  } else if ($column === 'admin_page_icon'){ # ICONO
    $admin_page_icon = get_term_meta( $term_id, 'admin_page_icon', true);
    if (!empty($admin_page_icon)) return "<span class='dashicons dashicons-$admin_page_icon'></span>";
  } else if ($column === 'admin_page_position'){ # POSICION
    $admin_page_pos = get_term_meta( $term_id, 'admin_page_position', true);
    if (!empty($admin_page_pos) || $admin_page_pos == 0) return $admin_page_pos;
  } else if ($column === 'admin_page_hide_admin'){ # HIDE ADMIN
    $admin_page_hide_admin = get_term_meta( $term_id, 'admin_page_hide_admin', true);
    $check = (!empty($admin_page_hide_admin)) ? 'checked' : '';
    return "<input type='checkbox' name='admin_page_hide_admin' id='admin_page_hide_admin' $check disabled />";
  }
}
# CUSTOM COLUMNS


/**
  * ESCONDE/REMUEVE CAMPOS SLUG Y DESCRIPTION DE LA TAXONOMIA CUSTOM
  * @return void
*/
function wpse344725_taxonomy_css() {
  global $taxonomy;
  $modified_tax_arr = array( 'admin_page_category' );
  if(empty($taxonomy) || !in_array($taxonomy, $modified_tax_arr)) return; ?>

  <script type="text/javascript">
    jQuery( document ).ready(function() {
      jQuery('#tag-slug').parent().remove();
      jQuery('#tag-description').parent().remove();
    });
  </script>

  <style>
    .form-field.term-slug-wrap,
    .form-field.term-description-wrap {display: none;}
  </style> <?php
}


/**
 * SE LIMITAN LAS JERARQUIAS A UN NIVEL
 * @param array $args argumentos de la taxonomia
 * @param string $taxonomy Taxonomia
 * @return array
 **/
function limit_parents_wpse_106164( $args, $taxonomy ) {
  if ('admin_page_category' != $taxonomy) return $args; # SIN CAMBIOS
    $args['depth'] = '1';
    return $args;
}

if ((isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'admin_page_category') || (isset($_GET['post_type']) && $_GET['post_type'] == 'visual_app_builder')){
  add_action('admin_enqueue_scripts', 'fkm_post_type_style_scritps'); # SE CARGAN LOS SCRIPTS Y CSS PARA POST TYPE
  function fkm_post_type_style_scritps(){
    wp_register_script('fkm_post_type_style', plugins_url(basename(FKM_PLUGIN_PATH) . "/admin/src/js/post_type.js"), array(), FKM_VERSION);
    wp_enqueue_script ('fkm_post_type_style');

    wp_register_style('fkm_post_type_script', plugins_url(basename(FKM_PLUGIN_PATH) . "/admin/src/css/post_type.css"), array(), FKM_VERSION);
    wp_enqueue_style ('fkm_post_type_script');
  }
}

Fkm_postType(); # SE CREA EL CUSTOM POST TYPE Y LA TAXONOMY


