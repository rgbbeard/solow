<?php
/*
/ Minimum PHP version 7.x
/ Using PHP version 8.0.1
/ Author - Davide
/ Git - github.com/rgbbeard/
*/

define("TIMEZONE_ROME", "Europe/Rome");
define("TIMEZONE_UTC", "UTC");

function dump(...$vars) {
    $ts = date("dmYHis");
    foreach($vars as $var) {
        $_SESSION["DEBUG"][$ts][] = $var;
    }
}

function info_log(string $message, int $type = 1) {
    $log_type = "INFO";

    switch($type) {
        case 2:
            $log_type = "ERROR";
            break;
        case 3:
            $log_type = "WARNING";
            break;
    }

    error_log("[$log_type] $message");
}

function first(array $target) {
    return array_shift($target);
}

function array_delast(array $target): array {
    array_pop($target);
    return $target;
}

function array_exclude(array $target, $element) {
    $temp = [];
    for ($x = 0; $x < count($target); $x++) {
        if($x == $element) {
            continue;
        }
        $temp[] = $target[$x];
    }
    return $temp;
}

function array_clear(array $target) {
    $temp = [];
    foreach($target as $item) {
        if(!empty($item)) {
            $temp[] = $item;
        }
    }
    return $temp;
}

function get_host() {
    $host = $_SERVER["HTTP_HOST"];

    $httpx = !@empty($_SERVER["HTTPS"]) ? "https" : "http";

    return "$httpx://$host";
}

# relative path
function relpath(string $file, string $localhostBase = ""): string {
    $current_url = $_SERVER["REQUEST_URI"];

    # Remove parameters
    $paramIndex = strpos($current_url, '?');
    if($paramIndex !== false) {
        $current_url = substr($current_url, 0, $paramIndex);
    }

    # Remove leading slash
    $current_url = ltrim($current_url, '/');

    # Traverse directories
    $levelUp = str_repeat('../', substr_count($current_url, '/'));
    $file = $levelUp . $file;

    # Append localhost base if applicable
    if(defined("localhost_base") && !empty(localhost_base)) {
        $localhostBase = localhost_base;
        $file = $localhostBase . $file;
    } elseif(!empty($localhostBase)) {
        $localhostBase = localhost_base;
        $file = $localhostBase . $file;
    }

    return $file;
}


function std2_array($stdclass): array {
    $temp = [];
    foreach($stdclass as $name => $value) {
        if($value instanceof stdClass) {
            $temp[$name] = std2_array($value);
        } else {
            $temp[$name] = $value;
        }
    }
    
    return $temp;
}

function capitalize(string $target): string {
    $words = explode(" ", $target);
    $temp = [];
    
    foreach($words as $word) {
        $word = strtolower($word);
        $temp[] = ucfirst($word);
    }
    
    return implode(" ", $temp);
}

function is_email(string $target) {
    return preg_match("/([a\.\--z\_]*[a0-z9]+@)([a-z]+\.)([a-z]{2,6})/", $target);
}

function is_it_phone(string $target, bool $type = false) {
    if($type === true) {
        #Code to detect if it's mobile or phone
    }
    
    return preg_match("/((3|0)([0-9]+){9,})/", $target);
}

function is_date(string $target) {
    return preg_match("/([\d]+){1,2}[\-|\/]([\d]+){1,2}[\-|\/]([\d]+){4}/", $target);
}

function get_date(string $timezone = TIMEZONE_UTC, string $separator = "/"): string {
    date_default_timezone_set($timezone);
    return date("d{$separator}m{$separator}Y");
}

function get_time(string $timezone = TIMEZONE_UTC, string $separator = ":"): string {
    date_default_timezone_set($timezone);
    return date("G{$separator}i{$separator}s");
}

function simple_hash(string $rawpw, string $algo = "sha256"): string {
    $salt = ""; # Here goes your hash key
    $saltc = str_split($salt);
    $startc = str_split($rawpw);
    $hash = array();

    for($x = 0;$x<count($startc);$x++) {
        $hash[] = $startc[$x] . $saltc[$x];
    }

    $hashsize = count($hash);

    $hash = implode("", $hash);

    return substr(hash($algo, $hash), 0, ($hashsize-2));
}

function timeout_location(string $location, int $timeout = 0) {
    header("refresh: $timeout;url=" . relpath($location));
}

function get_ip(): ?string {
    $ip = null;

    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR']; 
    }

    return $ip;
}