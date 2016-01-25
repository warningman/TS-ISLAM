#!/bin/bash
# Colors
ESC_SEQ="\x1b["
COL_RESET=$ESC_SEQ"39;49;00m"
COL_RED=$ESC_SEQ"31;01m"
COL_GREEN=$ESC_SEQ"32;01m"
COL_YELLOW=$ESC_SEQ"33;01m"


echo -e "$COL_RED
------------------------------------------------------------------------
|                                                                      |
|            
|               ========================================               |
|               |	  $COL_YELLOW Ts-Islam	$COL_RED	|              |
|               ========================================               |
|                                                                      |
------------------------------------------------------------------------ $COL_RESET"

 if [ $1 = 'stop' ] 
    then 
        pkill -f AdminsBot
		echo -e "Admins_Bot: $COL_GREEN Bot has been STOPED! $COL_RESET"
    fi

if [ $1 = 'start' ] 
    then 
        screen -dmS AdminsBot php adminsbot.php -i 1
		echo -e "Admins_Bot: $COL_GREEN Bot has been STARTED! $COL_RESET"
    fi
 
 