<?php

namespace App\Helpers;

/**
 * Sanitize Helper Class
 * 
 * Provides methods to sanitize data against XSS attacks and other malicious inputs.
 * This helper ensures that all user input is properly cleaned before being used
 * in the application.
 *
 * Features:
 * - HTML special characters encoding
 * - Recursive array sanitization
 * - Strip tags
 * - Custom allowed HTML tags
 * - Type preservation
 *
 * @package App\Helpers
 * @version 1.0.0
 */
class Sanitize {

    /**
     * List of HTML tags that are allowed
     * Empty array means no tags are allowed
     *
     * @var array
     */
    private static array $allowedTags = [];

    /**
     * Sanitizes a single value or an array of values
     * 
     * Handles different types of data and preserves the type while sanitizing.
     * Arrays are processed recursively.
     *
     * @param mixed $data    The data to sanitize
     * @param bool  $strip   Whether to strip HTML tags (default: true)
     * @return mixed The sanitized data
     */
    public static function clean(mixed $data, bool $strip = true): mixed
    {
        if (is_array($data)) {
            return self::cleanArray($data, $strip);
        }

        if (is_string($data)) {
            return self::cleanString($data, $strip);
        }

        if (is_object($data)) {
            return self::cleanObject($data, $strip);
        }

        // For other types (int, float, bool, null) return as is
        return $data;
    }

    /**
     * Sanitizes a string value
     *
     * @param string $string The string to sanitize
     * @param bool   $strip  Whether to strip HTML tags
     * @return string The sanitized string
     */
    private static function cleanString(string $string, bool $strip): string
    {
        // Convert special characters to HTML entities
        $string = htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($strip) {
            // Strip HTML tags, keeping allowed ones if specified
            $string = strip_tags($string, self::getAllowedTags());
        }

        // Remove null bytes and other dangerous characters
        $string = str_replace(
            [chr(0), chr(1), chr(2), chr(3), chr(4), chr(5), chr(6), chr(7), chr(8), chr(11), chr(12), chr(14), chr(15), chr(16), chr(17), chr(18), chr(19)],
            '',
            $string
        );

        return trim($string);
    }

    /**
     * Sanitizes an array recursively
     *
     * @param array $array The array to sanitize
     * @param bool  $strip Whether to strip HTML tags
     * @return array The sanitized array
     */
    private static function cleanArray(array $array, bool $strip): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            // Sanitize both key and value
            $cleanKey = is_string($key) ? self::cleanString($key, $strip) : $key;
            $result[$cleanKey] = self::clean($value, $strip);
        }
        return $result;
    }

    /**
     * Sanitizes an object by converting it to array and back
     *
     * @param object $object The object to sanitize
     * @param bool   $strip  Whether to strip HTML tags
     * @return object The sanitized object
     */
    private static function cleanObject(object $object, bool $strip): object
    {
        // Convert to array, clean, and convert back to object
        $array = json_decode(json_encode($object), true);
        $cleanArray = self::cleanArray($array, $strip);
        return (object) $cleanArray;
    }

    /**
     * Sets the allowed HTML tags
     *
     * @param array $tags Array of allowed HTML tags (e.g., ['<p>', '<a>'])
     * @return void
     */
    public static function setAllowedTags(array $tags): void
    {
        self::$allowedTags = array_map('trim', $tags);
    }

    /**
     * Gets the string of allowed HTML tags for strip_tags()
     *
     * @return string Allowed tags string or empty string if no tags allowed
     */
    private static function getAllowedTags(): string
    {
        return implode('', self::$allowedTags);
    }

    /**
     * Sanitizes file name to prevent directory traversal attacks
     *
     * @param string $filename The file name to sanitize
     * @return string The sanitized file name
     */
    public static function filename(string $filename): string
    {
        // Remove any directory traversal attempts
        $filename = basename($filename);
        
        // Remove any null bytes and other dangerous characters
        $filename = str_replace(
            ['../', './', '\\', chr(0)],
            '',
            $filename
        );

        // Remove special characters that might be problematic in filenames
        return preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    }

    /**
     * Sanitizes a URL
     *
     * @param string $url The URL to sanitize
     * @return string|null The sanitized URL or null if invalid
     */
    public static function url(string $url): ?string
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * Sanitizes an email address
     *
     * @param string $email The email address to sanitize
     * @return string|null The sanitized email or null if invalid
     */
    public static function email(string $email): ?string
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }
} 