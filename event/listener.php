<?php
/**
*
* @package profile_side_switcher
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tatiana5\profile_side_switcher\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{	
	/** @var \phpbb\user */
	protected $user;
	
	/**
	* Constructor
	* 
	* @param \phpbb\user $user
	*/
	
	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}
	
	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header'	=>	'profile_side_switcher',
		);
	}
	
	public function profile_side_switcher($event) {
		$this->user->add_lang_ext('tatiana5/profile_side_switcher', 'profile_side_switcher');
	}
}
