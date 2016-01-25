<?php

/**
 * @author Adamski Łukasz (admin@nolifers.pl)
 * @license LICENCE_PL.txt
 */

class filecache {
    
    private static $file;
    private static $fullDir;
    private static $handle;
    private static $dir = 'cache';
    
    public function __construct($cacheFileName) {
        if (is_dir(self::$dir) == false) {
            mkdir(self::$dir);
        }
        $newCache = false;
        if (file_exists(self::$dir.'/'.$cacheFileName.'.cache') == false) {
            $newCache = true;
        }
        $handle = fopen(self::$dir.'/'.$cacheFileName.'.cache','a');
        if (is_writable(self::$dir.'/'.$cacheFileName.'.cache') == false) {
            chmod(self::$dir.'/'.$cacheFileName.'.cache',0700);
        }
        self::$handle = $handle;
        self::$file = $cacheFileName.'.cache';
        self::$fullDir = self::$dir.'/'.self::$file;
        if ($newCache == true) {
            self::makeNewCacheFile(self::$fullDir);
        }
        fclose(self::$handle);
        return true;
    }
    
    public function getCacheValue() {
        return unserialize(file_get_contents(self::$fullDir));
    }
    
    public function delCache() {
        return unlink(self::$fullDir);
    }
    
    public function makeNewCacheFile() {
        $value = array('info' => array('created' => time()), 'value' => array());
        return file_put_contents(self::$fullDir,serialize($value));
    }
    
    public function setCacheVarible($key,$value = false) {
        $valuex = self::getCacheValue();
        if ($value != false) {
            $valuex['value'][$key] = $value;
        } else {
            $valuex['value'][] = $key;
        }
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    # { Defined 'notopicdate' : 
    # DataScaner Mod by Adams | Contact: admin@nolifers.pl
    
    public function mod_changeHourValue($cid,$hourID,$newVal) {
        $valuex = self::getCacheValue();
        $valuex['value'][$cid]['hourly_online'][$hourID] = $newVal;
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    public function mod_changeDayValue($cid,$dayID,$newVal) {
        $valuex = self::getCacheValue();
        $valuex['value'][$cid]['monthly_online'][$dayID] = $newVal;
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    public function mod_updateScanned($cid,$scanned) {
        $valuex = self::getCacheValue();
        $valuex['value'][$cid]['scanned'] = $scanned;
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    public function mod_updateTotalAndScannDays($cid,$totalClients) {
        $valuex = self::getCacheValue();
        $valuex['value'][$cid]['channel_days'] = $valuex['value'][$cid]['channel_days'] + 1;
        $valuex['value'][$cid]['total'] = $valuex['value'][$cid]['total'] + $totalClients;
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    public function mod_clearHours($cid) {
        $valuex = self::getCacheValue();
        $valuex['value'][$cid]['hourly_online'] = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0);
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    public function mod_clearDays($cid) {
        $valuex = self::getCacheValue();
        $valuex['value'][$cid]['monthly_online'] = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    # } Defined 'notopicdate' ;
    
    public function getCacheVarible($key) {
        $valuex = self::getCacheValue();
        if (array_key_exists($key,$valuex['value']) == true) {
            return $valuex['value'][$key];
        } else {
            return false;
        }
    }
    
    public function delCacheVarible($key) {
        $valuex = self::getCacheValue();
        if (array_key_exists($key,$valuex['value']) == true) {
            unset($valuex['value'][$key]);
            return file_put_contents(self::$fullDir,serialize($valuex));
        } else {
            return false;
        }
    }
    
    public function setCacheInfo($key,$value = false) {
        $valuex = self::getCacheValue();
        if ($value != false) {
            $valuex['info'][$key] = $value;
        } else {
            $valuex['info'][] = $key;
        }
        return file_put_contents(self::$fullDir,serialize($valuex));
    }
    
    public function getCacheInfo($key) {
        $valuex = self::getCacheValue();
        if (array_key_exists($key,$valuex['info']) == true) {
            return $valuex['info'][$key];
        } else {
            return false;
        }
    }
    
    public function delCacheInfo($key) {
        $valuex = self::getCacheValue();
        if (array_key_exists($key,$valuex['info']) == true) {
            unset($valuex['info'][$key]);
            return file_put_contents(self::$fullDir,serialize($valuex));
        } else {
            return false;
        }
    }
    
    public function getCacheCreateData() {
        $valuex = self::getCacheValue();
        return $valuex['info']['created'];
    }
    
}

?>