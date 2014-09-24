<?php namespace SlimFit;

use SlimFit\Error;

/**
 * Layout Handler
 *
 * @author cvgellhorn
 */
class Layout
{
	/**
	 * Single pattern implementation
	 *
	 * @return Layout Instance
	 */
	private static $_instance = null;

	/**
	 * HTML DOM title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * HTML DOM meta data
	 *
	 * @var array
	 */
	public $meta = [];

	/**
	 * HTML DOM stylesheets
	 *
	 * @var array
	 */
	public $css = [];

	/**
	 * HTML DOM scripts
	 *
	 * @var array
	 */
	public $js = [];

	/**
	 * Single pattern implementation
	 *
	 * @return Layout Instance
	 */
	public static function load()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Layout Constructor, sets default title
	 */
	public function __construct()
	{
		$this->title = Config::get('app_name');
	}

	/**
	 * Load view from controller action with default layout
	 *
	 * @throws Error If main template not exists
	 */
	public function loadView()
	{
		$layoutFile = Config::get('layout') . '.phtml';
		$layoutPath = APP_DIR . DS . 'layout' . DS . $layoutFile;

		if (file_exists($layoutPath)) {
			require_once($layoutPath);
		} else {
			throw new Error('Layout template does not exists: ' . $layoutFile);
		}
	}

	/**
	 * Add HTML DOM stylesheet
	 *
	 * @param string $key Stylesheet key
	 * @param string|null $style Stylesheet
	 * @param string|null $after Stylesheet position
	 */
	public function addCSS($key, $style = null, $after = null)
	{
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->css[$k] = $v;
			}
		} else {
			$this->css[$key] = $style;
		}
	}

	/**
	 * Remove stylesheet from list
	 *
	 * @param string $key Stylesheet key
	 */
	public function removeCSS($key)
	{
		unset($this->css[$key]);
	}

	/**
	 * Build and return the HTML DOM stylesheets
	 *
	 * @return string HTML DOM stylesheets
	 */
	public function getCSSDom()
	{
		$template = '<link rel="stylesheet" type="text/css" href="?" >';

		$styles = [];
		foreach ($this->js as $style) {
			$styles[] = str_replace('?', $style, $template);
		}

		return (!empty($styles)) ? implode(PHP_EOL, $styles) : '';
	}

	/**
	 * Add HTML DOM script
	 *
	 * @param string $key Script key
	 * @param string|null $script Script
	 * @param string|null $after Script position
	 */
	public function addJS($key, $script = null, $after = null)
	{
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->js[$k] = $v;
			}
		} else {
			$this->js[$key] = $script;
		}
	}

	/**
	 * Remove script from list
	 *
	 * @param string $key Script key
	 */
	public function removeJS($key)
	{
		unset($this->js[$key]);
	}

	/**
	 * Build and return the HTML DOM scriptss
	 *
	 * @return string HTML DOM scripts
	 */
	public function getJSDom()
	{
		$template = '<script type="text/javascript" src="?"></script>';

		$scripts = [];
		foreach ($this->js as $script) {
			$scripts[] = str_replace('?', $script, $template);
		}

		return (!empty($scripts)) ? implode(PHP_EOL, $scripts) : '';
	}
}