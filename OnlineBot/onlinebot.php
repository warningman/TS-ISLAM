<?PHP
/**
  * 
  *			Ts-Islam
  *
**/

require("lib/ts3admin.class.php");
include 'config.php';

$tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {

	$tsAdmin->login($ts3_user, $ts3_pass);
	$tsAdmin->selectServer($ts3_port);	
	$tsAdmin->setName($bot_nickname);
	
	$whoami = $tsAdmin->getElement('data', $tsAdmin->whoAmI());
    $tsAdmin->clientMove($whoami['client_id'],$bot_move);

	
	
while(1) {	

		$serverInfo = $tsAdmin->getElement('data', $tsAdmin->serverInfo());
        $clientsOnline = ($serverInfo['virtualserver_clientsonline'] - $serverInfo['virtualserver_queryclientsonline']);
		
		
		if($Channel == 1)
		{
			$tsAdmin->channelEdit($channel_id, array('channel_name' => $channel_name.' '.$clientsOnline));
		}
		
		if($Server == 1)
		{
			$tsAdmin->serverEdit(array('virtualserver_name' => $server_name.' '.$clientsOnline));
		}
		
sleep($sleep);									
}
}else{
	echo "Connetcion Problem";
}
?>
