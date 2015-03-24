<?php

namespace McAskill\Substrate;

/**
 * Substrate Module Repository
 *
 * @todo Implement ArrayAccess
 */
class ModuleRepository
{
	/**
	 * Repository prefix.
	 *
	 * @var string
	 */
	const PREFIX = 'substrate';

    /**
     * The base path for the Substrate plugin.
     *
     * @var string
     */
    protected $basePath;

	/**
	 * All of the feature modules.
	 *
	 * @var array
	 */
	protected static $modules = [];

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Create a new Illuminate application instance.
     *
     * @param string|null  $basePath
     */
    public function __construct($basePath = null)
    {
        if ( $basePath ) {
            $this->setBasePath($basePath);
        }
    }

    /**
     * Set the globally available instance of the repository.
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set the shared instance of the repository.
     *
     * @param ModuleRepository  $repository  A Substrate module repository.
     */
    public static function setInstance(ModuleRepository $repository)
    {
        static::$instance = $repository;
    }

    /**
     * Set the base path for the plugin.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        return $this;
    }

    /**
     * Get the base path of the Substrate plugin.
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Get the path to the module files.
     *
     * @return string
     */
    public function modulePath()
    {
        return $this->basePath.'/modules';
    }

	/**
	 * Load Substrate modules and utilities from theme features.
	 *
	 * @uses WP\$_wp_theme_features;
	 */
	public function loadModules()
	{
		global $_wp_theme_features;

		$prefix = self::PREFIX . '-';

		foreach ( glob($this->modulePath() . '/{*.php,*/}', GLOB_BRACE) as $path ) {
			$feature = $prefix . basename($path, '.php');

			if ( isset($_wp_theme_features[$feature]) ) {
				$this->add($feature, $path, (array) $_wp_theme_features[$feature]);
			}

			# require_once $path;
		}
	}

	/**
	 * Add a given module.
	 *
	 * @param  string  $module   The module name.
	 * @param  string  $path     The module's path.
	 * @param  array   $options  The module's options.
	 * @return Module
	 */
	public function add($module, $path, array $options = [])
	{
		if ( ! isset(self::$modules[$module]) ) {
			self::$modules[$module] = new Module($module, $path, $options);
		}

		return self::$modules[$module];
	}

	/**
	 * Get a specified module.
	 *
	 * @param  string  $module  The module name.
	 * @return Module
	 */
	public function get($module)
	{
		if ( isset(self::$modules[$module]) ) {
			return self::$modules[$module];
		}

		$prefix = self::PREFIX . '-';
		$length = strlen(self::PREFIX);

		if (substr($module, 0, $length) !== $prefix) {
			return self::get($prefix . $module);
		}

		return null;
	}

	/**
	 * Get a module by its path.
	 *
	 * @param  [type] $file [description]
	 * @return Module
	 */
	public function getByFile($file)
	{
		if ( file_exists($file) || file_exists($this->modulePath() . '/' . $file) ) {
			$prefix = self::PREFIX . '-';

			return self::get($prefix . basename($file, '.php'));
		}

		return null;
	}
}
