<?php
// @codeCoverageIgnoreStart
if (! function_exists('array_group_by'))
{
	// @codeCoverageIgnoreEnd
	/**
	 * Groups an array by a given key.
	 *
	 * Groups an array into arrays by a given key, or set of keys, shared between all array members.
	 *
	 * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
	 * This variant allows $key to be closures.
	 *
	 * @param mixed[] $array   The array to have grouping performed on.
	 * @param mixed   $key,... The key to group or split by. Can be a _string_,
	 *                         an _integer_, a _float_, or a _callable_.
	 *
	 *                         If the key is a callback, it must return
	 *                         a valid key from the array.
	 *
	 *                         If the key is _NULL_, the iterated element is skipped.
	 *
	 *                         ```
	 *                         string|int callback ( mixed $item )
	 *                         ```
	 *
	 * @return mixed[]|null Returns a multidimensional array or `null` if `$key` is invalid.
	 */
	function array_group_by(array $array, $key)
	{
		// @codeCoverageIgnoreStart
		if (! is_string($key) && ! is_int($key) && ! is_float($key) && ! is_callable($key))
		{
			trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
			return null;
		}
		// @codeCoverageIgnoreEnd

		$func = (! is_string($key) && is_callable($key) ? $key : null);
		$_key = $key;

		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($array as $value)
		{
			$key = null;

			if (is_callable($func))
			{
				$key = call_user_func($func, $value);
			}
			elseif (is_string($_key) && is_object($value) && property_exists($value, $_key))
			{
				$key = $value->{$_key};
			}
			elseif (is_string($_key) && is_array($value) && isset($value[$_key]))
			{
				$key = $value[$_key];
			}

			if ($key === null)
			{
				continue;
			}

			$grouped[$key][] = $value;
		}

		// @codeCoverageIgnoreStart
		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2)
		{
			$args = func_get_args();

			foreach ($grouped as $key => $value)
			{
				$params        = array_merge([ $value ], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func('array_group_by', ...$params);
			}
		}
		// @codeCoverageIgnoreEnd

		return $grouped;
	}
}

// @codeCoverageIgnoreStart
if (! function_exists('array_key_value'))
{
	// @codeCoverageIgnoreEnd
	/**
	 * Geneerat key-value pairs from array
	 * Example usage for generating options for dropdown
	 *
	 * @param mixed[] $array         array source data
	 * @param mixed[] $keyPairs      key-value pair array to be used in result
	 * @param mixed[] $initialReturn initial return array
	 * @param string  $separator     this will be used to be used as separator when has multiple value
	 *
	 * @return mixed[]
	 */
	function array_key_value(array $array, array $keyPairs, $initialReturn = [], $separator = ''): array
	{
		foreach ($array as $data)
		{
			$data   = (array) $data;
			$values = explode(',', current($keyPairs));

			$text = [];

			foreach ($values as $value)
			{
				$text[] = $data[trim($value)];
			}

			if (isset($data[key($keyPairs)]))
			{
				$initialReturn[$data[key($keyPairs)]] = implode($separator, $text);
			}
		}

		return $initialReturn;
	}
}
