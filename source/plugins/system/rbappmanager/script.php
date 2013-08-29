<?php
/**
* @copyright		Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license			GNU/GPL, see LICENSE.php
* @package			RB-APP-Manager
* @subpackage		Backend
*/
if(defined('_JEXEC')===false) die();

class plgsystemrbappmanagerInstallerScript
{
	
	/**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
	function postflight($type, $parent)
	{
		$executeOn = array('install', 'update');
		if(in_array($type, $executeOn)){
			$this->executeDBScript();
		} 
	}
	
	protected function executeDBScript()
	{
		$sqlFile = dirname(__FILE__).'/install.sql';
		$db = JFactory::getDBO();
		$buffer = file_get_contents($sqlFile);

		// Graceful exit and rollback if read not successful
		if ($buffer === false)
		{
			JLog::add(JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), JLog::WARNING, 'jerror');

			return false;
		}

		// Create an array of queries from the sql file
		$queries = JDatabase::splitSql($buffer);

		if (count($queries) == 0)
		{
			// No queries to process
			return 0;
		}

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query != '' && $query{0} != '#')
			{
				$db->setQuery($query);

				if (!$db->execute())
				{
					JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');

					return false;
				}
			}
		}
		return true;	
	}
}
