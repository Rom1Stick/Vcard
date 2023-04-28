<?php

// Register the VKard Widget
function vkard_register_widget() {
    register_widget('vkard_widget');
}
add_action('widgets_init', 'vkard_register_widget');

// Create the VKard Widget class
class vkard_widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'vkard_widget',
            __('VKard Widget', 'vkard'),
            array('description' => __('A widget to display the VKard vCard.', 'vkard'))
        );
    }

    public function display_output($options = array()) {
        // Get the saved VKard ID, redirect URL, and redirect delay from the options
        $vkard_id = get_option('vkard_id', '');
        $vkard_redirect_url = get_option('vkard_redirect_url', '');
        $vkard_redirect_delay = get_option('vkard_redirect_delay', 5000);
        $vkard_vcf_file_content = get_option('vkard_vcf_file_content', '');
    
        $output = '';
    
        // Display the vCard
        if (!empty($vkard_vcf_file_content)) {
            // Add headers for downloading the VCF content
            header('Content-Type: text/vcard');
            header('Content-Disposition: attachment; filename="vkard_vcf_file.vcf"');
    
            // Output the VCF content and exit
            echo $vkard_vcf_file_content;
            exit;
        } else {
            // Display the vCard using the VKard ID
            $output .= do_shortcode('[vkard id="' . $vkard_id . '"]');
        }
    
        // Add automatic redirection script
        if (!empty($vkard_redirect_url)) {
            $output .= '<script>';
            $output .= 'window.addEventListener("load", function () {';
            $output .= '  setTimeout(function() {';
            $output .= '    window.location.href = "' . esc_url($vkard_redirect_url) . '";';
            $output .= '  }, ' . esc_js($vkard_redirect_delay) . ');';
            $output .= '});';
            $output .= '</script>';
        }
    
        return $output;
    }


    public function widget($args, $instance) {
        // Before widget
        echo $args['before_widget'];

        // Display the vCard output
        echo $this->display_output();

        // After widget
        echo $args['after_widget'];
    }

    public function form($instance) {
        // There are no options for this widget
    }

    public function update($new_instance, $old_instance) {
        // There are no options for this widget
        return $instance;
    }
}

?>
