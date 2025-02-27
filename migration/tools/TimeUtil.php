<?php
namespace Migration\Tools;

class TimeUtil {
    private static $timestamp;
    private static $user;

    public static function initialize($timestamp = null, $user = null) {
        self::$timestamp = $timestamp ?: date('Y-m-d H:i:s');
        self::$user = $user ?: get_current_user();
    }

    public static function getTimestamp() {
        return self::$timestamp ?: '2025-02-26 10:20:02';
    }

    public static function getUser() {
        return self::$user ?: 'niloc95';
    }

    public static function formatTimestamp($timestamp = null) {
        $time = $timestamp ?: self::getTimestamp();
        return date('Y-m-d H:i:s', strtotime($time));
    }
}