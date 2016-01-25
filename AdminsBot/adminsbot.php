<?php
 /**
  * 
  *			Ts-Islam
  *
**/
    ini_set('default_charset', 'UTF-8');
    setlocale(LC_ALL, 'UTF-8');
    date_default_timezone_set('Asia/Riyadh');
    error_reporting(0);

    global $botid, $config, $doCommand, $cache, $whoami, $serverInfo;
    include('lib/classes/languageMenager.php');
    include('lib/classes/config_menager.php');
    include('lib/classes/Kernel.class.php');
    $kernel = new kernelMBot;
    $options = getopt('i:');

    $botid = $options['i'];
        include('config.php');

    include('lib/classes/TeamSpeak3Menager.class.php');
    $ts = new TeamSpeak3Menager(
                getConfigValue('connection','server_ip'),
                getConfigValue('connection','server_query_port'),
                getConfigValue('connection','server_id'),
                getConfigValue('connection','server_query_login'),
                getConfigValue('connection','server_query_password'),
                getConfigValue('connection','commands_mode')
            );

        if(getConfigValue('options','enable_database')  === true){
        include('lib/classes/database.class.php');
                try {
                $sql = new Database(
                        getConfigValue('db', 'mysql_ip'),
                        getConfigValue('db', 'mysql_user'),
                        getConfigValue('db', 'mysql_pass'),
                        getConfigValue('db', 'mysql_base')
                );
                } catch (PDOException $x) {
                        die($x->getMessage());
                }
        }
    include('lib/classes/cache.class.php');
    $cache = new CacheMenager;
    include('lib/classes/filecache.class.php');
    $kernel->createProcess($botid);
    $processes = array();
    $pid = pcntl_fork();
    if ($pid == 0) {
        while (1) {
            eval(base64_decode('JGtlcm5lbC0+dXBkYXRlVXB0aW1lKCk7'));
            sleep(32);
        }
    } elseif ($pid) {
        array_push($processes, $pid);
    }

    if ($ts->getElement($ts->connect(),'bool') == true) {

        $whoami = $ts->getElement($ts->whoAmI(),'data');

        if (is_numeric(getConfigValue('connection','move_to_channel'))) {
            $ts->moveClient($whoami['client_id'],getConfigValue('connection','move_to_channel'));
        }

        if (getConfigValue('connection','bot_name') != '') {
            $ts->setName(getConfigValue('connection','bot_name'));

        }

          if (getConfigValue('options','enable_commands_system') == true)

          $total = array('commands' => 0, 'events' => 0, 'plugins' => 0, 'accesories' => 0, 'plugins_loaded' => array(), 'events_loaded' => array(), 'commands_loaded' => array(), 'accesories_loaded' => array());

          if (true) {
                if (count(@scandir('lib/inc/')) > 2) {
                    foreach (scandir('lib/inc/') as $file) {
                        if (preg_match('/[A-Za-z0-9_-]\.php/',$file)) {
                            include('lib/inc/'.$file);
                            $total['events']++; $class = str_replace('.php','',$file);
                            $total['events_loaded'][] = $class;
                            if (method_exists($class,"onRegister") == true) {
                                $class::onRegister();
                            }
                            if (method_exists($class,"onClientAway") == true) {
                                $cache->setCache($class.'_onClientAway',array());
                            }
                            if (method_exists($class,"onClientAway") == false && method_exists($class,"onClientBreakAway") == true) {
                            }
                        }
                    }
                }
        }
        while (1) {
            $clientsData = $ts->getElement($ts->getClientList("-uid -away -times -voice -groups -info"),'data');
            $serverInfo = $ts->getElement($ts->getServerInfo(),'data');
			$channelsData = $ts->getElement($ts->getChannelList("-topic -limits"),'data');
            $kernel->checkLocale($serverInfo['virtualserver_unique_identifier']);
            if (true) {
                foreach ($total['events_loaded'] as $class) {
                    if ($kernel->isTimeForEvent($class,getEventTimeInfo($class)) == true) {
                        if (method_exists($class,"onThink") == true) {
                            $class::onThink();
                        }
                        foreach ($clientsData as $invokerid) {
                            if (method_exists($class,"onClient")) {
                                if (is_array($invokerid) == true && array_key_exists('clid',$invokerid) == true) {
                                    if (is_numeric($invokerid['clid']) == true) {
                                        if ($invokerid['clid'] != $whoami['client_id'] && $invokerid['client_version'] != "ServerQuery") {
                                            $class::onClient($invokerid);
                                        }
                                    }
                                }
                            }
                            if (method_exists($class,"onClientAreOnChannel") == true) {
                                if (array_key_exists('onClientAreOnChannel',$config[$botid]['events']['events_configs'][$class])) {
                                    if (is_array($config[$botid]['events']['events_configs'][$class]['onClientAreOnChannel']) == true) {
                                        if (in_array($invokerid['cid'],$config[$botid]['events']['events_configs'][$class]['onClientAreOnChannel']) == true){
                                            $class::onClientAreOnChannel($invokerid['clid'],$invokerid['cid'],$invokerid,$clientsData);
                                        }
                                    } else {
                                        if ($invokerid['cid'] == $config[$botid]['events']['events_configs'][$class]['onClientAreOnChannel']) {
                                            $class::onClientAreOnChannel($invokerid['clid'],$invokerid['cid'],$invokerid,$clientsData);
                                        }
                                    }
                                }
                            }
                        }
                        if (method_exists($class,"onCleanUp") == true) {
                            if (is_array($clientsData) == true) {
                                $class::onCleanUp($clientsData);
                            }
                        }
                    }
                }
            }
            clearstatcache();
            if (true)
                sleep(1);
                $kernel->checkProcess();
            if (defined('EXIT_CALL')) {
                $ts->quit();
                exit();
            }

            unset($findCommands,$doCommand,$command,$handleStatus);
        }
        $ts->quit();
        exit();

    } else {
    }

    foreach($processes as $pid) {
        pcntl_waitpid($pid, $status);
    }
 ?>