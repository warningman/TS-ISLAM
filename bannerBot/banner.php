<?php
/*
											بسم الله الرحمن الرحيم
											
								توكلنا على الله في ارجاع التيم سبيك زي ما المفروض يكون
									راح نخرب سالفة المواقع بحيث تكون للجميع
							ادعوا الله ان يوفقنا في حيتنا الشخصية وان ييسر لنا امرنا
									كما ان الدعاء علينا لن يضر الا نفسك 
							علما باننا لانهتم بكلام الناس الا برايكم عن النظام
	وشكراً لكل من سبي في الغيب وكلمني على ايميل علشان اصمملك سكربت مجاني تستحق مقابل اجرك بارك الله فيكم وفيني 


								/==========================================\



										 HisRoyal & WARNINGs (V 0.1)
											1437  -  1438 hagry

										 
								\==========================================/





*/

require_once("libraries/TeamSpeak3/TeamSpeak3.php");

try {
	// هنا الاتصال بالسيرفر وكذا عرفت
	  $config = array();
	  $config['teamspeakip'] = 'localhost';
	  $config['QueryName'] = 'serveradmin';
	  $config['QueryPass'] = '';
	  $config['QueryPort'] = '';
	  $config['PortServer'] = '';
	  
	  $config['GroupIDAdmin'] = '';
	  $config['GroupIDactivated'] = '';
	  $config['GroupIDnotactivated'] = '';
	  
	  $ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$config['QueryName'].":".$config['QueryPass']."@".$config['teamspeakip'].":".$config['QueryPort']."/?server_port=".$config['PortServer']."");
	 
	 // التعرف التلقائي
	 foreach ($ts3_VirtualServer->clientList() as $cl) {
		if ($cl->getProperty('connection_client_ip') == $_SERVER['REMOTE_ADDR']) {
		
			// هذه نستعملها للتجارب في المتصفح نصيحة شيلها :)
			header('Content-Type: image/png');
			header('refresh: 3; url=');
			
			$_SESSION ['ggids'] = explode(",", $cl["client_servergroups"]);
				
			if(in_array($config['GroupIDnotactivated'],$_SESSION['ggids'])){
			
				$image_file = 'banner_notactivated.png';
				
			} else if(in_array($config['GroupIDactivated'],$_SESSION['ggids'])){
			
				$image_file = 'banner_activated.png';
				
			} else if(in_array($config['GroupIDAdmin'],$_SESSION['ggids'])){
			
				$image_file = 'banner_admin.png';
				
			} else {
			
				$image_file = 'banner2.png';
				
			}
			// اضافة الصور
			$image = imagecreatefrompng($image_file);

			$sigIndex = rand(0, count($image)-1); 
			// اضافة الالوان
			$white = imagecolorallocate($image, 255, 255, 255);
			$blac1 = imagecolorallocate($image, 85, 85, 127);
			
			imagefilledrectangle($image, 999, 0, 399, 29, $white);
			
			// متغيرات النصوص
			$text = "Welcome," . htmlspecialchars($cl->client_nickname) ."";
			$text = str_replace (" ", "", $text);
			$text = str_replace ("!", "", $text);
			$time = "" . date("H",strtotime("+3 hours")) . ":" . date("i") . "";
			$time2 = "20" . date("y") . "/" . date("m") . "/" . date("d") . "";
			$clientsonline = "" . $ts3_VirtualServer->virtualserver_clientsonline-1 . "";
			$text2 = "   /    ";
			$text3 = "today f3alyt d3m";
			$maxclients = $ts3_VirtualServer->virtualserver_maxclients . "";

			// غير خطك من هنا
			$font = 'ambient.ttf';
			// هنا اضافة النص للصورة
			imagettftext($image, 20, 0, 380, 150, $blac1, $font, $text);
			if ($image_file == "banner9.png"){ 
			imagettftext($image, 20, 0, 75, 263, $black, $font, $time);
			}
			if ($image_file == "banner3.png"){
				imagettftext($image, 20, 0, 500, 263, $blac1, $font, $text3);
			}
			if ($image_file == "banner9.png"){
			imagettftext($im, 20, 0, 20, 240, $black, $font, $time2);
			}
			imagettftext($image, 20, 0, 40, 175, $black, $font, $clientsonline);
			imagettftext($image, 20, 0, 70, 170, $black, $font, $text2);
			imagettftext($image, 20, 0, 130, 175, $black, $font, $maxclients);
			
			// imagepng() للصور الي بنفس الامداد الموضح 
			// imagejpeg() للصور الي بنفس الامداد الموضح 
			imagepng($image, NULL);
			imagedestroy($image);
		}
	}
} catch (Exception $e) { 
echo '<div style="background-color:red; color:white; display:block; font-weight:bold;">QueryError: ' . $e->getCode() . ' ' . $e->getMessage() . '</div>';
die;
}
?> 
