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
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\user */
	protected $user;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var string */
	protected $phpbb_root_path;
	protected $php_ext;
	
	/**
	* Constructor
	* 
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	*/
	
	public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext)
	{
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
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
			'core.user_setup'					=> 'load_language_on_setup',
			'core.viewtopic_modify_page_title'	=> 'profile_side_switcher',
			'core.ucp_prefs_view_data'			=> 'ucp_profile_side_switcher_get',
			'core.ucp_prefs_view_update_data'	=> 'ucp_profile_side_switcher_set',
		);
	}
	
	public function load_language_on_setup($event) {
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'tatiana5/profile_side_switcher',
			'lang_set' => 'profile_side_switcher',
		);
		$event['lang_set_ext'] = $lang_set_ext;
		
		$this->template->assign_vars(array(
			'S_PSS_LEFT'	=> $this->user->data['allow_pss_left'],
		));
	}
	
	public function profile_side_switcher($event) {
		global $request;
		
		$topic_data = $event['topic_data'];
		$forum_id = $event['forum_id'];
		
		if ($request->is_ajax() && $request->is_set('pss'))
		{
			$pss_left = request_var('pss', 0);
			$sql = 'UPDATE ' . USERS_TABLE . ' SET allow_pss_left = ' . (int) $pss_left;
			$result = $this->db->sql_query($sql);
			
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success'		=> ($result) ? true : false,
			));
			
			$this->db->sql_freeresult($result);
		}
		
		$this->template->assign_vars(array(
			'PSS_URL_LEFT'		=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", 'f=' . $forum_id . '&amp;t='. $topic_data['topic_id'] . '&amp;pss=1'),
			'PSS_URL_RIGHT'		=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", 'f=' . $forum_id . '&amp;t='. $topic_data['topic_id'] . '&amp;pss=0'),
		));
	}
	
	public function ucp_profile_side_switcher_get($event) {
		$data = $event['data'];
		$data = array_merge($data, array(
			'allow_pss_left'		=> request_var('pss_left', (bool) (isset($this->user->data['allow_pss_left']) ? $this->user->data['allow_pss_left'] : false))
		));
		$event['data'] = $data;
	}
	
	public function ucp_profile_side_switcher_set($event) {
		$data = $event['data'];
		$sql_ary = $event['sql_ary'];
		if (isset($this->user->data['allow_pss_left']))
		{
			$sql_ary = array_merge($sql_ary, array(
				'allow_pss_left'	=> ($data['allow_pss_left']) ? 1 : 0,
			));
		}
		$event['sql_ary'] = $sql_ary;
	}
}
