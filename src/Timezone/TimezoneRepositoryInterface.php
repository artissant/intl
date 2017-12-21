<?php

namespace CommerceGuys\Intl\Timezone;

/**
 * Timezone repository interface.
 */
interface TimezoneRepositoryInterface
{
    /**
     * Returns a timezone instance matching the provided timezone ID.
     *
     * @param string $timezone       The timezone ID.
     * @param string $locale         The locale (i.e. fr-FR).
     * @param string $fallbackLocale A fallback locale (i.e "en").
     *
     * @return TimezoneInterface
     */
    public function get($timezone, $locale = null, $fallbackLocale = null);

    /**
     * Returns all timezone instances.
     *
     * @param string $locale         The locale (i.e. fr-FR).
     * @param string $fallbackLocale A fallback locale (i.e "en").
     *
     * @return array An array of timezones implementing the TimezoneInterface,
     *               keyed by timezone ID.
     */
    public function getAll($locale = null, $fallbackLocale = null);

    /**
     * Returns a list of timezones.
     *
     * @param string $locale         The locale (i.e. fr-FR).
     * @param string $fallbackLocale A fallback locale (i.e "en").
     *
     * @return array An array of timezone names, keyed by timezone ID.
     */
    public function getList($locale = null, $fallbackLocale = null);
}
