<?php

namespace Arifrh\DynaModel;

use \CodeIgniter\Config\Config;
use Arifrh\DynaModel\Exceptions\DBException;
use Arifrh\DynaModel\Models\DynaModel;

/**
 *  Starter Kit for creating composer package
 *
 * @author Arif RH
 */
class DB
{
	/**
	 * @param string      $table      table name to be created as model
	 * @param null|string $primaryKey if omit, it will be autodetect based on primary key field in table
	 * @param null|string $DBGroup    database group, if omit will use defaultGroup from config
	 *
	 * @return DynaModel
	 */
	public static function table($table = null, $primaryKey = null, $DBGroup = null)
	{
		if (is_null($DBGroup))
		{
			$config  = Config::get('Database');
			$DBGroup = $config->defaultGroup;
		}

		$db = \Config\Database::connect($DBGroup);

		if (! empty($table) && $db->tableExists($table))
		{
			$dataModel = new DynaModel($db);

			$dataModel->setTable($table, $primaryKey, ['DBGroup' => $DBGroup]);

			return $dataModel;
		}
		else
		{
			throw DBException::forTableNotFound($table);
		}
	}
}
