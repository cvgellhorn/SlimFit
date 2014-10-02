<?php namespace SlimFit;

/**
 * SlimFit Debug Class (based on Zend Framework 1.12 Zend_Debug.php)
 * 
 * @author cvgellhorn
 */
class Debug
{
	/**
     * @var string
     */
    protected static $_sapi = null;

    /**
     * Get the current value of the debug output environment.
     * This defaults to the value of PHP_SAPI.
     *
     * @return string;
     */
    public static function getSapi()
    {
        if (self::$_sapi === null) {
            self::$_sapi = PHP_SAPI;
        }

        return self::$_sapi;
    }

    /**
     * Set the debug ouput environment.
     * Setting a value of null causes Zend_Debug to use PHP_SAPI.
     *
     * @param string $sapi
     * @return void;
     */
    public static function setSapi($sapi)
    {
        self::$_sapi = $sapi;
    }
	
	/**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var   The variable to dump.
     * @param  string $label OPTIONAL Label to prepend to output.
     * @param  bool   $echo  OPTIONAL Echo output if true.
     * @return string
     */
    public static function dump($var, $label = null, $echo = true)
    {
        // Format the label
        $label = ($label === null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // Neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (self::getSapi() == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $flags = ENT_QUOTES;

                // PHP 5.4.0+
                if (defined('ENT_SUBSTITUTE')) {
                    $flags = ENT_QUOTES | ENT_SUBSTITUTE;
                }
                $output = htmlspecialchars($output, $flags);
            }

            $output = '<pre>'
                    . $label
                    . $output
                    . '</pre>';
        }

        if ($echo) echo $output;

        return $output;
    }
}