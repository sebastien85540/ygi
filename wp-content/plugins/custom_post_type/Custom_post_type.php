<?php
/*
* Plugin Name: Custom Post Type
* Description: Ce plugin ajoute un type de contenu personnalisé appelé "Avis".
*/


// Déclaration de la fonction qui va enregistrer notre Custom Post Type
function wporg_custom_post_type()
{
    // Fonction pour enregistrer un nouveau type de contenu : 'aviq'
    register_post_type(
        'avis', // Identifiant du type de contenu
        array(
            // Labels visibles dans l'interface d'administration WordPress
            'labels' => array(
                'name' => __('Avis', 'textdomain'), // Nom au pluriel affiché dans le menu
                'singular_name' => __('Avis', 'textdomain'), // Nom au singulier (utilisé dans les titres, etc.)
            ),
            'public' => true,              // Si true, les utilisateurs peuvent le voir et l'utiliser sur le site
            'hierarchical' => false,       // false = pas de hiérarchie (comme les articles), true = hiérarchique (comme les pages)
            'has_archive' => true,         // Si true, crée une page d’archive (ex: /instrument/) listant tous les instruments
        )
    );
}


// Hook WordPress : on exécute la fonction lors de l'initialisation ('init')
add_action('init', 'wporg_custom_post_type');
