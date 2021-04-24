<?php

# CONVIERTE EL NOMBRE DEL ROL PARA MOSTRARLO AL USUARIO
function fkm_conver_role_to_display($roleName){
    global $wp_roles;
    if (isset($wp_roles->role_names[$roleName])){
        return ucfirst($wp_roles->role_names[$roleName]);
    }
    return;
}

# RETORNA EL USUARIO ACTUAL
function fkm_get_currentUser(){
    if (is_user_logged_in()){
        $user = wp_get_current_user();
        return isset($user->roles[0]) ? $user->roles[0] : null;
    }
}

# VERIFICA QUE EL USUARIO ACTUAL ES IGUAL A $userName
function fkm_current_User($userName){
    if ($userName == fkm_get_currentUser()){
        return true;
    }
}

# VERIFICA SI EL ROLE FUE CREADO POR EL PLUGIN
function fkm_is_role_default($role) {
    // if ($role == 'administrator' || $role == 'editor' || $role == 'author' ||
    //     $role == 'contributor' || $role == 'subscriber'){
    //         return true;
    // }  
    $pos = strpos($role, 'fkm_');
    return ($pos !== false) ? false : true;
}