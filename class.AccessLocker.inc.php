<?php

/**
 * A PHP crontab access locker
 *
 * @author Uli Pahlke <github@matrix-tools.de>
 * @link http://www.matrix-tools.de
 * @version 0.1
 */

/** Usage
$al = new AccessLocker();
if ($al->setAccessLocker('process1') == false) {
    exit('Prozess läuft bereits...');
} else {
    // do something...
    $al->freeAccessLocker();
}
*/

class AccessLocker
{
    var $fh;
    var $suffix = 'lock';
    var $path;

    function AccessLocker($path = '')
    {
        if (empty($path)) {
            $this->path = '';
        } else {
            $path = preg_replace("~/$~", "", trim($path));
            $this->path = trim($path) . '/';
        }
    }

    function setAccessLocker($filename)
    {
        if (strstr($filename, '.')) {
            $reverseName = strrev($filename);
            $reverseName = substr($reverseName, strpos($reverseName, '.') + 1, strlen($reverseName));
            $filename    = strrev($reverseName) . '.' . str_replace(".", "", $this->suffix);
        } else {
            $filename = $filename . '.' . str_replace(".", "", $this->suffix);
        }
        $this->fh = fopen($this->path . $filename, 'w');
        if (!flock($this->fh, LOCK_EX + LOCK_NB)) {
            return false;
        } else {
            return true;
        }
    }

    function freeAccessLocker()
    {
        flock($this->fh, LOCK_UN);
        fclose($this->fh);
    }
}

?>