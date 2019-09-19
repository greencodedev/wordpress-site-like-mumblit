<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mumblit_support' );

/** MySQL database username */
define( 'DB_USER', 'mumblit_test' );

/** MySQL database password */
define( 'DB_PASSWORD', '!Ilovemee1' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Q`@_Bh2EzLH{^5VnYdh. W@# v`G[)1hk~4,#l[.DFTasU?Vj`3$j.~ZIheWT)BM' );
define( 'SECURE_AUTH_KEY',  '$98GxX{4HJq6qh/-FAKXeb* Kb}Bcf$KrrZETw|yHKp%4@GL=]{IUH;Dw2tZJ@KN' );
define( 'LOGGED_IN_KEY',    '_&&&dmj9@[RX>|(<6^Tp[.;Ld5Vx;yWYQIWxmg@I<zEfj_xTy9Y-<(DsOMM>;{<)' );
define( 'NONCE_KEY',        '@.9c1hZn:o[/+x.=O6qyq[rZ.q:1h`M0vt*<l<6b[]50{D~E)+0ww2Dxwa_}.dci' );
define( 'AUTH_SALT',        'E!zacyjVV3r1BXHb}G:Y-q.ko|y@8;p}U=Ls#AL~GZFkhp{ub6)1Iv{<07fi;!6x' );
define( 'SECURE_AUTH_SALT', 'p2Ws;(H]+ti1#kD]U >s_Q_sWwW8l2~i,ikt5$@Wa+HS84:8Fpmdb7(m}MfS?x0m' );
define( 'LOGGED_IN_SALT',   'q0=u+!#/mr;%9cx8-kv#P>_&6]W$rFpZ:Qc&3DVd:vlqojVe!T0v=qfP896vzn)M' );
define( 'NONCE_SALT',       'nl<~Mm&z#!t6*o,^x`Q&xEUr26f;t;s:UR9Ua-MEk~{nxa8}|RKI5Icpec>lDbjJ' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'su_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
