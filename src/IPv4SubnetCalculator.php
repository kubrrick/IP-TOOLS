<?php

namespace kubrick;

abstract class IPv4SubnetCalculator
{

    /**
     * @param IPv4 $IPv4
     * @param int $n
     * @return IPv4[]
     */
    public static function getNextAddresses(IPv4 $IPv4,int $n, int $ex = 0):array {

        $IPv4s = [$IPv4];

        for ($round=0; $round < $n; $round++){

            $IPv4 = end($IPv4s);

            $new_binary_host_part = decbin(bindec($IPv4->getBinaryHostPart()) + 1);

            if (strlen($IPv4->getBinaryHostPart()) < strlen($new_binary_host_part)){
                if($ex == 0){
                    break;
                } elseif ($ex == 1){
                    throw new OutOfIPv4Exception("");
                }
            }
            else if(strlen($IPv4->getBinaryHostPart()) > strlen($new_binary_host_part)){
                for ($i = strlen($new_binary_host_part); $i<strlen($IPv4->getBinaryHostPart()); $i++){
                    $new_binary_host_part = 0 . $new_binary_host_part;
                }
            }

            if(preg_match("#^1+$#", $new_binary_host_part) || preg_match("#^0+$#", $new_binary_host_part)){
                if($ex == 0) {
                    break;
                } elseif ($ex == 1){
                    throw new OutOfIPv4Exception("");
                }
            }

            $newBinaryIPv4 = $IPv4->getBinaryNetworkPart() . $new_binary_host_part;
            $binary_array_IPV4 = str_split($newBinaryIPv4, 8);


            $array_IPv4 = [];
            foreach ($binary_array_IPV4 as $block){
                array_push($array_IPv4, bindec($block));
            }

            array_push($IPv4s, new IPv4(implode(".", $array_IPv4), $IPv4->getNetworkMask()));

        }

        array_shift($IPv4s);
        return $IPv4s;
    }
}