<?php 

namespace Arifrh\DynaModel;

use Arifrh\DynaModel\Exceptions\DBException;
/**
*  Starter Kit for creating composer package
*
*  @author Arif RH
*/
class DB
{
    public static function table($table = null, $primaryKey = null, $DBGroup = null)
    {
        if (is_null($DBGroup))
        {
            $config  = config('Database');
            $DBGroup = $config->defaultGroup; 
        }

        $db = \Config\Database::connect($DBGroup);

        if (!empty($table) && $db->tableExists($table))
        {
            $dataModel = model('Arifrh\DynaModel\Models\DynaModel', false, $db);

            $dataModel->setTable($table, $primaryKey, ['DBGroup' => $DBGroup]);

            return $dataModel;
        }
        else 
        {
           throw DBException::forTableNotFound($table);
        }
    }
}