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
define( 'DB_NAME', 'pingfm_ip_test' );

/** Database username */
define( 'DB_USER', 'pingfm' );

/** Database password */
define( 'DB_PASSWORD', '3Ld^nMd3AC@qXP%' );

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
define( 'AUTH_KEY',         'AQd9gPt8pl%PTwt/V$m``N|x2:?7)4@FSJUpZ&}j[%m ;>uKK|ySBRSg~DGGPTb0' );
define( 'SECURE_AUTH_KEY',  'ad6{7u;3AXF=bkxG`mN<v.DM>Wg&t5zf0Y2O?cFLT#@-vSa^R$mSR~}x]sa*7%3Z' );
define( 'LOGGED_IN_KEY',    'CvcIhAD1tX_YcJdzC|09:Kgz<N=]^bzlG*1o|aSegsxG4I8jWPb=Px4^wHJq=b.s' );
define( 'NONCE_KEY',        'k$k&U69&O=vtJibSa$A%#soz!3%_5svj;,6.A[-1FcJVeS$4{dU^i9}alffL_Wku' );
define( 'AUTH_SALT',        'CJ{7$LbZQle6%-H0-:p)fgvU^M3f6[O}h}%a}R^1D`-1ss/>Wc-HyL{fmdQERv[j' );
define( 'SECURE_AUTH_SALT', 'tOg7p`[SQm4IT%QI^L.Ok+Rax-3M=*qvDf59_ ?o7L3+r% AK@8O9rl}7b c4@T8' );
define( 'LOGGED_IN_SALT',   '<x< qunYj/3xf,c0>2K;Ddmf<{,jxM|%9,/X<6_6#6ZG$w~:D7Y@$CP;FjBn:YV.' );
define( 'NONCE_SALT',       'z5$vl6_~USDturAtC,1$+>GR_~-k<B?6W0s]R](?rL{ye%qt1U)bXGFjG*YSrlDQ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ip_';

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
