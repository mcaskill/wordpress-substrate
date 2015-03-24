<?php

namespace McAskill\Substrate;

use Exception;

/**
 * Substrate Module
 */
class Module
{
	/**
	 * The name of the module.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The path to the module.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * All of the feature's options.
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Create new Substrate Module object.
	 *
	 * @param  string  $module   The module name.
	 * @param  string  $path     The module's path.
	 * @param  array   $options  The module's options.
	 */
	public function __construct($module, $path, array $options = [])
	{
		$this->name    = $module;
		$this->path    = $path;
		$this->options = $options;

		$this->loadFile();
	}

	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Load a given module.
	 *
	 * @param  string  $module   The module name.
	 * @param  string  $path     The module's path.
	 * @param  array   $options  The module's options.
	 * @return self
	 */
	private function loadFile()
	{
		if ( is_null($this->path) ) {
			throw new Exception('The module has no path.');
		}

		if ( is_dir($this->path) ) {
			$basePath = rtrim($this->path, '/');
			$options  = ( is_array($this->options[0]) ? $this->options[0] : $this->options );

			foreach ( $options as $module ) {
				if ( is_string($module) ) {
					$file = $basePath.'/'.$module.'.php';

					if ( is_file($file) ) {
						require_once $file;
					}
				}
			}
		} else {
			require_once $this->path;
		}

		return $this;
	}
}

/**
 * Scope isolated include.
 *
 * Prevents access to $this/self from included files.
 */
function requireFile($file)
{
    require_once $file;
}
