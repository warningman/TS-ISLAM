<?PHP
/**
  * 
  *			Ts-Islam
  *
**/
date_default_timezone_set("Asia/Riyadh"); 
require("libraries/ts3admin.class.php");
include 'config.php';

$tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {

	$tsAdmin->login($ts3_user, $ts3_pass);
	$tsAdmin->selectServer($ts3_port);	
	$tsAdmin->setName($bot_nickname);
	
	$whoami = $tsAdmin->getElement('data', $tsAdmin->whoAmI());
    $tsAdmin->clientMove($whoami['client_id'],$bot_move);
	
	
//█▀▀█ ▄█─ █▀█ █▀▀█ █▀▀ ─█▀█─ ▄▀▀▄ ▀▀▀█ ▄▀▀▄ ▄▀▀▄  
//█▄▀█ ─█─ ─▄▀ ──▀▄ ▀▀▄ █▄▄█▄ █▄▄─ ──█─ ▄▀▀▄ ▀▄▄█ 
//█▄▄█ ▄█▄ █▄▄ █▄▄█ ▄▄▀ ───█─ ▀▄▄▀ ─▐▌─ ▀▄▄▀ ─▄▄▀ 

			
//▄ 
//─ 
//▀ 
			
			
$line1[0] = "█▀▀█";
$line2[0] = "█▄▀█";
$line3[0] = "█▄▄█";

$line1[1] = "▄█─";
$line2[1] = "─█─";
$line3[1] = "▄█▄";
		
$line1[2] = "█▀█";
$line2[2] = "─▄▀";
$line3[2] = "█▄▄";
			
$line1[3] = "█▀▀█";
$line2[3] = "──▀▄";
$line3[3] = "█▄▄█";
					
$line1[4] = "─█▀█─";
$line2[4] = "█▄▄█▄";
$line3[4] = "───█─";

$line1[5] = "█▀▀";
$line2[5] = "▀▀▄";
$line3[5] = "▄▄▀";

$line1[6] = "▄▀▀▄";
$line2[6] = "█▄▄─";
$line3[6] = "▀▄▄▀";

$line1[7] = "▀▀▀█";
$line2[7] = "──█─";
$line3[7] = "─▐▌─";

$line1[8] = "▄▀▀▄";
$line2[8] = "▄▀▀▄";
$line3[8] = "▀▄▄▀";

$line1[9] = "▄▀▀▄";
$line2[9] = "▀▄▄█";
$line3[9] = "─▄▄▀";

while(1) {
				
			$time = date('h:i', time());			
				
				$time_explode = explode(':', date('h:i') );
				$time_H = str_split($time_explode[0],1);
				$time_H1 = $time_H[0];
				$time_H2 = $time_H[1];
				$time_M = str_split($time_explode[1],1);
				$time_M1 = $time_M[0];
				$time_M2 = $time_M[1];		
					
				$channel_time1 =  "[cspacer]".$line1[$time_H1]."─".$line1[$time_H2]."─▄─".$line1[$time_M1]."─".$line1[$time_M2];
				$channel_time2 =  "[cspacer]".$line2[$time_H1]."─".$line2[$time_H2]."───".$line2[$time_M1]."─".$line2[$time_M2];
				$channel_time3 =  "[cspacer]".$line3[$time_H1]."─".$line3[$time_H2]."─▄─".$line3[$time_M1]."─".$line3[$time_M2];

				$tsAdmin->channelEdit($channel_id_1, array('channel_name' => $channel_time1));
				$tsAdmin->channelEdit($channel_id_2, array('channel_name' => $channel_time2));
				$tsAdmin->channelEdit($channel_id_3, array('channel_name' => $channel_time3));
 
 
sleep(10);
}
	
}else{
	echo "Connetcion Problem";
}
?>
