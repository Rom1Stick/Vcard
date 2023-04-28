<?php

function vkard_register_block() {
    // Check if the register function exists
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    // Register the block script
        wp_register_script(
            'vkard-block-script',
            plugin_dir_url( __FILE__ ) . 'vkard-block.js',
            array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' )
        );


    // Register the block
    register_block_type( 'vkard/vkard-block', array(
        'editor_script' => 'vkard-block-script',
        'render_callback' => 'vkard_render_block',
    ) );
}
add_action( 'init', 'vkard_register_block' );

function vkard_render_block( $attributes ) {
    // Create an instance of the widget
    $vkard_widget = new vkard_widget();

    // Get the widget output
    $output = $vkard_widget->display_output();

    return $output;
}

