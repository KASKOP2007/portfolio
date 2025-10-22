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
define( 'DB_NAME', 'portfolio_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'y@&zFb?$c=viQHY6rI_VqTB> (pZXbC7Df@R85x2BD7:SpU?Qo<XV QV)m?eY@[s' );
define( 'SECURE_AUTH_KEY',  'SJ6,ZxO~]2U Lk..^,DOw5(jB`7],Bsv8}J]{XqyLx=aM{Im|g^I)Za!;:O$L ~L' );
define( 'LOGGED_IN_KEY',    '{-_h[ AqdSnFHIEPl/Y`59n9fpXU0@} 8%:q8_81,6,D=w,](:x;,Z~9JNL**Y[v' );
define( 'NONCE_KEY',        'LGu+;b8{wY;itrggY#y+dHx&(AMTTWm15>PPRrfi*Z+%M{C M1Ydh L^JPcf2,l~' );
define( 'AUTH_SALT',        '3?=WDJyr(xpU9yvX&lvas5q zHeZ]>w;[1A+y<Y~qAe#Ena#Vwh1YPgcmm)qzV;f' );
define( 'SECURE_AUTH_SALT', 'aEc;Jy@CG5)<@`~QriQH^oX@i;3wxm1-K&&It*bE~4~buSba&2K,-+XHnixi`ozm' );
define( 'LOGGED_IN_SALT',   ' -P0c~LHE>Y~U]Wu!G`o`-:^z:K]:9?[?7AKM5j8)A?PRHMfo<#Gnnx:Us^B{gua' );
define( 'NONCE_SALT',       'GA+O+@k-V lKF]<nEapXFloXj1`UT+gIAhDb=<3_CqZXhHDi=zD/2v-@GidZpjg6' );

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
$table_prefix = 'wp_';

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
