<?php
/**
 * Modify tutor instructor capibilities
 *
 * @return void
 */
function wporg_simple_role_caps()
{
    $admin = get_role('administrator');
    $tutor = get_role('tutor_instructor');
    if ($admin && $tutor) {
        foreach ($admin->capabilities as $capability => $value) {
            $tutor->add_cap($capability);
        }
    }

    $removecap = array(
        'switch_themes',
        'edit_themes',
        'activate_plugins',
        'edit_plugins',
        'moderate_comments',
        'manage_categories',
        'manage_links',
        'import',
        'unfiltered_html',
        'edit_others_posts',
        'edit_pages',
        'edit_others_pages',
        'edit_published_pages',
        'publish_pages',
        'delete_pages',
        'delete_others_pages',
        'delete_published_pages',
        'delete_others_posts',
        'delete_published_posts',
        'delete_private_pages',
        'edit_private_pages',
        'read_private_pages',
        'unfiltered_upload',
        'edit_dashboard',
        'update_plugins',
        'delete_plugins',
        'install_plugins',
        'update_themes',
        'install_themes',
        'update_core',
        'promote_users',
        'edit_theme_options',
        'delete_themes',
        'export',
        'wpcode_edit_snippets',
        'wpcode_activate_snippets',
        'manage_tutor',
        'manage_woocommerce',
        'view_woocommerce_reports',
        'edit_product',
        'read_product',
        'delete_product',
        'edit_products',
        'edit_others_products',
        'publish_products',
        'read_private_products',
        'delete_products',
        'delete_private_products',
        'delete_published_products',
        'delete_others_products',
        'edit_private_products',
        'edit_published_products',
        'manage_product_terms',
        'edit_product_terms',
        'delete_product_terms',
        'assign_product_terms',
        'edit_shop_order',
        'delete_shop_order',
        'edit_shop_orders',
        'edit_others_shop_orders',
        'publish_shop_orders',
        'read_private_shop_orders',
        'delete_shop_orders',
        'delete_private_shop_orders',
        'delete_published_shop_orders',
        'delete_others_shop_orders',
        'edit_private_shop_orders',
        'edit_published_shop_orders',
        'manage_shop_order_terms',
        'edit_shop_order_terms',
        'delete_shop_order_terms',
        'assign_shop_order_terms',
        'edit_others_shop_coupons',
        'read_private_shop_coupons',
        'delete_private_shop_coupons',
        'delete_others_shop_coupons',
        'edit_private_shop_coupons',
        'resume_plugins',
        'resume_themes',
        'view_site_health_checks',
    );

    foreach ($removecap as $remove) {
        $tutor->remove_cap($remove, true);
    }
}
add_action('init', 'wporg_simple_role_caps', 11);