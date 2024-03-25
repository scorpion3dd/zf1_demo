<?php
/**
 * Class Zf1_Helper_Server
 */
class Zf1_Helper_Server
{
    /**
     * @return bool
     */
    public static function isApache()
    {
        return strpos(strtolower($_SERVER['SERVER_SOFTWARE']), strtolower('Apache')) !== false;
    }

    /**
     * @return bool
     */
    public static function isNginx()
    {
        return strpos(strtolower($_SERVER['SERVER_SOFTWARE']), strtolower('Nginx')) !== false;
    }
}
