<?php

/**
 * Base Model which using DynaModelTrait
 * Give it Relationship Feature built in
 */

namespace Arifrh\DynaModel\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Model;
use Arifrh\DynaModel\Models\DynaModelTrait;

/**
 * DynaModel - BaseModel that use DynaModelTrait
 *
 * @package Arifrh\DynaModel
 */
class DynaModel extends Model
{
	use DynaModelTrait;

	/**
	 * Overide Model constructor, to initialize the model
	 *
	 * @param \CodeIgniter\Database\BaseConnection        $db
	 * @param \CodeIgniter\Validation\ValidationInterface $validation
	 *
	 * @return void
	 */
	public function __construct(BaseConnection &$db = null, ValidationInterface $validation = null)
	{
		parent::__construct($db, $validation);

		if (! empty($this->table))
		{
			$this->initialize($this->table);
		}
	}
}
