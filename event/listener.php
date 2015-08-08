<?php
/**
*
* @package profileSideSwitcher
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tatiana5\profileSideSwitcher\event;

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

	/** @var \phpbb\request\request */
	protected $request;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $ext_name;

	/**
	* Constructor
	*
	* @param \phpbb\template\template          $template
	* @param \phpbb\user                       $user
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\request\request            $request
	* @param string                            $phpbb_root_path
	* @param string                            $php_ext
	*/

	public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, $phpbb_root_path, $php_ext)
	{
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->ext_name = "tatiana5/profileSideSwitcher";
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
			'core.user_setup'							=> 'load_language_on_setup',
			'core.page_header_after'					=> 'generate_paths',
			'core.viewtopic_modify_page_title'			=> 'profile_side_switcher',
			'core.ucp_prefs_view_data'					=> 'ucp_profile_side_switcher_get',
			'core.ucp_prefs_view_update_data'			=> 'ucp_profile_side_switcher_set',
			'core.acp_users_prefs_modify_data'			=> 'acp_profile_side_switcher_get',
			'core.acp_users_prefs_modify_template_data'	=> 'acp_profile_side_switcher_template',
			'core.acp_users_prefs_modify_sql'			=> 'ucp_profile_side_switcher_set', // For the ACP.
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'tatiana5/profileSideSwitcher',
			'lang_set' => 'profile_side_switcher',
		);
		$event['lang_set_ext'] = $lang_set_ext;

		$this->template->assign_vars(array(
			'S_PSS_LEFT'	=> $this->user->data['allow_pss_left'],
		));
	}

	public function generate_paths($event)
	{
		$ext_style_path = $this->phpbb_root_path . 'ext/' . $this->ext_name . '/styles/';
		$style_lang_path = rawurlencode($this->user->style['style_path']) . '/theme/' . $this->user->lang_name . '/profile_side_switcher.css';
		if (!file_exists($ext_style_path . $style_lang_path))
		{
			// Fallback to English language.
			$style_lang_path = rawurlencode($this->user->style['style_path']) . '/theme/en/profile_side_switcher.css';
			if (!file_exists($ext_style_path . $style_lang_path))
			{
				// Fallback to prosilver (prosilver does not need to be installed on the board; but the style file for prosilver exists in this extension).
				$style_lang_path = 'prosilver/theme/en/profile_side_switcher.css';
			}
		}

		$this->template->assign_vars(array(
			'T_PSS_STYLESHEET_LANG_LINK'	=> $style_lang_path,
		));
	}

	public function profile_side_switcher($event)
	{
		$topic_data = $event['topic_data'];
		$forum_id = $event['forum_id'];

		if ($this->request->is_set('pss'))
		{
			$pss_left = $this->request->variable('pss', 0);
			$sql = 'UPDATE ' . USERS_TABLE . ' SET allow_pss_left = ' . (int) $pss_left . ' WHERE user_id = ' . (int) $this->user->data['user_id'];
			$result = $this->db->sql_query($sql);

			if ($this->request->is_ajax())
			{
				$json_response = new \phpbb\json_response;
				$json_response->send(array(
					'success'		=> ($result) ? true : false,
				));
			}

			$this->db->sql_freeresult($result);
		}

		$this->template->assign_vars(array(
			'PSS_URL_LEFT'		=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", 'f=' . $forum_id . '&amp;t='. $topic_data['topic_id'] . '&amp;pss=1'),
			'PSS_URL_RIGHT'		=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", 'f=' . $forum_id . '&amp;t='. $topic_data['topic_id'] . '&amp;pss=0'),
		));
	}

	public function ucp_profile_side_switcher_get($event)
	{
		$data = $event['data'];
		$data = array_merge($data, array(
			'allow_pss_left'		=> $this->request->variable('pss_left', (int) (isset($this->user->data['allow_pss_left']) ? $this->user->data['allow_pss_left'] : 0))
		));
		$event['data'] = $data;
	}

	public function acp_profile_side_switcher_get($event)
	{
		$data = $event['data'];
		$user_row = $event['user_row'];
		$data = array_merge($data, array(
			'allow_pss_left'		=> $this->request->variable('pss_left', (int) (isset($user_row['allow_pss_left']) ? $user_row['allow_pss_left'] : 0))
		));
		$event['data'] = $data;
	}

	public function acp_profile_side_switcher_template($event)
	{
		$data = $event['data'];
		$user_prefs_data = $event['user_prefs_data'];
		$user_prefs_data = array_merge($user_prefs_data, array(
			'S_USER_PSS_LEFT'		=> $data['allow_pss_left'],
		));
		$event['user_prefs_data'] = $user_prefs_data;
	}

	public function ucp_profile_side_switcher_set($event)
	{
		$data = $event['data'];
		$sql_ary = $event['sql_ary'];
		$sql_ary = array_merge($sql_ary, array(
			'allow_pss_left'	=> $data['allow_pss_left'],
		));
		$event['sql_ary'] = $sql_ary;
	}
}
