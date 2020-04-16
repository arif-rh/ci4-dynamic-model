<?php namespace Arifrh\DynaModel\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class DBException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingTemplateView(string $template = null)
	{
		return new static(lang('Themes.missingTemplateView', [$template]));
	}

	public static function forPluginNotRegistered(string $plugin = null)
	{
		return new static(lang('Themes.pluginNotRegistered', [$plugin]));
	}

	public static function forTableNotFound(string $table = null)
	{
		return new static(lang('DynaModel.tableNotFound', [$table]));
	}
}
