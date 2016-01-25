<?php 

class kernelMBot {
    
    var $_configs;
    var $_errors;
    var $_lastupdate;
    public $commands;
    public $localDebugBlock;
    
    public function __construct() {
        
        $this->_errors = array('count' => 0, 'errors' => array());
        $this->_configs = array();
        $this->localDebugBlock = true;
        $this->_lastupdate = 0;
        
        define("KERNEL_VERSION",                    '3.2');
        define("KERNEL_BUILD",                      '1017');
        define("KERNEL_AUTHOR",                     'Adams');
        define("KERNEL_TEAMSPEAK_MIN_VER",          "3.0.0-rc1");
        define("KERNEL_TEAMSPEAK_MIN_BUILD",        '14468');
        define("KERNEL_FULL_DEBUG",                 true);
        define("ERROR_TYPE_FATAL",                  1);
        define("ERROR_TYPE_CONNECT",                2);
        define("ERROR_TYPE_FAIL",                   3);
        define("ERROR_TYPE_CONFIG",                 4);
        define("ERROR_NORMAL",                      5);
        define("KERNEL_STARTUP",                    time());
		
        $this->openNewLogFile();

        return true;
    }
        
    public function getProcessList() {
		$file = @file_get_contents("/tmp/." . md5('bothelper'));
		return array(
			'lines' => explode("\n",$file)
		);
	}

	public function checkLocale($server_uid) {
	}
    
    public function licenseExit($val = '') {
        print "\n\n!!! License server error [$val], exiting. !!!\n\n\n";
        exit();
    }
    
    //TODO:
    public function updateUptime() {
        global $botid;
    }
    
    public function checkLicense() {
		return true;
    }
    
	public function checkProcess() {
		
		list($lines) = array_values($this->getProcessList());
		$process_count = 0;
		$rewrite = '';

		if ($lines != '') {
			foreach ($lines as $line) {
				if ($line != '') {
					$line = base64_decode($line);
					$data = explode(':', $line);
					if (file_exists("/proc/{$data[0]}")) {
						$rewrite .= base64_encode($line) . "\n";
						$process_count++;
					}
				}
			}
		}
		
		if ($rewrite != '')
			file_put_contents('/tmp/.' . md5('bothelper'),$rewrite);

	}

	public function createProcess($botid) {
		$this->checkProcess();
		list($license, $lines) = array_values($this->getProcessList());
		
		$towrite = base64_encode(getmypid() . ":" . $botid);
		
		if ($lines != '') {
			foreach ($lines as $line) {
				if ($line != '') {
					$line = base64_decode($line);
					$data = explode(':', $line);
					if (file_exists("/proc/{$data[0]}")) {
						if ($data[1] == $botid) {
						}
					}
				}
			}
		}
		
		$file = fopen("/tmp/." . md5('bothelper'),"a");
		fwrite($file, $towrite . "\n");
		fclose($file);
		
	}

    public function convertSeconds($seconds) {
        $output = array();
        $output['days'] = floor($seconds / 86400);
        $output['hours'] = floor(($seconds - ($output['days'] * 86400)) / 3600);
        $output['minutes'] = floor(($seconds - (($output['days'] * 86400)+($output['hours'] * 3600))) / 60);
        $output['seconds'] = floor(($seconds - (($output['days'] * 86400) + ($output['hours'] * 3600) + ($output['minutes'] *60))));
        return $output;
    }
    
    public function convertSecondsToStr($seconds) {
        $output = convertSeconds($seconds);
        return $output['days'].'d '.$output['hours'].'h '.$output['minutes'].'m '.$output['seconds'].'s';
    }
    
    public function getKernelUptime() {
        return $this->convertSecondsToStr(KERNEL_STARTUP);
    }
    
    public function getErrorStr($error_type) {
        global $lang;
        switch ($error_type) {
            case ERROR_TYPE_FATAL:
                return $lang->getLanguage('ERROR_TYPE_FATAL');
            break;
            case ERROR_TYPE_CONNECT:
                return $lang->getLanguage('ERROR_TYPE_CONNECT');
            break;
            case ERROR_TYPE_FAIL:
                return $lang->getLanguage('ERROR_TYPE_FAIL');
            break;
            case ERROR_TYPE_CONFIG:
                return $lang->getLanguage('ERROR_TYPE_CONFIG');
            break;
            case ERROR_NORMAL:
                return "";
            break;
        }
    }
    
    public function printError($error_str,$error_type = 5,$die = false,$error_function = '', $error_class = '', $error_line = '') {
        global $lang;
        $output = '';
        
        if (empty($error_function) || empty($error_line)) {
            $debug = debug_backtrace();
            $error_function = $debug[0]['function'];
            $error_line = $debug[0]['line'];
            if (!empty($debug[0]['file']))
                $output .= $lang->getConsoleLanguage('KERNEL_ERROR').'['.$debug[0]['file'].']';   
        }
        
        $output .= $lang->getConsoleLanguage('KERNEL').$this->getErrorStr($error_type).': "'.$error_str.'"';
        if ($error_function && !$error_class)
            $output .= ' : '.$lang->getConsoleLanguage('IN_FUNCTION').' '.$error_function;
        elseif ($error_function && $error_class)
            $output .= ' : '.$lang->getConsoleLanguage('IN_FUNCTION').' '.$error_class.'::'.$error_function;
        elseif ($error_class && !$error_function)
            $output .= ' : '.$lang->getConsoleLanguage('IN_CLASS').' '.$error_class;
        if ($error_line && is_numeric($error_line))
            $output .= ' : '.$lang->getConsoleLanguage('ON_LINE').' '.$error_line;
        if ($error_class || $error_function || $error_line)
            $output .= ' :';
        echo $output."\n";
        $this->_errors['count']++;
        $this->_errors['errors'][] .= $output;
        if (KERNEL_FULL_DEBUG == true && isset($debug) && $this->localDebugBlock != false) {
            $this->makeFullDebug($debug);
        }
        if ($die) {
            exit();
        }
        return true;
    }
    
    public function createLine($sign,$len) {
        $output = '';
        for ($i=0;$i<$len;$i++) {
            $output .= $sign;
        }
        return $output;
    }
    
      
    public function writeLog($text) {
        if (is_dir('inc/logs/'.getConfigValue('connection','bot_name')) == false) {
        }
    }
     public function makeFullDebug($debug_backtrace) {
        $output = "*".$this->createLine('-',68)."*\n";
        $output .= "* Debug time: ".date('j/F/Y G:i:s')."\n* Start of bot Debug\n* Bot Name: ".getConfigValue('connection','bot_name')." @ ".getConfigValue('connection','server_ip').":".getConfigValue('connection','server_query_port')." Server#: ".getConfigValue('connection','server_id')."\n";
        $lines = explode("\n",print_r($debug_backtrace,true));
        foreach ($lines as $line) {
            $output .= "* ".(string) $line."\n";
        }
        $output .= "* End of bot Debug\n*".$this->createLine('-',68)."*";
        
        return true;
    }
    public function writeLogToFile($fileDir,$text,$enterDate = true) {
        if (!file_exists($fileDir))
            $chmod = true;
        $fhandle = fopen($fileDir,'a');
        chmod($fileDir,0777);
        if ($enterDate == true)
            fwrite($fhandle,'['.date('j/F/Y G:i:s').'] '.$text."\r\n");
        else
            fwrite($fhandle,$text."\r\n");
        return fclose($fhandle);   
    }
    
    public function openNewLogFile() {
        if (is_dir('inc/logs/'.getConfigValue('connection','bot_name')) == false) {
        }
        if (!file_exists($fileDir)) {
;
        } 
        return true;
    }
    
    public function isTimeForEvent($eventName,$eventTime) {
        global $cache, $lang;
        if (!is_array($eventTime)) {
            $this->printError($lang->getLanguage('NOT_VALID_EVENT_TIME'),5,true,'isTimeForEvent','kernelMBot');
            return false;   
        }   
        $exec = array('seconds' => 0, 'minutes' => 0, 'hours' => 0, 'days' => 0, 'time' => 0);
        $cachex = $cache->getCacheValue($eventName.'_ev');
        if ($eventTime['seconds'] != false)
            $exec['seconds'] = $eventTime['seconds'];
        if ($eventTime['minutes'] != false)
            $exec['minutes'] = $eventTime['minutes'];
        if ($eventTime['hours'] != false)
            $exec['hours'] = $eventTime['hours'];
        if ($eventTime['days'] != false)
            $exec['days'] = $eventTime['days']; 
        $exec['time'] = time() + $exec['seconds'] + ($exec['minutes'] * 60) + ($exec['hours'] * 60 * 60) + ($exec['days'] * 24 * 60 * 60);
        if ($cachex['executed'] == 0) {
            $cache->setCache($eventName.'_ev',array('executed' => $exec['time']));
            return false;
        }
        if (time() >= $cachex['executed']) {
            $cache->setCache($eventName.'_ev',array('executed' => $exec['time']));
            return true;
        } else
            return false;
    }
    
    public function checkCommandGroups($clientInfo,$command) {
        global $config, $botid;
        if (array_key_exists('groups',$config[$botid]['commands']['commands_configs'][$command]) == true) {
            $clientGroups = explode(',',$clientInfo['client_servergroups']);
            foreach ($clientGroups as $clientGroup) {
                if (in_array($clientGroup,$config[$botid]['commands']['commands_configs'][$command]['groups'])) {
                    return true;
                }
            }
        }
        return false;
    }
}

?>