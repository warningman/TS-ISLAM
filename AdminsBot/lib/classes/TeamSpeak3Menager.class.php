<?php     



class TeamSpeak3Menager {
    
    public $system = array('handle' => '', 'status' => '');
    public $info = array('server_ip' => '', 'server_query_port' => '', 'server_id' => '', 'server_query_login' => '', 'server_query_password' => '', 'commands_mode' => true);
    public $kernel;
    
    public function TeamSpeak3Menager($server_ip,$server_query_port,$server_id,$server_query_login = '',$server_query_password = '',$commands_mode = true) {
        global $kernel;
        $this->kernel = $kernel;
        
        if (!preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",$server_ip))
            return $this->createOutput("This is not valid server ip addres.","",false);
        if (!is_numeric($server_query_port) || strlen($server_query_port) > 5)
            return $this->createOutput("This is not valid server query port.","",false);
        if (!is_numeric($server_id))
            return $this->createOutput("Not valid server id.","",false);
        if ($this->getStatus() != '')
            return $this->createOutput("You cant duble start 2 connections in one script.","",false);
		
        $this->info['server_ip'] = $server_ip;
        $this->info['server_query_port'] = $server_query_port;
        $this->info['server_id'] = $server_id;
        $this->info['server_query_login'] = $server_query_login;
        $this->info['server_query_password'] = $server_query_password;
        $this->info['commands_mode'] = $commands_mode;
        $this->system['status'] = "configured";
        
        if ($this->getStatus() == "configured") {
            return $this->createOutput("","",true); 
        } else {
            return $this->createOutput("Failed to configure.","",false);   
        }
    }
    
    public function __destruct() {
        $this->quit();    
    }
    
    public function connect() {
        if ($this->getStatus() != "configured") {
            return $this->createOutput("First configure TeamSpeak3Menager.","",false);
        }
        
        if ($this->system['handle'] = @fsockopen($this->info['server_ip'], $this->info['server_query_port'], $errnum, $errstr, 10)) {
            $this->system['status'] = "connected";
            $this->selectServer($this->info['server_id']);
            if ($this->info['server_query_login'] != '' && $this->info['server_query_password'] != '')
                 $this->serverQueryLogin($this->info['server_query_login'],$this->info['server_query_password']);
            if ($this->info['commands_mode'])
                $this->commandData('servernotifyregister event=textprivate');
            return $this->createOutput("","",true);
        } else {
            return $this->createOutput("Can't connect to the TeamSpeak3 server.","",false);
        }
    }
    
    public function isConnected() {
        if ($this->getStatus() == "connected")
            return $this->createOutput("","",true);
        else
            return $this->createOutput("","",false);
    }
    
	public function sendCommand($command) {
	    global $kernel;
		if ($this->getElement($this->isConnected(),'bool') == false)
			return $this->createOutput("First connect to the server.","",false);
			
		if (fputs($this->getHandle(), $command."\n") == false) { die("TeamSpeak3 server is down.\n"); }
		$read = '';
		
		do {
			$read .= fread($this->getHandle(), 50 * 1024);
			
			if(strpos($read, 'error id=3329 msg=connection') !== false) {
				echo "\n[TeamSpeak3:Menager][FloodBan]: I'm wait 30 seconds to end of floodban, and try send command again.\n";
                sleep(30);
                $this->sendCommand($command);
			}
			
		} while(strpos($read, 'msg=') === false || strpos($read, 'error id=') === false);

		if (strpos($read, 'error id=0 msg=ok') === false) {
            $info = $this->getElement($this->createDataOutput($read,'array'),'data');
            $kernel->writeLog("[QUERY_ERROR] ".$info['msg']);
            return $this->createOutput("Error: ".$info['msg'],'',false);
        }

		return $this->createOutput("",$read,true);
	}
    
	public function createDataOutput($readData,$mode = 'bool') {
		$correctModes = array('plain', 'text', 'plaintext', 'multi', 'array', 'table', 'bool','boolean');
		if (!in_array($mode,$correctModes))
			return $this->createOutput("Wrong data mode value.",'',false);	

        $readData = str_replace(array('error id=0 msg=ok', chr('01')), '', $readData);

		if ($mode == 'plain' || $mode == 'text' || $mode == 'plintext') {
			return $this->createOutput("",$returnData,true);
		} elseif ($mode == 'array' || $mode == 'table') {
			$dataExplodes = explode(' ', $readData);
			$returnValue = array();
			foreach ($dataExplodes as $dataExplode) {
				$dataExplode = explode('=', $dataExplode);
                if (count($dataExplode) > 2) {
                    for ($i=2;$i<count($dataExplode);$i++) { 
                        $dataExplode[1] .= '='.$dataExplode[$i];
                    }
                    $returnValue[$this->unEscapeText((string) $dataExplode[0])] = $this->unEscapeText((string) $dataExplode[1]);
                } else {
    				if (count($dataExplode) == 1) {
    					$returnValue[$this->unEscapeText((string) $dataExplode[0])] = '';
    				} else {
    					$returnValue[$this->unEscapeText((string) $dataExplode[0])] = (string) $this->unEscapeText((string) $dataExplode[1]);
                    }
                }
			}
            return $this->createOutput("",$returnValue,true);
		} elseif ($mode == 'multi') {
			$dataExplodes = explode('|', $readData);
			$returnValue = array();
			foreach ($dataExplodes as $dataExploder) {
				$dataExploder = explode(' ', $dataExploder);
				$tmp = array();
				foreach ($dataExploder as $dataExplode) {
					$dataExplode = explode('=', $dataExplode);
                    if(count($dataExplode) > 2) {
                        for ($i=2;$i<count($dataExplode);$i++) {
                            $dataExplode[1] .= '='.(string) $dataExplode[$i];
                        }
                        $tmp[$this->unEscapeText((string) $dataExplode[0])] = $this->unEscapeText((string) $dataExplode[1]);
                    } else {
    					if (count($dataExplode) == 1) {
    						$tmp[$this->unEscapeText((string) $dataExplode[0])] = '';
    					} else {
    						$tmp[$this->unEscapeText((string) $dataExplode[0])] = $this->unEscapeText((string) $dataExplode[1]);
                        }
                    }
				}
                $returnValue[] = $tmp;
			}
            return $this->createOutput("",(array) $returnValue,true);
		} elseif ($mode == 'bool' || $mode == 'boolean') {
		    if (empty($readData))
                return $this->createOutput("",false,false);
            else
                return $this->createOutput("",true,true);
        }
	}
    
    public function commandData($command,$dataType = 'bool') {
        $abliveTypes = array('plain', 'text', 'plaintext', 'multi', 'array', 'table','bool','boolean');
        if (!in_array($dataType,$abliveTypes)) {
            return $this->createOutput("Wrong dataType.","",false);
        }
        return $this->createOutput('',$this->getElement($this->createDataOutput($this->getElement($this->sendCommand($command),'data'),$dataType),'data'),true);    
    }
    
    public function getElement($array,$element) {
        if (is_array($array)) {
            return $array[$element];
        } elseif (is_array($element)) {
            return $element[$array];
        } else {
            return $this->createOutput("Wrong function parametrs.","",false);
        }
    }    
	
    public function createOutput($error,$data,$bool) {
        #if ($error != "") {
		#	if ($error == "Error: server is shutting down") { exit(); }
        #    $this->kernel->printError($error);
        #}
        
        return array('error' => $error, 'data' => $data, 'bool' => $bool);
    }
    
    function getHandle() {
        return $this->system['handle'];
    }
    
    function getStatus() {
        return $this->system['status'];
    }
    
	public function escapeText($text) {
	   return str_replace(array("\t","\v","\r","\n","\f",' ','|','/'),array('\t','\v','\r','\n','\f','\s','\p','\/'),$text);
    }
    
	public function unEscapeText($text) {
	   return (string) str_replace(array('\t','\v','\r','\n','\f','\s','\p','\/'),array("\t","\v","\r","\n","\f",' ','|','/'),$text);
    }
    
    public function convertSeconds($seconds) {
        $output = array();
        $output['days'] = floor($seconds / 86400);
        $output['hours'] = floor(($seconds - ($output['days'] * 86400)) / 3600);
        $output['minutes'] = floor(($seconds - (($output['days'] * 86400)+($output['hours'] * 3600))) / 60);
        $output['seconds'] = floor(($seconds - (($output['days'] * 86400) + ($output['hours'] * 3600) + ($output['minutes'] *60))));
        return $this->createOutput('',$output,true);
    }
    public function checkSelected() {
		$backtrace = debug_backtrace();
		$this->addDebugLog('you can\'t use this function if no server is selected', $backtrace[1]['function'], $backtrace[0]['line']);
		return $this->generateOutput(false, array('you can\'t use this function if no server is selected'), false);
	}
    public function convertSecondsToStr($seconds) {
        $output = $this->getElement($this->convertSeconds($seconds),'data');
        return $output['days'].'d '.$output['hours'].'h '.$output['minutes'].'m '.$output['seconds'].'s';
    }
    
    /* Single Functions */
    
    public function selectServer($server_id) {
        return $this->commandData("use sid=".$server_id,'bool');    
    }
    
    public function setName($new_name) {
        return $this->commandData("clientupdate client_nickname=".$this->escapeText($new_name));
    }
    
    public function setDesc($new_desc) { 
        return $this->commandData("clientupdate client_description=".$this->escapeText($new_desc));
    }
    
    public function serverQueryLogin($query_user,$query_password) {
        return $this->commandData("login ".$this->escapeText($query_user)." ".$this->escapeText($query_password));
    }
    
    public function globalMessage($msg) {
        return $this->commandData('gm msg='.$this->escapeText($msg));
    }
    
    public function getHostInfo() {
        return $this->commandData('hostinfo','array');   
    }
    
    public function getInstanceInfo() {
        return $this->commandData('instanceinfo','array');
    }
    
    public function getPermissionsList() {
        return $this->commandData('permissionlist','multi');
    }
    
    public function quit() {
        return $this->commandData('quit');
    }
    
    public function sendMessage($mode,$target,$message) {
        return $this->commandData('sendtextmessage targetmode='.$mode.' target='.$target.' msg='.$this->escapeText($message));
    }
    
    public function getServerGroupList() {
        return $this->commandData('servergrouplist','multi');
    }
    
    public function getServerInfo() {
        return $this->commandData('serverinfo','array');
    }
    
    public function getVersion() {
        return $this->commandData('version','array');
    }
    
    public function whoAmI() {
        return $this->commandData('whoami','array');
    }
    
    public function help($topic = '') {
        if ($topic != '') $topic = ' '.$topic;
        return $this->commandData('help'.$topic);
    }
    
    public function addClientServerGroup($cldbid,$group) {
        return $this->commandData('servergroupaddclient sgid='.$group.' cldbid='.$cldbid);
    }
    
    public function delClientServerGroup($cldbid,$group) {
        return $this->commandData('servergroupdelclient sgid='.$group.' cldbid='.$cldbid);
    }  
	
    public function addIpBan($ip, $time, $reason = '') {
        if ($reason != '') $reason = ' banreason='.$this->escapeText($reason);
        return $this->commandData('banadd ip='.$ip.' time='.$time.$reason);
    }
    
    public function addNameBan($client_name, $time, $reason = '') {
        if ($reason != '') $reason = ' banreason='.$this->escapeText($reason);
        return $this->commandData('banadd name='.$this->escapeText($client_name).' time='.$time.$reason);
    }
    
    public function addUidBan($client_uid,$time,$reason = '') {
        if ($reason != '') $reason = ' banreason='.$this->escapeText($reason);
        return $this->commandData('banadd uid='.$client_uid.' time='.$time.$reason);
    }
    
    public function removeBan($ban_id) {
        return $this->commandData('bandel banid='.$banID);    
    }
    
    public function removeAllBans() {
        return $this->commandData('bandelall');   
    }
    
    public function getBanList($params = '') {
        if ($params != '') $params = ' '.$params;
        return $this->commandData('banlist'.$params,'multi');
    }
    
    
    
    public function getChannelPermList($channel_id,$perm_ids = false) {
        if ($perm_ids) $perm_ids = ' -permids'; else $perm_ids = '';
        return $this->commandData('channelpermlist cid='.$channel_id.$prem_ids,'multi');
    }
    
    public function getChannelList($params = '') {
        if ($params != '') $params = ' '.$params;
        return $this->commandData('channellist'.$params,'multi');
    }
    
    public function moveChannel($channel_id,$channel_pid,$order_by = '') {
        if ($order_by != '') $order_by = ' order='.$order_by;
        return $this->commandData('channelmove cid='.$channel_id.' cpid='.$channel_pid.$order_by);
    }
    
    public function getChannelInfo($channel_id) {
        return $this->commandData('channelinfo cid='.$channel_id,'array');
    }
    
    public function findChannel($pattern) {
        return $this->commandData('channelfind pattern='.$this->escapeText($pattern),'multi');
    }
    
    public function editChannel($channel_id, $datas) {
        $sharedDataStr = '';
        foreach ($datas as $key => $value) {
            $sharedDataStr .= ' '.$key.'='.$this->escapeText($value);   
        }
        return $this->commandData('channeledit cid='.$channel_id.$sharedDataStr);
    }
    
    public function deleteChannel($channel_id, $force_delete = 1) {
        return $this->commandData('channeldelete cid='.$channel_id.' force='.$force_delete);
    }
    
    public function removeChannelPerm($channel_id, $perms_ids) {
        $permsArray = array();
        if (count($perms_ids) > 0) {
            foreach($perms_id as $values) {
                $permsArray[] = 'permid='.$value;
            }
            return $this->commandData('channeldelperm cid='.$channel_id.' '.implode('|',$permsArray));
        } else {
            return $this->createOutput("Permissions ids not found.",'',false);   
        }
    }
    
    public function createChannel($datas) {
        $datasStr = '';
        foreach ($datas as $keyez => $valueez) {
            $datasStr .= ' '.$keyez.'='.$this->escapeText($valueez);
        }
        return $this->commandData('channelcreate '.$datasStr,'array');
    }
    
    public function getChannelGroups() {
        return $this->commandData('channelgrouplist','multi');
    }
    
    public function getChannelGroupUsers($channel_id) {
        return $this->commandData('channelgroupclientlist cid='.$cid,'multi');
    }
	
    public function setClientChannelGroup($channel_id,$channel_group_id,$client_db_id) {
        return $this->commandData('setclientchannelgroup cgid='.$channel_group_id.' cid='.$channel_id.' cldbid='.$client_db_id,'bool');   
    }   
    public function channelGroupClientList($cid = NULL, $cldbid = NULL, $cgid = NULL) {
		return $this->commandData('channelgroupclientlist'.(!empty($cid) ? ' cid='.$cid : '').(!empty($cldbid) ? ' cldbid='.$cldbid : '').(!empty($cgid) ? ' cgid='.$cgid : ''),'multi');
	}
    public function clientDeleteFromDb($client_db_id) {
        return $this->commandData('clientdbdelete cldbid='.$client_db_id);
    }
    
    public function clientEditInDb($client_db_id, $data) {
        $datasStr = '';
        foreach ($data as $key => $value) {
            $datasStr .= ' '.$key.'='.$this->escapeText($value);
        }
        return $this->commandData('clientdbedit cldbid='.$client_db_id.$datasStr);
    }
    public function serverEdit($data) {
		$settingsString = '';
		
		foreach($data as $key => $value) {
			$settingsString .= ' '.$key.'='.$this->escapeText($value);
		}
		return $this->commandData('serveredit'.$settingsString,'boolean');
	}
    public function clientFindInDb($pattern, $uid = false) {

        if ($uid != false) $uid = ' -uid';
        return $this->commandData('clientdbfind pattern='.$this->escapeText($pattern).$uid,'multi');
    }
    
    public function clientInfoFromDb($client_db_id) {
        return $this->commandData('clientdbinfo cldbid='.$client_db_id, 'array');
    }
	
    public function editClient($client_id, $data) {
        $datas = '';
        foreach ($data as $key => $value) {
            $datas .= ' '.$key.'='.$this->escapeText($value);
        }
        return $this->commandData('clientedit clid='.$client_id.$datas);
    }
    
    public function findClient($pattern) {
        return $this->commandData('clientfind pattern='.$this->escapeText($pattern),'multi');
    }
    
    public function getClientDbIdFromUid($client_uid) {
        return $this->commandData('clientgetdbidfromuid cluid='.$client_uid,'array');
    }
    
    public function getClientAllIds($client_uid) {
        return $this->commandData('clientgetids cluid='.$client_uid,'multi');
    }
    
    public function getClientNameByDbid($client_db_id) {
        return $this->commandData('clientgetnamefromdbid cldbid='.$client_db_id,'array');
    }
    
    public function getClientNameByUid($client_uid) {
        return $this->commandData('clientgetnamefromuid cluid='.$client_uid,'array');
    }
    
    public function getClientInfo($client_id) {
        return $this->commandData('clientinfo clid='.$client_id,'array');
    }
    
    public function kickClient($client_id,$kickmsg = '',$from = 'server') {
        if ($from == 'server') { $from = 5; } elseif ($from == 'channel') { $from = 4; } else { $from = 5; }
        if ($kickmsg != '') $kickmsg = ' reasonmsg='.$this->escapeText($kickmsg);
        return $this->commandData('clientkick clid='.$client_id.' reasonid='.$from.$kickmsg);
    }
    
    public function getClientList($params = '') {
        if ($params != '') $params = ' '.$params;
        return $this->commandData('clientlist'.$params,'multi');
    }
    
    public function moveClient($client_id,$channel_id) {
        return $this->commandData('clientmove clid='.$client_id.' cid='.$channel_id);
    }
    
    public function pokeClient($client_id, $message) {
        return $this->commandData('clientpoke clid='.$client_id.' msg='.$this->escapeText($message));
    }
    
    public function getClientPermList($client_db_id, $perms_id = false) {
        if ($perms_id != false) $perms_id = ' -permsid'; else $perms_id = '';
        return $this->commandData('clientpermlist clidbid='.$client_db_id.$perms_id,'multi');
    }
    
    public function getServerGroupClients($gid) {
        return $this->commandData('servergroupclientlist sgid='.$gid,'multi');
    }
    
   
    
    public function getOnlineClientIds() {
        $clientlist = $this->getElement($this->getClientList(),'data');
        $output = array();
        foreach ($clientlist as $client) {
            if (is_numeric(@$client['clid']) == true) {
                $output[] = $client['clid'];
            }
        }
        return $this->createOutput('',$output,true);
    }
    
    public function isClientOnline($clid) {
        if (in_array($clid,$this->getElement($this->getOnlineClientIds(),'data')) == true) {
            return $this->createOutput('',true,true);
        } else {
            return $this->createOutput('',false,false);
        }
    }
    
}
?>