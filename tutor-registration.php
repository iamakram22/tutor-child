<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access

add_filter('tutor_student_registration_required_fields', 'required_registration_fields_callback');
if ( ! function_exists('required_registration_fields_callback')){
    function required_registration_fields_callback($atts){

        foreach(CUSTOM_FIELDS as $key => $value) {
            $atts[$key] = $value . ' is required';
        }

        return $atts;
    }
}
add_action('user_register', 'update_fields_on_registeration');
add_action('profile_update', 'update_fields_on_registeration');
if ( ! function_exists('update_fields_on_registeration')) {
    function update_fields_on_registeration($user_id){

        foreach (CUSTOM_FIELDS as $key => $value) {
            if( ! empty($_POST[$key]) ) {
                $field = $_POST[$key];
                update_user_meta($user_id, '_' . $key, $field);
            }
        }

    }
}