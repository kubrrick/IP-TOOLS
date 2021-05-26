<?php

namespace kubrick;

abstract class IPv6SubnetCalculator
{

    /**
     * @param IPv6 $IPv6
     * @param int $upper_prefix
     * @param int $n
     * @param int $ex
     * @return IPv6[] return n IPv6 Prefixes
     */
    public static function getNextIPv6Prefix(IPv6 $IPv6, int $upper_prefix, int $n, $ex = 0):array {

        if($upper_prefix > 63 || $upper_prefix > $IPv6->getNetworkMask()){
            return [];
        }

        $IPv6s = [$IPv6];

        for ($round=0; $round < $n; $round++){

            $IPv6 = end($IPv6s);

            $binary_prefix =  substr($IPv6->getNetworkPart(), $upper_prefix);
            $uper_binary_prefix = substr($IPv6->getNetworkPart(), 0, $upper_prefix);
            $new_binary_prefix = decbin(bindec($binary_prefix) + 1);

            if (strlen($binary_prefix) < strlen($new_binary_prefix)){
                if ($ex == 0){
                    break;
                } elseif ($ex == 1){
                    throw new OutOfIPv6PrefixException();
                }
            }
            else if(strlen($binary_prefix) > strlen($new_binary_prefix)){
                for ($i = strlen($new_binary_prefix); $i<strlen($binary_prefix); $i++){
                    $new_binary_prefix = 0 . $new_binary_prefix;
                }
            }

            $new_ipv6_binary_address = $new_binary_prefix = $uper_binary_prefix . $new_binary_prefix;
            for($i = strlen($new_binary_prefix); $i < 128; $i++){
                $new_ipv6_binary_address .= 0;
            }

            $binary_split_IPv6 = str_split($new_ipv6_binary_address, 4);

            $hexa_full_IPv6 = "";
            foreach ($binary_split_IPv6 as $value){
                $hexa_full_IPv6 .= array_search($value, IPv6::$hex_values);
            }

            $hexa_split_IPv6 = str_split($hexa_full_IPv6, 4);
            $string_IPv6 = "";
            foreach ($hexa_split_IPv6 as $value){
                $string_IPv6 = implode(":", $hexa_split_IPv6);
            }

            array_push($IPv6s, new IPv6($string_IPv6, $IPv6->getNetworkMask()));
        }

        array_shift($IPv6s);
        return $IPv6s;

    }

    /**
     * @param IPv6 $IPv6
     * @param int $number
     * @param int $ex
     * @return IPv6[]
     */
    public static function getNextIPv6Address(IPv6 $IPv6, int $number, $ex = 0):array {

        $IPv6s = [$IPv6];

        for ($round=0; $round < $number; $round++){

            $IPv6 = end($IPv6s);

            $new_binary_host_part = decbin(bindec($IPv6->getBinaryHostPart()) + 1);

            if (strlen($IPv6->getBinaryHostPart()) < strlen($new_binary_host_part)){
                if($ex == 0){
                    break;
                } elseif ($ex == 1){
                    throw new OutOfIPv6PrefixException("");
                }
            } else{
                for($i = strlen($new_binary_host_part); $i < $IPv6->getHostMask(); $i++){
                    $new_binary_host_part = 0 . $new_binary_host_part ;
                }
            }

            $new_ipv6_binary_address = $IPv6->getBinaryNetworkPart() . $new_binary_host_part;

            $binary_split_IPv6 = str_split($new_ipv6_binary_address, 4);

            $hexa_full_IPv6 = "";
            foreach ($binary_split_IPv6 as $value){
                $hexa_full_IPv6 .= array_search($value, IPv6::$hex_values);
            }

            $hexa_split_IPv6 = str_split($hexa_full_IPv6, 4);
            $string_IPv6 = "";
            foreach ($hexa_split_IPv6 as $value){
                $string_IPv6 = implode(":", $hexa_split_IPv6);
            }

            array_push($IPv6s, new IPv6($string_IPv6, $IPv6->getNetworkMask()));
        }

        array_shift($IPv6s);
        return $IPv6s;

    }

    /**
     * @param IPv6 $IPv6
     * @param int $prefix
     * @return IPv6[]
     */
    public static function getPrefixRange(IPv6 $IPv6, int $prefix):array {

    }

    /**
     * @param IPv6 $IPv6
     * @return IPv6[]
     */
    public static function getHostRange(IPv6 $IPv6):array {

    }
}
