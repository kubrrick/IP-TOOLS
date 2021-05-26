<?php

use kubrick\IPv4;
use kubrick\IPv4SubnetCalculator;
use kubrick\IPv6;
use kubrick\IPv6SubnetCalculator;

require "vendor/autoload.php";

ini_set('precision', 128);


//$nextIPv4s = IPv4SubnetCalculator::getNextAddresses(new IPv4("185.239.196.203", 24),1);

//foreach ($nextIPv4s as $IPv4){
//    echo $IPv4->getAddress() . "/" . $IPv4->getNetworkMask() . "\n";
//}


$IPv4 = new IPv4("127.0.0.209", 24);
$IPv6Address = new IPv6("2001:db8:ffff::d2", 64);
$IPv6Prefix = new IPv6("2001:db8:0:d000::", 56);

$nextIPv4Address = IPv4SubnetCalculator::getNextAddresses($IPv4, 1);
$nextIPv6 = IPv6SubnetCalculator::getNextIPv6Address($IPv6Address, 1);
$nextIPv6Prefix = IPv6SubnetCalculator::getNextIPv6Prefix($IPv6Prefix, 48, 1);

echo "Current IPv4 address: " . $IPv4->getAddress() . "/" . $IPv4->getNetworkMask() . " Next: " . $nextIPv4Address[0]->getAddress() . "/" . $nextIPv4Address[0]->getNetworkMask() . "\n";
echo "Current IPv6 address: " . $IPv6Address->getAbbreviatedIPv6Address() . "/128" . " Next: " . $nextIPv6[0]->getAbbreviatedIPv6Address() . "/128" . "\n";
echo "Current IPv6 Prefix: " . $IPv6Prefix->getAbbreviatedIPv6Address() . "/" . $IPv6Prefix->getNetworkMask() . " Next: " . $nextIPv6Prefix[0]->getAbbreviatedIPv6Address() . "/" . $IPv6Prefix->getNetworkMask() . "\n";


/**


foreach ($nextIPv6Prefixes as $IPv6Prefix){
    echo $IPv6Prefix->getAbbreviatedIPv6Address(). "/" . $IPv6Prefix->getNetworkMask() . "\n";
}
**/