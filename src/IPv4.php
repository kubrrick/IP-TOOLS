<?php

namespace kubrick;

class IPv4
{
    private $IPv4;
    private $array_IPv4;
    private $binary_IPv4;
    private $network_mask;
    private $host_mask;

    public function __construct(string $IPv4, int $mask)
    {
        $this->IPv4 = $IPv4;
        $this->array_IPv4 = $this->addressToBlock($IPv4);
        $this->binary_IPv4 = $this->addressToBinary($this->array_IPv4);
        $this->network_mask = $mask;
        $this->host_mask = 32 - $mask;
    }

    private function addressToBlock(string $IPv4):array {

        preg_match_all("#[0-9]+#", $IPv4, $temp_array_IPv4);

        return $temp_array_IPv4[0];
    }

    private function addressToBinary(array $array_IPv4):string {

        $binary_IPv4 = "";
        foreach ($array_IPv4 as $block) {

            $binary_block = decbin($block);

            if (strlen($binary_block) < 8) {
                for ($i = strlen($binary_block); $i < 8; $i++) {
                    $binary_block = 0 . $binary_block;
                }
            }
            $binary_IPv4 .= $binary_block;
        }

        return $binary_IPv4;
    }

    public function getBinaryAddress():string {
        return $this->binary_IPv4;
    }

    public function getAddress():string{
        return $this->IPv4;
    }

    public function getNetworkMask():int {
        return $this->network_mask;
    }

    public function getHostMask():int {
        return $this->host_mask;
    }

    public function getNetworkPart():string {
        return "";
    }

    public function getBinaryNetworkPart():string {
        return substr($this->binary_IPv4, 0, -$this->host_mask);
    }

    public function getBinaryHostPart():string {
        return substr($this->binary_IPv4, $this->network_mask);
    }

    public function getHostPart():string {
        return "";
    }

}