<?php 


namespace WPGodWpseopress\Models;

/**
 *
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @author Thomas DENEULIN <contact@wp-god.com> 
 * 
 */
interface GodHandlerInterface{
	/**
	 *
	 * @version 1.0.0
	 * @version 1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function godErrorHandler($code, $message, $file, $line, $ctx);	
	
	/**
	 *
	 * @version 1.0.0
	 * @version 1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function godErrorShutdownHandler();	
}
