<?php

switch(@$_SERVER['argv'][1])
{

case 'start':

  // wake up xbmc-db
  wol("10.10.0.255", "90:e6:ba:d6:3c:ef");

  // wake up the nas
  wol("10.10.0.255", "00:00:24:a6:a7:e4");

  // connect to receiver then power on & select input 5 (pc)
  $iscp = new iscp("10.10.0.9");
  //$iscp->sendcmd("!1PWR01");
  //$iscp->sendcmd("!1SLI05");
  break;

case 'stop':

  // shutdown xbmc-db
  $ssh = new ssh("server", "xbmc", "shutdown_if_idle");

  // shutdown the nas
  $ssh = new ssh("nas", "xbmc", "shutdown_if_idle");

  // power off receiver
  $iscp = new iscp("10.10.0.9");
  //$iscp->sendcmd("!1PWR00");
  break;

default:
  print "Usage: media-boot.php [ start | stop ]\n";

}

function wol($bcast, $mac)
{
  $b = explode(':', $mac);
  $hwaddr = '';

  for($i=0;$i<6;$i++)
    $hwaddr .= chr(hexdec($b[$i]));

  $p = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);

  for($i=1;$i<=16;$i++)
    $p .= $hwaddr;

  $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  socket_set_option($s, SOL_SOCKET, SO_BROADCAST, TRUE);

  if(socket_sendto($s, $p, strlen($p), 0, $bcast, 7))
    print "sent magic packet to ".$mac."\n";

  socket_close($s);
}

class iscp
{

  protected $ip;
  protected $port = 60128;

  protected $fp;
  protected $errno;
  protected $errstr;

  public function __construct($ip=false, $port=false)
  {
    if($ip!=false) $this->ip = $ip;
    if($port!=false) $this->port = $port;
    self::connect();
  }

  public function __destruct()
  {
    $this->disconnect();
  }

  public function connect()
  {
    $this->fp = pfsockopen("tcp://".$this->ip, $this->port, $this->errno, $this->errstr);
  }

  public function disconnect()
  {
    @fclose($this->fp);
  }

  public function sendcmd($cmd)
  {
    fwrite($this->fp, "ISCP\x00\x00\x00\x10\x00\x00\x00".chr(strlen($cmd)+1+16)."\x01\x00\x00\x00".$cmd."\x0d");
    print "sent command ".$cmd." to tcp://".$this->ip.":".$this->port."\n";
  }
}

class ssh
{
  public function __construct($host=false, $user=false, $cmd=false)
  {
    $_cmd = "ssh ";
    if($user!=false) $_cmd .= $user."@";
    $_cmd .= $host." '".$cmd."'";
    passthru($_cmd);
  }
}
