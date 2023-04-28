<?php
/*
Plugin Name: Vcard Utlimate Addon
Plugin URI: https://example.com/vkard-integration
Description: An extension to integrate your vCard with your WordPress site.
Version: 0.22
Author: Magnier
Author URI: https://example.com
License: GPLv2 or later
Text Domain: vkard-integration
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Define plugin path
define('VKARD_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include necessary files
require_once VKARD_PLUGIN_DIR . 'includes/widget.php';
require_once VKARD_PLUGIN_DIR . 'includes/options-page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/vkard-block/vkard-block.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/vkard-block/vkard-block.js';

function vkard_vcf_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'content' => '',
        ),
        $atts,
        'vkard_vcf'
    );

    if (!empty($atts['content'])) {
        $vcf_content = base64_decode($atts['content']);

        // Ici, vous pouvez ajouter le code pour traiter et afficher le contenu du fichier VCF
        // selon la structure que vous souhaitez pour la vCard.
        // Ce code dépendra de la manière dont vous souhaitez afficher les informations
        // de la vCard sur votre site Web.

        // Pour l'instant, affichons simplement le contenu du fichier VCF
        return '<pre>' . esc_html($vcf_content) . '</pre>';
    } else {
        return '';
    }
}
add_shortcode('vkard_vcf', 'vkard_vcf_shortcode');


// AJout du widget vcard


function vkard_add_widget_to_sidebar() {
    // Nom de la zone de widget où vous voulez ajouter le widget vKard
    $sidebar_id = 'sidebar-1';

    // Vérifie si le widget vKard est déjà présent dans la zone de widget
    $sidebars_widgets = get_option('sidebars_widgets');
    $vkard_widget_exists = false;

    if (isset($sidebars_widgets[$sidebar_id])) {
        foreach ($sidebars_widgets[$sidebar_id] as $widget) {
            if (strpos($widget, 'vkard_widget') !== false) {
                $vkard_widget_exists = true;
                break;
            }
        }
    }

    // Si le widget vKard n'est pas présent, ajoutez-le à la zone de widget
    if (!$vkard_widget_exists) {
        $widget_instances = get_option('widget_vkard_widget');
        $new_instance_id = max(array_keys($widget_instances)) + 1;

        $widget_instances[$new_instance_id] = array(); // Vous pouvez ajouter des paramètres de widget par défaut ici
        update_option('widget_vkard_widget', $widget_instances);

        $sidebars_widgets[$sidebar_id][] = 'vkard_widget-' . $new_instance_id;
        update_option('sidebars_widgets', $sidebars_widgets);
    }
}
register_activation_hook(__FILE__, 'vkard_add_widget_to_sidebar');


// *********************************************************************************************************
// class VCard_Widget extends WP_Widget {
//     public function __construct() {
//         parent::__construct(
//             'vcard_widget', // Base ID
//             'vCard Widget', // Name
//             array('description' => __('A widget to display vCard information', 'text_domain'))
//         );
//     }

//     public function widget($args, $instance) {
//         // Affichez ici le contenu de votre widget vCard
//         // Par exemple, vous pouvez inclure un fichier PHP avec le code d'affichage :
//         include('path/to/your/vcard/display/file.php');
//     }

//     public function form($instance) {
//         // Ajoutez ici les options de configuration du widget, si nécessaire
//     }

//     public function update($new_instance, $old_instance) {
//         // Mettez à jour les options du widget ici, si nécessaire
//     }
// }

// function register_vcard_widget() {
//     register_widget('VCard_Widget');
// }
// add_action('widgets_init', 'register_vcard_widget');
// **********************************************************************************************************


// Enregistre le code personnalisé
if (isset($_POST['custom_code'])) {
    update_option('vkard_custom_code', stripslashes($_POST['custom_code']));
}

$custom_code = get_option('vkard_custom_code', '');
if (!empty($custom_code)) {
    // Save custom code to a separate PHP file
    $custom_code_file = VKARD_PLUGIN_DIR . 'custom-code.php';
    file_put_contents($custom_code_file, $custom_code);

    // Include the custom code file
    include($custom_code_file);
}

