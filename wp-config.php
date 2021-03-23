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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ceekayy_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'V AG]/ 9Evwt^Wy3Tg@C0O`m1={3y+|QFr59%st*0Ml8i&{-Y}R/?}MTP+?yBay_' );
define( 'SECURE_AUTH_KEY',  'vg.vB;_+pzMj~p5!8%v`@Sj{(4 U9?s_ +E?@9ap2@:)DgeH&&#K<=>9ml}9B>{N' );
define( 'LOGGED_IN_KEY',    'R?WpkLX.gAic3Hq8{$?TETfT=<Qn3GxA9.yvdc$cZd3xv( P*}9^dV41^7Dvx-g^' );
define( 'NONCE_KEY',        ';}B/dgIMq7fCF8QmV%>QR*r3sk)1z;Gu_gMm@1j8EV._a-d;5X*C5b58VBJyRP*u' );
define( 'AUTH_SALT',        'Kd>z6m3z7O7+]]_Jn?3:$aQC4}CXKSaHuEV$kDQ!Z&V@/SVw}H#iffw!f80V^7`I' );
define( 'SECURE_AUTH_SALT', 'AEvO0(K&:iYy)caB`QLcZ-V0PvL!2B!wU4NFEAEoG-kSm@$HMO.`(Fl`Qx;wHxBG' );
define( 'LOGGED_IN_SALT',   '[>.{7CttMh-09H<OI|W`g}:(q_KDJLxWlD+fnta+3`_%oJnh&Mz[,[H<VOL7j]tC' );
define( 'NONCE_SALT',       '@f:zKP,69qJyQr*f:n$uAX!yi<?:6^.R/Z:hyd~oZN=Uf;lNLE~3K;MDdF{ZaJ+{' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
