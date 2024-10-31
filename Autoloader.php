<?php

/**
 * RefPress Class Autoloader
 *
 * @since RefPress 1.0.0
 */

namespace RefPress;

class Autoloader {

	/**
	 * Autoload function for registration with spl_autoload_register
	 *
	 * Looks recursively through project directory and loads class files based on
	 * filename match.
	 *
	 * @param  string  $className
	 */

	public static function loader( $className ) {
		if ( ( false === strpos( $className, __NAMESPACE__ ) ) || class_exists( $className ) ) {
			return;
		}

		$classDir = str_replace( array( __NAMESPACE__ . '\\',  __NAMESPACE__.'/', 'RefPressPro\\', 'RefPressPro/' ), '', $className );

		$fullFileName = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $classDir );

		if ( strpos( $fullFileName, DIRECTORY_SEPARATOR ) !== false ) {
			$splitClassName = explode( DIRECTORY_SEPARATOR, $fullFileName );
			$classFileName  = array_pop( $splitClassName );
			$fullFileName   = strtolower( implode( DIRECTORY_SEPARATOR, $splitClassName ) ) . DIRECTORY_SEPARATOR . $classFileName;
		}

		$fullFilePath = dirname( REFPRESS_FILE ) . DIRECTORY_SEPARATOR . $fullFileName . '.php';

		//Check if the class comes from the pro plugin
		if (  false !== strpos( $className, 'RefPressPro' ) ) {
			//Changing fullFilePath to the pro directory.
			$fullFilePath = dirname( REFPRESS_PRO_FILE ) . DIRECTORY_SEPARATOR . $fullFileName . '.php';
		}

		if ( file_exists( $fullFilePath ) ) {
			include_once $fullFilePath;
		}
	}
}

spl_autoload_register('\RefPress\Autoloader::loader');