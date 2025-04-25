<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ygi_wp676' );

/** Database username */
define( 'DB_USER', 'ygi_wp676' );

/** Database password */
define( 'DB_PASSWORD', ')pKC4S61[5' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'fe5g4jluwfcknhpoufffi5nkyxjfyqspqfmywe7mgsb9eetfrwewthrpusghoiym' );
define( 'SECURE_AUTH_KEY',  'rkylp9lyaploocp0i3zdpefb7kovblymqhkdqgspb3kkibcy4fcne7xzx3dpmyqz' );
define( 'LOGGED_IN_KEY',    '8ecixzmseli4pvxxavnnpczlyxht5j1i6xqrs5el1tosbaplrd4oup3hvawaovph' );
define( 'NONCE_KEY',        'qfijtk2laz1yhensuupwkackzn94gmybblhwpluczyiwgcteifo7ycqqdwvhayh1' );
define( 'AUTH_SALT',        'svppuaqbaasxnklfxoiquvcfjcuwjbvnubrfoyechq7luxlmj29dflaprjwv9mjc' );
define( 'SECURE_AUTH_SALT', '6qp3by3tqbjhzhpn3xvm8kae359lejohzl3gg3iafxahnv9yqybfopf1glyezjdd' );
define( 'LOGGED_IN_SALT',   'bzx2gyd858tjatrczgpbxcnhxnrkuoocdpjcxl9wuhjjimmzddnp8ubagqazwv33' );
define( 'NONCE_SALT',       'uoywfcnenyprzhd1juvwoj2vuhtvgbdeordvhqpat3bsbodl54z9qct34o5zkktl' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpwk_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
