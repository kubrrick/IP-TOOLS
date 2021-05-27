# IP-TOOLS
IPv4 and IPv6 tools for PHP

## Getting Started

### Installation

IP-TOOLS requires PHP >= 7.2.

```shell
composer require kubrick/ip-tools
```

### Basic Usage
```php
<?php

require_once 'vendor/autoload.php';

// Create an IPv4 address 127.0.0.1/24
$IPv4 = new kubrick\IPv4\IPv4("127.0.0.1", 24);

// Get the next IPv4 Address
$nextIPv4s = IPv4SubnetCalculator::getNextAddresses($IPv4, 1);
echo $nextIPv4Address[0]->getAddress()