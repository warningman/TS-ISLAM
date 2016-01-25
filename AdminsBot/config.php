<?php
//Ts-Islam
$config = array();
$config[] = array();

$config[1]['connection'] = array(
         
/* Server IP  		*/			'server_ip' => '127.0.0.1',
/* Query Port 		*/			'server_query_port' => 10011,
/* Server ID  		*/  		'server_id' => 1,
/* Query login 	 	*/			'server_query_login' => 'serveradmin',
/* Query pass   	*/			'server_query_password' => 'IRu7KBRK',
/* Nick Name  		*/			'bot_name' => 'AdminsBot',
/* Channel to move  */			'move_to_channel' => 6
);

$config[1]['events'] = array(
			          
'events_configs' => array(
'adminsOnline' => array(
/* Id channel   	*/			'write_channel' => 6,					
/* Name channel 	*/			'channel_name' => '[cspacer]Admin Online :   ',
/* Up Desc. 		*/			'up_description' => '\n[center][size=15][color=purple]Administration Status[/color][/size][/center][hr]\n',
/* Down Desc.  		*/			'update' => 'Last Update:',
/* Info Down Desc. 	*/			'info' => 'By Ts-Islam',
/* Admins Group  	*/			'groups' => array(61,11,57,12,13,123)
		),
	)
);
?>
