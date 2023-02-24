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
                'label' => 'Faster Panel',
                'name' => 'faster_panel_acf',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'active' => 'Activated',
                    'deactive' => 'Deactivated',
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
    $user_id = get_current_user_id();
    $faster_panel = get_field('faster_panel_acf', 'user_' . $user_id);

    if (!$user_id || !$faster_panel) {
        return;
    }

    if (is_admin() && $faster_panel == 'active') {

        add_filter('pre_get_posts', function ($query) {
            $query->set(
                'ep_integrate',
                true
            );
            $query->set('date_query', array('after' => '3 month ago'));
        });


        $current_date = date('U');
        $date_30_days_ago =

            add_action('pre_get_posts', 'post_modify_range_date');
        function post_modify_range_date($query)
        {
            if ($query->is_main_query())
                $query->set('date_query', [
                    'after' => '3 month ago',
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
                'after' => '3 month ago',
                'inclusive' => true,
            ]);
        }
    }
}
