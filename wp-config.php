<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'seasparrow' );

/** Database username */
define( 'DB_USER', 'seasparrow-admin' );

/** Database password */
define( 'DB_PASSWORD', 'seasparrow143' );

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
define( 'AUTH_KEY',         'fBJ}S=EY|)Z1?^TZg|*(E4/WoU%|%05n|LIcAuP*B<ILGBH&l@0cr4xn`$:~K@41' );
define( 'SECURE_AUTH_KEY',  'u*P1ZEG>iFezU4jHQPZ%[|j:/swR/cp9/daoiWB9!3cQkaK4BLFwz@<BG2fdL#x$' );
define( 'LOGGED_IN_KEY',    'a6b79,rR,qP#[uJ`tHc nUj/7@F=YJQ,q0KUz#SL{6s=hIVwC[=-o#zHredtGk^E' );
define( 'NONCE_KEY',        'ZHlA|}O>p(`2fJ;7EY4oa^gvlWfjOS;-BCS|/kqrD`*;fX#+M<[@OY.OQ<6[LQkG' );
define( 'AUTH_SALT',        'DVC4tYna@0pSmI%^}*F|JSCAXc}m:x=%:~0nU;4lt9<Uv|!._Qa:=|*lO05YW^kb' );
define( 'SECURE_AUTH_SALT', 'Sx0V{5#A 1_/+l=oo  UN].82`o$2zU&V;=P>_J&~-H $MqUSV2IL7K%oC50=O^0' );
define( 'LOGGED_IN_SALT',   'M5i(n1Pn>Pvql^L_WYo22aK]]^pFcq<Lkb.#a :iVu0inw{^_8m3_K,cSC%*XQI#' );
define( 'NONCE_SALT',       '#1[K0!TXZGyvE+mEiIhs7rj~gK0x76mC=lK;`~$M<`GI2(mGa^[fN|koZOQQR+mJ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
