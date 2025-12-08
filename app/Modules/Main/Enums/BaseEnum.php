<?php


namespace App\Modules\Main\Enums;


use ReflectionClass;

abstract class BaseEnum
{

    
    /**
     * @return array
     */
    public static function getAll()
    {
        try {
            $class = new ReflectionClass(get_called_class());
        } catch (\ReflectionException $e) {
            \Log::error('Enum Reflection Class Error: '. $e->getMessage());
        }
        return array_combine(self::getValues(), self::getKeys());
    }

    /**
     * @return array
     */
    public static function getKeys()
    {
        try {
            $class = new ReflectionClass(get_called_class());
        } catch (\ReflectionException $e) {
            \Log::error('Enum Reflection Class Error: '. $e->getMessage());
        }
        return array_keys($class->getConstants());
    }
    /**
     * @return array
     */
    public static function getValues()
    {
        try {
            $class = new ReflectionClass(get_called_class());
        } catch (\ReflectionException $e) {
            \Log::error('Reflection Class Error: '. $e->getMessage());
        }
        return array_values($class->getConstants());
    }
    /**
     * @return integer
     */
    public static function getKey($value)
    {
        try {
            $class = new ReflectionClass(get_called_class());
            $enum = array_flip($class->getConstants());
            return isset($enum[$value]) ? str_replace('_',' ', ucfirst($enum[$value])) : "{$value}";
        } catch (\ReflectionException $e) {
            \Log::error('Reflection Class Error: '. $e->getMessage());
        }
        return null;
    }
}

