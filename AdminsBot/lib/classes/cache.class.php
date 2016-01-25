<?php

/**
 * @author Adamski Łukasz (admin@nolifers.pl)
 * @license LICENCE_PL.txt
 */

class CacheMenager {
    
    private static $cache = array();
    
    function __construct() {
       return true; 
    }  
    
    public function isInCache($cache_name) {
        if (array_key_exists($cache_name,self::$cache))
            return true;
        else
            return false;
    }
    
    public function setCache($cache_name,$cache_value) {
        self::$cache[$cache_name] = $cache_value;
        return true;
    }
    
    public function getCacheValue($cache_name) {
        if (array_key_exists($cache_name,self::$cache))
            return self::$cache[$cache_name];
        else
            return false;
    }
    
    public function addNewValue($cache_name,$cache_new_value) {
        $caches = $this->getCacheValue($cache_name);
        if (is_array($caches)) {
            self::$cache[$cache_name][] = $cache_new_value;
            return true;
        } else
            return false;
    }
    
    public function addNewValueWitchName($cache_name,$cache_new_name,$cache_new_value) {
        $caches = $this->getCacheValue($cache_name);
        if (is_array($caches)) {
            self::$cache[$cache_name][$cache_new_name] = $cache_new_value;
            return true;
        } else
            return false;
    }
    
    public function deleteCache($cache_name) {
        self::$cache[$cache_name] = NULL;
        unset(self::$cache[$cache_name]);
        return true;
    }
    
    public function deleteArrayCacheValue($cache_name,$key) {
        self::$cache[$cache_name][$key] = NULL;
        unset(self::$cache[$cache_name][$key]);
        return true;
    }
    
    public function clearValues($cache_name,$new_value = array()) {
        return $this->setCache($cache_name,$new_value);
    }
    
    public function getFullCache() {
        return self::$cache;   
    }
}
?>