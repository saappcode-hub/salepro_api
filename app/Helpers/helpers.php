<?php

if (!function_exists('jDateTimeFormat')) {
    /**
     * Convert ISO 8601 timestamp to a human-readable format.
     *
     * @param string $isoDate
     * @param string $format
     * @return string
     */
    function jDateTimeFormat(string $isoDate, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            return \Carbon\Carbon::createFromFormat($format, $date)->format($format);
        } catch (\Exception $e) {
            return $isoDate; // fallback to original string if parsing fails
        }
    }
}

if (!function_exists('jPoints')) {
    /**
     * Format a coordinate string to a fixed number of decimal places.
     *
     * @param string $coordString e.g. "11.602402325322371, 104.88625161745807"
     * @param int $decimals Number of decimal places (default 6)
     * @return string Formatted coordinates
     */
    function jPoints(string $coordString, int $decimals = 6): string
    {
        // Split the string into latitude and longitude
        [$lat, $lng] = explode(',', $coordString);

        // Trim whitespace
        $lat = trim($lat);
        $lng = trim($lng);

        // Format to the specified number of decimals
        $lat = number_format((float)$lat, $decimals, '.', '');
        $lng = number_format((float)$lng, $decimals, '.', '');

        // Return formatted string
        return $lat . ', ' . $lng;
    }
}
