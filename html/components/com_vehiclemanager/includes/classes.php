<?php
/**
* This file provides compatibility for VehicleManager on Joomla! 1.0.x and Joomla! 1.5
*
*/
/**
*
* @package VehicleManager
* @copyright 2013 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

JLoader::register('JPaneTabs',  JPATH_LIBRARIES. '/' .'joomla'. '/' .'html'. '/' .'pane.php');

/**
 * Legacy class, replaced by full MVC implementation.  See {@link JController}
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if ( !class_exists('mosAbstractTasker')) {
  class mosAbstractTasker
  {
	  function __construct()
	  {
		  jexit( 'mosAbstractTasker deprecated, use JController instead' );
	  }
  }
}

/**
 * Legacy class, removed
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if ( !class_exists('mosEmpty')) {
  class mosEmpty
  {
	  function def( $key, $value='' )
	  {
		  return 1;
	  }
	  function get( $key, $default='' )
	  {
		  return 1;
	  }
  }
}

/**
 * Legacy class, removed
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if ( !class_exists('MENU_Default')) {
  class MENU_Default
  {
	  function __construct()
	  {
		  JToolBarHelper::publishList();
		  JToolBarHelper::unpublishList();
		  JToolBarHelper::addNew();
		  JToolBarHelper::editList();
		  JToolBarHelper::deleteList();
		  JToolBarHelper::spacer();
	  }
  }
}

/**
 * Legacy class, use {@link JPanel} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if(version_compare(JVERSION, '3.0', 'lt')) {
    if ( !class_exists('mosTabs')) {
      class mosTabs extends JPaneTabs
      {
	      var $useCookies = false;

	      function __construct( $useCookies, $xhtml = null) {
		      parent::__construct( array('useCookies' => $useCookies) );
	      }

	      function startTab( $tabText, $paneid ) {
		      echo $this->startPanel( $tabText, $paneid);
	      }

	      function endTab() {
		      echo $this->endPanel();
	      }

	      function startPane( $tabText ){
		      echo parent::startPane( $tabText );
	      }

	      function endPane(){
		      echo parent::endPane();
	      }
      }
    }
}
