<?php
/**
* This file provides compatibility for VehicleManager on Joomla! 1.0.x and Joomla! 1.5
*
*/


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

// Register legacy classes for autoloading
JLoader::register('JTableCategory' , JPATH_LIBRARIES. '/' .'joomla'. '/' .'database'. '/' .'table'. '/' .'category.php');

/**
 * Legacy class, use {@link JTableCategory} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if ( !class_exists('mosCategory')) {
  class mosCategory extends JTableCategory
  {
	  /**
	  * Constructor
	  */
	  function __construct( &$db)
	  {
		  parent::__construct( $db );
	  }
  
	  function mosCategory(&$db)
	  {
		  parent::__construct( $db );
	  }
  
	  /**
	  * Legacy Method, use {@link JTable::reorder()} instead
	  * @deprecated As of 1.5
	  */
	  function updateOrder( $where='' )
	  {
		  return $this->reorder( $where );
	  }
  
	  /**
	  * Legacy Method, use {@link JTable::publish()} instead
	  * @deprecated As of 1.0.3
	  */
	  function publish_array( $cid=null, $publish=1, $user_id=0 )
	  {
		  $this->publish( $cid, $publish, $user_id );
	  }
  }
}
