<?php
/**
*
* @package profilesideswitcher
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace tatiana5\profilesideswitcher\migrations\v0xx;

class v_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['pss_version']) && version_compare($this->config['pss_version'], '0.0.1', '>=');
	}

	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'allow_pss_left'	=> array('BOOL', 1),
				),
			),
		);
	}

	public function revert_schema()
	{
			return array(
				'drop_columns'	=> array(
					$this->table_prefix . 'users' => array('allow_pss_left'),
				),
			);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('pss_version', '0.0.1')),
		);
	}
}
