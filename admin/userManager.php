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
        return $user->roles[0];
    }
}

# VERIFICA QUE EL USUARIO ACTUAL ES IGUAL A $userName
function fkm_current_User($userName){
    if ($userName == fkm_get_currentUser()){
        return true;
    }
}


// echo "<pre>";
// print_r( $ );
// echo "</pre>";