<?php 

/**
 * Base Model which using DynaModelTrait 
 * Give it Relationship Feature built in
 */

namespace Arifrh\DynaModel\Models;

use CodeIgniter\Model;
use Arifrh\DynaModel\Models\DynaModelTrait;

class DynaModel extends Model
{
    use DynaModelTrait;

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
	{
        parent::__construct($db, $validation);

        if (!empty($this->table))
        {
            $this->initialize($this->table);
        }
    }
}