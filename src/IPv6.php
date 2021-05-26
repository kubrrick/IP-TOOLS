<?php

namespace kubrick;

class IPv6
{
    const DUID_TYPE_LL = "DUID-LL";
    const DUID_TYPE_LLT = "DUID-LLT";
    const DUID_TYPE_EN = "DUID-EN";
    const DUID_TYPE_UUID = "DUID-UUID";
    const DUID_TYPE_UNKNOWN = "DUID-UNK";

    const DUID_LINK_TYPE_ETHERNET = "LINK-ETHERNET";
    const DUID_LINK_TYPE_AX25 = "LINK-AX25";
    const DUID_LINK_TYPE_TOKEN_RING = "LINK-TOKEN_RING";
    const DUID_LINK_TYPE_UNKNOWN = "LINK-UNK";

    private $raw_IPv6;
    private $array_IPv6;
    private $binary_IPv6;
    private $full_IPv6;
    private $abb_IPv6;
    private $network_mask;
    private $host_mask;

    public static $binary_values = array(
        "0000" => "0",
        "0001" => "1",
        "0010" => "2",
        "0011" => "3",
        "0100" => "4",
        "0101" => "5",
        "0110" => "6",
        "0111" => "7",
        "1000" => "8",
        "1001" => "9",
        "1010" => "a",
        "1011" => "b",
        "1100" => "c",
        "1101" => "d",
        "1110" => "e",
        "1111" => "f",
    );
    public static $hex_values = array(
        "0" => "0000",
        "1" => "0001",
        "2" => "0010",
        "3" => "0011",
        "4" => "0100",
        "5" => "0101",
        "6" => "0110",
        "7" => "0111",
        "8" => "1000",
        "9" => "1001",
        "a" => "1010",
        "b" => "1011",
        "c" => "1100",
        "d" => "1101",
        "e" => "1110",
        "f" => "1111"
    );

    public function __construct(string $IPv6, int $network_mask)
    {
        if (!$this->isValidAddress($IPv6)){
            return null;
        } else{
            $this->raw_IPv6 = $IPv6;
            $this->array_IPv6 = $this->addressToBlock($IPv6);
            $this->binary_IPv6 = $this->addressToBinary($this->array_IPv6);
            $this->full_IPv6 = $this->fullAddress();
            $this->abb_IPv6 = $this->abbreviate();
            $this->network_mask = $network_mask;
            $this->host_mask = 128 - $network_mask;
        }
    }

    private function addressToBlock($IPv6):array {

        preg_match_all("#^(?<=)[0-9a-fA-F:]+(?=::)#", $IPv6, $IPv6_temp_a);
        preg_match_all("#[a-fA-F0-9]+#", implode("", $IPv6_temp_a[0]), $IPv6_temp_a);
        preg_match_all("#(?<=::)[0-9a-fA-F:]+(?<=)$#", $IPv6, $IPv6_temp_b);
        preg_match_all("#[a-fA-F0-9]+#", implode("", $IPv6_temp_b[0]), $IPv6_temp_b);

        preg_match_all("#[a-fA-F0-9]+#", $IPv6, $IPv6_temp_all);

        $IPv6_unab = array();
        if((count($IPv6_temp_a[0]) || count($IPv6_temp_b[0])) > 0){
            $missing_numbers = 8 - count($IPv6_temp_all[0]);

            foreach ($IPv6_temp_a[0] as $item){
                array_push($IPv6_unab, $item);
            }

            for($i = 0; $i < $missing_numbers; $i++ ){
                array_push($IPv6_unab, "0000");
            }

            foreach ($IPv6_temp_b[0] as $item){
                array_push($IPv6_unab, $item);
            }
        } else{
            foreach ($IPv6_temp_all[0] as $item){
                array_push($IPv6_unab, $item);
            }
        }

        foreach ($IPv6_unab as $key => $item){;
            if(strlen($item) < 4) {
                for ($i = 0; $i < (4 - strlen($item)) ; $i++) {
                    $IPv6_unab[$key] = "0" . $IPv6_unab[$key];
                }
            }
        }

        return $IPv6_unab;
    }

    private function addressToBinary($array_IPv6):string {
        $full_ipv6 = "";
        foreach ($array_IPv6 as $block) {
            $full_ipv6 .= $block;
        }

        $split_IPv6 = str_split($full_ipv6, 1);

        $binary = "";
        foreach ($split_IPv6 as $value){
            $binary .= array_search($value, IPv6::$binary_values);
        }
        return $binary;
    }

    private function abbreviate():string {
        $array = $this->array_IPv6;
        foreach ($array as $id => $block){
            $array[$id] = preg_replace("#^0+#", "", $block);

            if(empty($array[$id])){
                $array[$id] = "0";
            }
        }

        $final = "";
        $abb = false;
        $nbz = 0;
        for($i = 7; $i + 1; $i--){
            if($array[$i] == "0"){
                if($abb){
                    $final = $array[$i] . $final;
                    $final = ($i != 0)? ":" . $final : $final;
                } else{
                    $nbz++;
                }
            } else {
                if($nbz >= 2 && !$abb){
                    $abb = true;
                    $final = ($final != "")? $array[$i] . ":" . $final : $array[$i] . "::";
                } else{
                    $final = $array[$i] . $final;
                }
                $final = ($i != 0)? ":" . $final : $final;
            }
        }
        return $final;
    }

    private function fullAddress():string{
        return implode(":", $this->array_IPv6);
    }


    public static function isValidAddress(string $IPv6):bool {
        return (filter_var($IPv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))? true : false;
    }

    /**
     *
     * Get the DUID type based on RFC8415 -> https://tools.ietf.org/html/rfc8415
     *
     * @param string $DUID
     * @return string
     */
    public static function getDUIDType(string $DUID):string {

        preg_match("#(?<=^00:0)[0-9a-fA-F]#", $DUID, $type);

        switch ($type[0]){
            case 1:
                return self::DUID_TYPE_LLT;
            case 2:
                return self::DUID_TYPE_EN;
            case 3:
                return self::DUID_TYPE_LL;
            case 4:
                return self::DUID_TYPE_UUID;
            default :
                return self::DUID_TYPE_UNKNOWN;
        }

    }

    /**
     *
     * Get the DUID link type based on iana -> https://www.iana.org/assignments/arp-parameters/arp-parameters.xhtml
     *
     * @param string $DUID
     * @return string
     */
    public static function getDUIDLLLinkType(string $DUID):string {

        preg_match("#(?<=^00:0[0-9a-fA-F]:00:0)[0-9a-fA-F]#", $DUID, $type);

        switch ($type[0]){
            case 1:
                return self::DUID_LINK_TYPE_ETHERNET;
            case 3:
                return self::DUID_LINK_TYPE_AX25;
            case 4:
                return self::DUID_LINK_TYPE_TOKEN_RING;
            default :
                return self::DUID_LINK_TYPE_UNKNOWN;
        }

    }

    public static function getDUIDLLMACAddress(string $DUID):?string {
        if(self::getDUIDType($DUID) == self::DUID_TYPE_LL || self::getDUIDType($DUID) == self::DUID_TYPE_LLT){
            preg_match("#[0-9a-fA-F:]{17}$#", $DUID, $MAC);
            return $MAC[0];
        } else {
            return null;
        }
    }

    public function getNetworkAddress():string {
        return "";
    }

    public function getNetworkPart():string{
        return substr($this->binary_IPv6,0 , -(128 - $this->network_mask));
    }

    public function getHostPart():string {
        return "";
    }

    public function getNetworkMask():int {
        return $this->network_mask;
    }

    public function getBinaryNetworkPart():string {
        return substr($this->binary_IPv6, 0, -$this->host_mask);
    }

    public function getBinaryHostPart():string {
        return substr($this->binary_IPv6, $this->network_mask);
    }

    public function getHostMask():int {
        return $this->host_mask;
    }

    public function getFullIPv6Address():string {
        return $this->full_IPv6;
    }

    public function getBinaryIPv6Address():string {
        return $this->binary_IPv6;
    }

    public function getAbbreviatedIPv6Address():string {
        return $this->abb_IPv6;
    }

}