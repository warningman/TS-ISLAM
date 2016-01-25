<?php

/**
 * @author Adamski Łukasz (admin@nolifers.pl)
 * @licence LICENCE_PL.txt
 */

class languageMenager {
    
    private $__langPack;
    private $replaces = array('ó' => 'o', 'ż' => 'z', 'ź' => 'z', 'ą' => 'a', 'ę' => 'e', 'ł' => 'l', 'ć' => 'c', 'ś' => 's', 'ń' => 'n', 'Ó' => 'O', 'Ż' => 'Z', 'Ź' => 'z', 'Ą' => 'A', 'Ę' => 'E', 'Ł' => 'L', 'Ć' => 'C', 'Ś' => 'S', 'Ń' => 'N');
    
    public function __construct() {
        $this->__langPack = array();
        return true;   
    }
    
    public function __destruct() {
        return true;   
    }
    
    public function loadLanguagePack($folderName) {
        $folder = 'inc/languages/'.$folderName.'/';
        $language = array();
        if (count(@scandir($folder)) > 2) {
            foreach (scandir($folder) as $file) {
                if (preg_match('/[A-Za-z0-9_-]\.lang\.php/',$file)) {
                    include($folder.$file);
                    foreach ($language as $key => $value) {
                        $this->__langPack[$key] = $value;
                    }
                }
            }
        } else {
            return false;
        }
    }
    
    public function getLanguage($langName) {
        return @str_replace('È','ó',@$this->__langPack[$langName]);
    }
    
    public function getConsoleLanguage($langName) {
        return @str_replace(array_keys($this->replaces),array_values($this->replaces),@$this->__langPack[$langName]);
    }
    
    public function langReplace($toReplace,$replaces,$langName) {
        return str_replace($toReplace,$replaces,$this->getLanguage($langName));
    }
    
    public function langConsoleReplace($toReplace,$replaces,$langName) {
        return @str_replace(array_keys($this->replaces),array_values($this->replaces),str_replace($toReplace,$replaces,$this->getLanguage($langName)));
    }
    
    public function signsFilter($text) {
          $signsArray = array(-71 => 165,-13 => 162,-100 => 152,-77 => 136,-65 => 190,-97 => 171,-26 => 134,-15 => 228,-91 => 164,-54 => 168,-45 => 224,-117 => 151,-93 => 157,-81 => 189,-113 => 141,-58 => 143,-47 => 227);
          $newArray = array();
          foreach ($signsArray as $key => $value) {
                $newArray[chr($key)] = chr($value);
          }
          return str_replace(array_keys($newArray),array_values($newArray),$text);
    }

}
?>