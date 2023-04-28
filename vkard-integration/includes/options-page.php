<?php
// Create custom plugin settings menu
add_action('admin_menu', 'vkard_create_menu');



function vkard_upload_vcf() {
    if (isset($_FILES['vkard_vcf_file']['tmp_name']) && !empty($_FILES['vkard_vcf_file']['tmp_name'])) {
        // Vérifier l'extension du fichier
        $file_extension = pathinfo($_FILES['vkard_vcf_file']['name'], PATHINFO_EXTENSION);
        if (strtolower($file_extension) !== 'vcf') {
            // L'extension du fichier n'est pas '.vcf', arrêtez le traitement et affichez un message d'erreur
            wp_die('Le fichier téléchargé n\'est pas un fichier VCF valide. Veuillez télécharger un fichier avec l\'extension .vcf.');
        }

        $vcf_file_content = file_get_contents($_FILES['vkard_vcf_file']['tmp_name']);
        update_option('vkard_vcf_file_content', $vcf_file_content);
        update_option('vkard_vcf_file_name', $_FILES['vkard_vcf_file']['name']);
    }
}



function vkard_delete_vcf() {
    if (isset($_POST['vkard_action']) && $_POST['vkard_action'] == 'delete_vcf') {
        delete_option('vkard_vcf_file_content');
        delete_option('vkard_vcf_file_name');
    }
}
add_action('admin_init', 'vkard_delete_vcf');




function register_vkard_settings() {
    // Register our settings
    register_setting('vkard-settings-group', 'vkard_id');
    register_setting('vkard-settings-group', 'vkard_redirect_url');
    register_setting('vkard-settings-group', 'vkard_redirect_delay');

}




function vkard_create_menu() {
    add_menu_page('VKard Settings', 'VKard', 'manage_options', 'vkard', 'vkard_settings_page', 'dashicons-id');
    // add_submenu_page('vkard', 'VKard Advanced Settings', 'Advanced Settings', 'manage_options', 'vkard_advanced', 'vkard_advanced_settings_page');

    add_action('admin_init', 'register_vkard_settings');
    add_action('admin_post_vkard_upload_vcf', 'vkard_upload_vcf');
    add_action('admin_post_vkard_delete_vcf', 'vkard_delete_vcf');
}





function vkard_settings_page() {
?>
<div class="wrap">
    <h1>VCard Config</h1>

    <div class="vkard-settings">
        <ul class="vkard-settings-tabs">
            <li class="tab active" data-tab="options">Options</li>
            <li class="tab" data-tab="info">Info</li>
        </ul>

        <div class="vkard-settings-content">
            <div class="tab-content active" id="options">
                <form id="vkard-upload-vcf-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="vkard_upload_vcf">
                    <?php settings_fields('vkard-settings-group'); ?>
                    <?php do_settings_sections('vkard-settings-group'); ?>
                    

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Télécharger le fichier VCF</th>
                                <td>
                                    <input type="file" name="vkard_vcf_file" accept=".vcf">
                                    <input type="submit" class="button" value="Télécharger">
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Le fichier VCF actuel est :</th>
                                <td>
                                    <span id="vcf-file-name"><?php echo esc_html(get_option('vkard_vcf_file_name', '')); ?></span>
                                    <?php if (get_option('vkard_vcf_file_name', '') != ''): ?>
                                        <a href="#" id="delete-vcf" onclick="deleteVcf()" style="margin-left: 10px;">Supprimer</a>
                                        <input type="hidden" name="vkard_action" value="">
                    
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                    
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">ID vCard</th>
                            <td><input type="text" name="vkard_id" value="<?php echo esc_attr(get_option('vkard_id')); ?>" /></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">URL de redirection</th>
                            <td><input type="text" name="vkard_redirect_url" value="<?php echo esc_attr(get_option('vkard_redirect_url')); ?>" /></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">URL de redirection</th>
                            <td>
                                <input type="text" name="vkard_redirect_url" value="<?php echo esc_attr(get_option('vkard_redirect_url')); ?>" />
                                <a href="#" id="advanced-settings-tab" style="margin-left: 10px;">Advanced Settings</a>
                            </td>
                        </tr>
                        <tr valign="top" id="advanced-settings" style="display:none;">
                            <th scope="row">Redirection Delay (ms)</th>
                            <td><input type="number" name="vkard_redirect_delay" value="<?php echo esc_attr(get_option('vkard_redirect_delay', 5000)); ?>" /></td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            
            
            
            <!--<li><a href="#affichage" data-toggle="tab">Affichage</a></li>-->
                <div id="affichage" class="tab-content">
                    <h2>Personnaliser l'affichage de la vCard</h2>
                    <p>Dans cet onglet, vous pouvez ajouter un code personnalisé pour traiter et afficher les informations de la vCard selon la structure souhaitée sur votre site Web. Utilisez le champ de texte ci-dessous pour insérer votre code personnalisé. Une fois terminé, cliquez sur le bouton "Enregistrer les modifications" pour sauvegarder votre code.</p>
                
                    <h3>Ajouter un code personnalisé</h3>
                    <textarea id="custom_code" name="custom_code" rows="10" cols="80"></textarea>
                    <br>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                
                    <h3>Exemple de code personnalisé</h3>
                    <p>Voici un exemple de code pour afficher les informations de la vCard sous forme de liste :</p>
                    <pre><code>
                    &lt;ul&gt;
                        &lt;li&gt;Nom : &lt;?= $vcard->fullname ?&gt;&lt;/li&gt;
                        &lt;li&gt;Organisation : &lt;?= $vcard->organization ?&gt;&lt;/li&gt;
                        &lt;li&gt;Titre : &lt;?= $vcard->title ?&gt;&lt;/li&gt;
                        &lt;li&gt;Téléphone : &lt;?= $vcard->telephone ?&gt;&lt;/li&gt;
                        &lt;li&gt;Email : &lt;?= $vcard->email ?&gt;&lt;/li&gt;
                        &lt;li&gt;Adresse : &lt;?= $vcard->address ?&gt;&lt;/li&gt;
                    &lt;/ul&gt;
                    </code></pre>
                </div>

            
            
            
            
                <<div id="info" class="tab-content">
                    <h2>Informations sur l'intégration de vCard</h2>
                    <p>Ce plugin vous permet d'intégrer facilement une vCard dans votre site WordPress à l'aide d'un widget ou d'un shortcode. Vous pouvez également configurer une URL de redirection pour rediriger automatiquement les visiteurs vers une URL spécifique lorsqu'ils atterrissent sur une page contenant la vCard.</p>
                    
                    <h3>Utilisation du Widget</h3>
                    <p>Pour ajouter la vCard à votre site à l'aide du widget, allez dans Apparence > Widgets et ajoutez le "Widget VKard" à l'une de vos zones de widgets.</p>
                    
                    <h3>Utilisation du Shortcode</h3>
                    <p>Pour ajouter la vCard à votre site à l'aide du shortcode, insérez le code suivant dans l'éditeur de contenu de votre page ou de votre article :</p>
                    <code>[vkard id="your_vkard_id"]</code>
                    <p>Remplacez "your_vkard_id" par l'ID de la vCard que vous avez défini dans l'onglet "Options".</p>
                    
                    <h3>Configuration de l'URL de redirection</h3>
                    <p>Pour configurer la redirection automatique vers une URL spécifique, entrez l'URL souhaitée dans le champ "URL de redirection" de l'onglet "Options". Lorsqu'un visiteur atterrit sur une page contenant la vCard, il sera automatiquement redirigé vers cette URL après un délai que vous pouvez modifier dans l'onglet "Options > Paramètres avancés". Vous pouvez ajuster ce délai en modifiant la valeur de `setTimeout` dans le code JavaScript du fichier `widget.php`.</p>
                    
                    <h3>Téléchargement d'un fichier VCF</h3>
                    <p>En plus d'utiliser un ID de vCard, vous pouvez également télécharger un fichier VCF directement sur votre site Web. Pour ce faire, allez dans l'onglet "Options" et utilisez l'option "Télécharger le fichier VCF". Une fois que vous avez téléchargé un fichier VCF, le widget affichera la vCard en fonction du contenu du fichier au lieu de l'ID de la vCard.</p>
                    
                    <h3>Affichage et suppression du fichier VCF</h3>
                    <p>Après avoir téléchargé un fichier VCF, le nom du fichier sera affiché dans l'onglet "Options" sous "Contenu du fichier VCF". Pour supprimer le fichier VCF, cliquez simplement sur le lien "Supprimer" à côté du nom du fichier. La page sera alors rechargée et les modifications seront enregistrées.</p>
                    
                    <p>Notez que le contenu du fichier VCF téléchargé sera affiché en texte brut. Vous devrez ajouter un code personnalisé pour traiter et afficher les informations de la vCard selon la structure souhaitée sur votre site Web.</p>
                </div>

            </div>
        </div>
    </div>
</div>

    <style>
    .vkard-settings-tabs {
        display: flex;
        list-style: none;
        padding: 0;
        margin-bottom: 20px;
        border-bottom: 2px solid #ccc;
    }

    .vkard-settings-tabs .tab {
        margin: 0;
        padding: 10px 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .vkard-settings-tabs .tab.active {
        border-bottom: 4px solid #0073aa;
    }

    .vkard-settings-content .tab-content {
        display: none;
    }

    .vkard-settings-content .tab-content.active {
        display: block;
    }
</style>

<script>
    (function ($) {
        $(document).ready(function () {
            $('.vkard-settings-tabs .tab').on('click', function () {
                var target = $(this).data('tab');

                $('.vkard-settings-tabs .tab').removeClass('active');
                $(this).addClass('active');

                $('.vkard-settings-content .tab-content').removeClass('active');
                $('#' + target).addClass('active');
            });
        });
    })(jQuery);
</script>

<script>
    document.getElementById('advanced-settings-tab').addEventListener('click', function (event) {
        event.preventDefault();
        var advancedSettings = document.getElementById('advanced-settings');
        if (advancedSettings.style.display === 'none') {
            advancedSettings.style.display = 'table-row';
        } else {
            advancedSettings.style.display = 'none';
        }
    });
</script>

<script>
    
function deleteVcf() {
    document.querySelector('input[name="vkard_action"]').value = 'delete_vcf';
    document.querySelector('form[method="post"]').submit();
}

    
</script>



<?php
}



