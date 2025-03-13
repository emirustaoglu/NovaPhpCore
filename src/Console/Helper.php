<?php

namespace NovaPhp\Core\Console;

class Helper
{
    public static function phpSurum($num = 0)
    {
        $output = shell_exec('php -v');

        // Çıktının ilk satırını alalım
        $lines = explode("\n", trim($output));
        $versionLine = $lines[0];

        // Sadece PHP versiyon numarasını alalım
        if (preg_match('/^PHP (\S+)/', $versionLine, $matches)) {
            if ($num == 1) {
                return str_replace(".", "", $matches[1]);
            } else if ($num == 2) {
                return $matches[1];
            }
            return "PHP Sürümünüz: " . $matches[1];
        } else {
            return "PHP Sürümü Öğrenilemedi";
        }
    }


    public static function novaPhpAsci()
    {
        return "\e[31m  _   _                 ____  _           
 | \ | | _____   ____ _|  _ \| |__  _ __  
 |  \| |/ _ \ \ / / _` | |_) | '_ \| '_ \ 
 | |\  | (_) \ V / (_| |  __/| | | | |_) |
 |_| \_|\___/ \_/ \__,_|_|   |_| |_| .__/ 
                                   |_|    \e[0m\r\n";
    }
}