<?php

/**
 * Class RootLocator_Errors
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Errors
{
    /**
     * Return error for key
     *
     * @param string $key
     *
     * @return string
     */
    public static function get($key)
    {
        switch ($key) {
            case 'no_results':
                return self::noResults();
            case 'quota_exceeded':
                return self::quotaExceeded();
            case 'http_error':
                return self::httpError();
            case 'invalid_credentials':
                return self::invalidCredentials();
            case 'unknown':
            default:
                return self::unknown();
        }
    }

    /**
     * Results not found
     *
     * @return string
     */
    public static function noResults()
    {
        return PerchLang::get('No results found for address');
    }

    /**
     * API quote has been exceeded
     *
     * @return string
     */
    public static function quotaExceeded()
    {
        return PerchLang::get('API quota limits exceeded');
    }

    /**
     * CURL unable to fetch API response
     *
     * @return string
     */
    public static function httpError()
    {
        return PerchLang::get('HTTP request could not be completed.');
    }

    /**
     * API authorisation denied
     *
     * @return string
     */
    public static function invalidCredentials()
    {
        return PerchLang::get('API credentials invalid');
    }

    /**
     * Fallback 'you done goofed' message
     *
     * @return string
     */
    public static function unknown()
    {
        return PerchLang::get('unknown error');
    }
}
