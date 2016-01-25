<?php

class adminsOnline {
    
    private static $eventName = 'adminsOnline';
    private static $config;
    private static $simpleConfig = array(
        'write_channel' => 0,
        'groups' => array()
    );
    
    private static function loadConfig() {
        global $lang;
        $cfg = getEventConfigValue(self::$eventName);
        if ($cfg != false) {
            self::$config = $cfg;
        } else {
            self::$config = self::$simpleConfig;
            echo ": > [".self::$eventName."]: ".$lang->getConsoleLanguage('SIMPLE_CONFIGURATION')."\n";
        }
        return true;
    }
    
    public static function onRegister()
	{
        self::loadConfig();
        return true;
    }
    
    private static function isClientInGroup($group,$clientGroups) {
        foreach ($clientGroups as $checkGroup) {
            if ($group == $checkGroup) {
                return $group;
            }            
        }
        return false;
    }	
	private static function format_seconds($seconds)
	{    
    
		$uptime = array();
		$uptime['days']=floor($seconds / 86400);
		$uptime['hours']=floor(($seconds - ($uptime['days'] * 86400)) / 3600);
		$uptime['minutes']=floor(($seconds - (($uptime['days'] * 86400)+($uptime['hours']*3600))) / 60);
		$uptime['seconds']=floor(($seconds - (($uptime['days'] * 86400)+($uptime['hours']*3600)+($uptime['minutes'] * 60))));
		
		$uptime_text = '';
		
		if ($uptime['days'] > 0) {
			$uptime_text .= $uptime['days'] . ' ' . ($uptime['days'] == 1 ? 'day ' : 'days ');
		}
		
		if ($uptime['hours'] > 0) {
			$uptime_text .= $uptime['hours'] . ' ' . ($uptime['hours'] == 1 ? 'hour ' : 'hours ');
		}
		
		if ($uptime['minutes'] > 0) {
			$uptime_text .= $uptime['minutes'] . ' ' . ($uptime['minutes'] == 1 ? 'minute' : 'minutes');
		}
		
		if ($uptime_text == '') {
			$uptime_text .= $uptime['seconds'] . ' seconds';
		}
		
		return $uptime_text;
	}
	
    public static function onThink()
	{
		global $lang, $ts, $whoami;		
        $desc = self::$config['up_description'];
      
        $i=1; 
        $admins = array();		
		$channel = $ts->getElement($ts->getChannelList(),'data');
        $servergroups = $ts->getElement($ts->getServerGroupList(),'data');		
		foreach ($ts->getElement($ts->getClientList('-groups -uid -away -voice -times'),'data') as $client) {
			$client_info = $ts->getElement($ts->getClientInfo($client['clid']),'data');
			$clientinfos[$client["clid"]] = $client_info["connection_connected_time"];
            if ($client['clid'] != $whoami['client_id']) {
                $clientGroups = explode(',',$client['client_servergroups']);
                foreach (self::$config['groups'] as $checkThisGroup) {
                    $group = self::isClientInGroup($checkThisGroup,$clientGroups);
                    if (is_numeric($group) == true && in_array($group,self::$config['groups']) == true) {
                        $admins[$client['client_nickname']] = array('group' => $group, 'cid' => $client['cid'], 'clid' => $client['clid'], 'unique_id' => $client['client_unique_identifier'], 'idle' => $client['client_idle_time'], 'last_connect'=> $client['client_lastconnected'], 'away' => $client['client_away'], 'mute' => $client['client_output_muted']);
							
					}
                }
            }
        }
		foreach($channel as $channels){
			$channelname[$channels['cid']] = $channels['channel_name'];
		}
        foreach ($servergroups as $group) {
			if (in_array($group['sgid'],self::$config['groups']) == true) {
				foreach ($admins as $nickname => $values) {						
					$status = '[color=green]Online[/color]';
					if ($values['away'] == 1 || $values['mute'] == 1) {
							$status = '[color=red]Away[/color]';
					}
						if ($values['group'] == $group['sgid']) {
							$desc .= '[size=8] [URL=client://' . $values['clid'] . '/' . $values['unique_id'] . ']' . $nickname . '[/URL][/size][size=8]\n      ╔➼ Rank : [color=red][b]'.$group['name'].'[/b][/color]\n      ╠➼  Status : [b]'.$status.'[/b]\n      ╠➼  Channel : [b][url=channelID://'.$values['cid'].']'.str_replace('[cspacer]', '', $channelname[$values['cid']]).'[/url][/b]\n      ╚➼ Online Since : [b]'.self::format_seconds(time() - $values['last_connect']).'[/b][/size]\n\n';							
							$i++;
						}

					}						
					
				}
		}
		$desc .= '\n[hr][right][size=5][u][i]'.self::$config['update'].' [color=purple]'.date('H:i', time()).'[/color].\n[/right]';
		
		$desc .= '[right][size=9]'.self::$config['info'].'[/size][/right]';
		
		$ts->editChannel(self::$config['write_channel'], array(
                'channel_name' => self::$config['channel_name'].''.($i - 1 ).''
        )
		);
        $ts->editChannel(self::$config['write_channel'], array(
                'channel_description' => $desc
            )
        );
    }
    
}

?>