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
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         '7j8L%0:KjGUPe}cwB0Q*Qj9i3Y3>.+D]/MM>LkdL&Q+$`gg9w-9`yVKIv$-=a<:d' );
define( 'SECURE_AUTH_KEY',  'LZ[LGvQYH|V-LF(qNLZ]s3~@tv+M!$t9-u{no.;V~xSsmPx_hDK*zF}7o1-^/E^W' );
define( 'LOGGED_IN_KEY',    '{!HS?~f=},VxYC1JdD:/mdHz7OCi%7)6F_vOGO!9/VM@bbn=3d|.1z;z<HyAs%,}' );
define( 'NONCE_KEY',        'P!,c.mD[rY[t$^]T},^hT(Ak~|e-5?pq7RztLsRS}5WVCZ3AL5(YR%hzM?eqKd-f' );
define( 'AUTH_SALT',        'OZl]oD]IoF577qi<P<O%o(f0)t-T^p4XLIPt /W3],FfC!]D1vKZmA7hLILC6h&o' );
define( 'SECURE_AUTH_SALT', '5>Wm}8:?WhA}n/<0c.c@O#um8(F~%^Ha]}]Yby76`:5sNx=GAuiG`2_9I#qo9_QH' );
define( 'LOGGED_IN_SALT',   '`?W32InA0rGMi|7vK,kf*2jA!BQFCX r2J{JBGf_[AvUu$KNh$b|f6yvM.Tmjb*f' );
define( 'NONCE_SALT',       'q]T]<[B[blIq{wL%i;w>oexPs*KghFF;L^|u1oqr[!fdhC}Nr9g#8/`6bk.lC<D6' );

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
