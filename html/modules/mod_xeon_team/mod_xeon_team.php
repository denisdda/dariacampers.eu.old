<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_xeon_team
 * @copyright	Copyright (C) 2010 - 2013 JoomShaper. All rights reserved.
 * @license		GNU General Public License version 2 or later; 
 */

// no direct access
defined('_JEXEC') or die;

$data = array();

//Team 1
if( $params->get('team1_img') ){
	$data[0][ 'img' ] 				= $params->get('team1_img');
	$data[0][ 'name' ] 				= $params->get('team1_name');
	$data[0][ 'desg' ] 				= $params->get('team1_desg');
}

//Team 2
if( $params->get('team2_img') ){
	$data[1][ 'img' ] 				= $params->get('team2_img');
	$data[1][ 'name' ] 				= $params->get('team2_name');
	$data[1][ 'desg' ] 				= $params->get('team2_desg');
}

//Team 3
if( $params->get('team3_img') ){
	$data[2][ 'img' ] 				= $params->get('team3_img');
	$data[2][ 'name' ] 				= $params->get('team3_name');
	$data[2][ 'desg' ] 				= $params->get('team3_desg');
}

//Team 4
if( $params->get('team4_img') ){
	$data[3][ 'img' ] 				= $params->get('team4_img');
	$data[3][ 'name' ] 				= $params->get('team4_name');
	$data[3][ 'desg' ] 				= $params->get('team4_desg');
}

//Team 5
if( $params->get('team5_img') ){
	$data[4][ 'img' ] 				= $params->get('team5_img');
	$data[4][ 'name' ] 				= $params->get('team5_name');
	$data[4][ 'desg' ] 				= $params->get('team5_desg');
}

//Team 6
if( $params->get('team6_img') ){
	$data[5][ 'img' ] 				= $params->get('team6_img');
	$data[5][ 'name' ] 				= $params->get('team6_name');
	$data[5][ 'desg' ] 				= $params->get('team6_desg');
}

require JModuleHelper::getLayoutPath('mod_xeon_team');