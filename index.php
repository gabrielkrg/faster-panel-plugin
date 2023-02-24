<?php

/*
Plugin Name: Faster Panel BW2
Description: Plugin com funções que deixam o painel mais rápido. (Necessário plugin ACF)
Author: BW2
Version: 1.0.0
*/

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_63f7c914c6563',
        'title' => 'User Options',
        'fields' => array(
            array(
                'key' => 'field_63f7c91566ac2',
                'label' => 'Acelerar painel',
                'name' => 'faster_panel_acf',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => 'Ao ativar esta opção, todas as consultas serão de até 1 meses atrás. (Notícias e Mídias) Utilize essa opção para acelerar seu painel, mas desative caso precise pesquisar algo além dos 1 meses retroativos.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'active' => 'Ativado',
                    'deactive' => 'Desativado',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'user_form',
                    'operator' => '==',
                    'value' => 'all',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}

add_action('init', 'user_config');
function user_config()
{
    add_action('admin_head', 'fpb_styles');
    function fpb_styles()
    {
        wp_enqueue_style('fpb-styles', plugins_url('assets/css/styles.css', __FILE__));
    }

    $user_id = get_current_user_id();
    $faster_panel = get_field('faster_panel_acf', 'user_' . $user_id);

    if (!$user_id || !$faster_panel) {
        return;
    }

    add_action('admin_bar_menu', 'admin_bar_item', 500);
    function admin_bar_item(WP_Admin_Bar $admin_bar)
    {
        $user_id = get_current_user_id();
        $faster_panel = get_field('faster_panel_acf', 'user_' . $user_id);
        $title = "";

        if (!current_user_can('manage_options')) {
            return;
        }

        if ($faster_panel == 'active') {

            $args = array(
                'id'    => 'menu-fast-active',
                'parent' => null,
                'group'  => null,
                'title' => 'Acelerador de painel - Ativado',
                'href'  => get_edit_profile_url(),
                'meta' => [
                    'title' => __('Clique aqui para ir às configurações de usuário e alterar essa opção. Basta ir até a sessão User options > Acelerar painel.', 'textdomain'), //This title will show on hover
                ]
            );
        } else {
            $args = array(
                'id'    => 'menu-fast-deactive',
                'parent' => null,
                'group'  => null,
                'title' => 'Acelerador de painel - Desativado',
                'href'  => get_edit_profile_url(),
                'meta' => [
                    'title' => __('Clique aqui para ir às configurações de usuário e alterar essa opção. Basta ir até a sessão User options > Acelerar painel.', 'textdomain'), //This title will show on hover
                ]
            );
        }

        $admin_bar->add_menu($args);
    }

    if (is_admin() && $faster_panel == 'active') {

        add_action('pre_get_posts', 'post_modify_range_date');
        function post_modify_range_date($query)
        {
            if ($query->is_main_query())
                $query->set('date_query', [
                    'after' => '1 month ago',
                    'inclusive' => true,
                ]);
        }

        add_action('pre_get_posts', 'modify_media_range_date');
        function modify_media_range_date($query)
        {

            global $pagenow;

            if (!in_array($pagenow, array('upload.php', 'admin-ajax.php')))
                return;

            $query->set('date_query', [
                'after' => '1 month ago',
                'inclusive' => true,
            ]);
        }
    }
}
