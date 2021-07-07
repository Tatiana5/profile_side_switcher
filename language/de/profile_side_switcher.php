<?php
/**
*
* profileSideSwitcher [German]
*
* @package language profileSideSwitcher
* @copyright (c) 2015 tas2580
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'PSS_LEFT'			=> 'Profil links',
	'PSS_RIGHT'			=> 'Profil rechts',
	'PSS_VIEW_LEFT'		=> 'Zeige das Profil auf der linken Seite',
));
