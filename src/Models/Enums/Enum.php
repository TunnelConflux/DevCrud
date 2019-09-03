<?php
/**
 * Project      : DevCrud
 * File Name    : Enum.php
 * User         : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/04/24 7:32 PM
 */

namespace TunnelConflux\DevCrud\Models\Enums;

use Illuminate\Support\Str;
use ReflectionClass;

abstract class Enum
{
    private static function data(): array
    {
        return (new ReflectionClass(get_called_class()))->getConstants();
    }

    static function getKeys()
    {
        return array_flip(self::data());
    }

    static function getKey($value)
    {
        return self::getKeys()[$value] ?? null;
    }

    static function getValue($key)
    {
        return self::data()[$key];
    }

    static function getHumanKeys()
    {
        $keys = self::getKeys();

        return array_map(function ($value) {
            return self::getHumanValue($value, true);
        }, $keys);
    }

    static function getHumanValue($value, $convert = false)
    {
        if ($convert) {
            return Str::title(str_replace("_", " ", Str::snake($value ?: "")));
        }

        return Str::title(str_replace("_", " ", Str::snake(self::getKey($value) ?: "")));
    }
}