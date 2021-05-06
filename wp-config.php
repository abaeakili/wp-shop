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
define( 'DB_NAME', 'wp-shop' );

/** MySQL database username */
define( 'DB_USER', 'wp-shop' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wp-shop' );

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
define( 'AUTH_KEY',         'awPQhtc85ww=A)xIUHQjzzC2C/#WdTu]xB <N^Y1n5yA8]*`#}TP7b![9Ld{7gBw' );
define( 'SECURE_AUTH_KEY',  '1.DmkJ7r%UKbQtc8UMKIu!NRTStf8KUEt:>Zy|f]mC;tRL)#=>]g+0-SoQA[(v5+' );
define( 'LOGGED_IN_KEY',    'KYR5!qMLHrZ_56#c8IG+@~Gp}WrX7mG`tq0ZT<sp3QBT6FBuy^?[$8db[NPh320X' );
define( 'NONCE_KEY',        '&v{PWO3Ad4NpY vSa7#9%Hb;g<[u:v0LDZ,6MFFr0X-jxXI^+ S|k-B6K{r{Ic17' );
define( 'AUTH_SALT',        'b,7;Zf82*K.x()}|(Ieo$/h@}9y4c8wWAr0LGP/^~6c4yvku4?BV<B$$*CjH3zh!' );
define( 'SECURE_AUTH_SALT', '^<k ezH=yQ]vndE{J-Kg$ugV966>lz~ntmMc?-fs$Ohft%Yg1OH(^Q=Nteyg,S1%' );
define( 'LOGGED_IN_SALT',   ')n}`+|p-s*>xMx:/`z:Sw#hLfv AIl7qyFxcVH7PBSxAy%[XvRX j#<z`:iBbKL*' );
define( 'NONCE_SALT',       'd=>4xruRNd2J,%WvmgZEC.fZz>8OET3q/4gRg :([0^kjbu-=~5tXA}I>x+4}@FE' );

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
