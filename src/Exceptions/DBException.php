<?php namespace Arifrh\DynaModel\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

final class DBException extends FrameworkException implements ExceptionInterface
{
	/**
	 * @return DBException
	 */
	public static function forTableNotFound(string $table = null)
	{
		return new static(lang('DynaModel.tableNotFound', [$table]));
	}
}
