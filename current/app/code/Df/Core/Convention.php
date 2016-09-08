<?php
namespace Df\Core;
// инструмент парадигмы «convention over configuration»
class Convention extends O {
	/**
	 * Проверяет наличие следующих классов в указанном порядке:
	 * 1) <имя конечного модуля>\<окончание класса>
	 * 2) $defaultResult
	 * Возвращает первый из найденных классов.
	 * @param object $caller
	 * @param string $classSuffix
	 * @param string|null $defaultResult [optional]
	 * @param bool $throwOnError [optional]
	 * @return string|null
	 */
	public function getClass($caller, $classSuffix, $defaultResult = null, $throwOnError = true) {
		df_param_string_not_empty($classSuffix, 1);
		/** @var string $callerClassName */
		$callerClassName = get_class($caller);
		/** @var string $cacheKey */
		$cacheKey = implode('::', [$callerClassName, $classSuffix]);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var string $callerModuleName */
			$callerModuleName = df_module_name($callerClassName, '\\');
			/** @var string $class */
			$class = df_cc_class($callerModuleName, $classSuffix);
			/** @var string|null $result */
			if (@class_exists($class)) {
				$result = $class;
			}
			else {
				if ($defaultResult) {
					df_param_string_not_empty($defaultResult, 2);
					$result = $defaultResult;
				}
				else {
					if ($throwOnError) {
						df_error('Системе требуется класс «%s».', $class);
					}
					else {
						$result = null;
					}
				}
			}
			$this->{__METHOD__}[$cacheKey] = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__}[$cacheKey]);
	}

	/** @return Convention */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}