media-boot-php
=============
Startup/shutdown script that send ISCP commands, wake-on-lan magic packets and can execute commands on remote hosts via ssh.

Howto
-------
Modify media-boot.php to suit your needs and tie into your system startup and shutdown procedures.

	$php media-boot.php
	Usage: media-boot.php [ start | stop ]
  

Why
-------
Needed something to make my home cinema setup a bit easier to use, during the bootup process of my media pc (running xbmc) i use this script to 
wake up the NAS and a server via wake-on-lan, and make sure my onkyo receiver is turned on and set to the right input. Slapped together in a hurry this weekend.