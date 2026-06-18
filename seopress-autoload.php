<?php

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

spl_autoload_register(
	function ( $class ) {
		// project-specific namespace prefix
		$prefix = 'SEOPress\\';

		// base directory for the namespace prefix
		$base_dir = __DIR__ . '/src/';

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			// no, move to the next registered autoloader
			return;
		}

		// get the relative class name
		$relative_class = substr( $class, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		// PHP re-invokes registered autoloaders every time class_exists()/defined()
		// is called on a still-undefined class. The src/Thirds tree contains
		// function-only files (no class declared) that would otherwise be
		// require()'d twice and fatal on the second pass.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);
