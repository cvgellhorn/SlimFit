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
	private function __construct()
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
	 * Add item to given item list
	 *
	 * @param array $items Items
	 * @param string $key Item key
	 * @param null | string $value Item to add
	 * @param null | string $after Insert new item after given item key
	 */
	public function addItem(&$items, $key, $value = null, $after = null)
	{
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$items[$k] = $v;
			}
		} else {
			if ($after) {
				// Remove item if exists
				unset($items[$key]);

				$pos = array_search($after, array_keys($items)) + 1;
				array_splice($items, $pos, 0, [$key]);

				$items = array_combine(
					array_replace(array_keys($items), [$pos => $key]),
					array_values($items)
				);
			} else {
				$items[$key] = $value;
			}
		}
;	}

	/**
	 * Get items HTML DOM templates
	 *
	 * @param array $data Items to implode
	 * @param string $template HTML DOM template
	 * @return string Filled DOM template
	 */
	public function getItemDom(&$data, $template)
	{
		$items = [];
		foreach ($data as $val) {
			$items[] = str_replace('?', $val, $template);
		}

		return (!empty($items)) ? implode(PHP_EOL, $items) : '';
	}

	/**
	 * Add HTML DOM stylesheet
	 *
	 * @param string $key Stylesheet key
	 * @param null | string $style Stylesheet
	 * @param null | string $after Insert new style after given style key
	 */
	public function addCSS($key, $style = null, $after = null)
	{
		$this->addItem($this->css, $key, $style, $after);
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
		return $this->getItemDom($this->css, '<link rel="stylesheet" type="text/css" href="?">');
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
		$this->addItem($this->js, $key, $script, $after);
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
		return $this->getItemDom($this->js, '<script type="text/javascript" src="?"></script>');
	}
}