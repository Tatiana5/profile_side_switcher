<?php
/**
*
* profileSideSwitcher [Russian]
*
* @package language profileSideSwitcher
* @copyright (c) 2014 Татьяна5
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
	'PSS_LEFT'			=> 'Минипрофили слева',
	'PSS_RIGHT'			=> 'Минипрофили справа',
	'PSS_VIEW_LEFT'		=> 'Показывать минипрофили слева',
));
