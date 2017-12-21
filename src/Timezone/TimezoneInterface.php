<?php

namespace CommerceGuys\Intl\Timezone;

interface TimezoneInterface
{
    /**
     * Gets the timezone ID.
     *
     * @return string
     */
    public function getTimezone();

    /**
     * Gets the timezone name.
     *
     * Note that certain locales have incomplete translations, in which
     * case the english version of the timezone name is used instead.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the timezone two-letter country code.
     *
     * @return string
     */
    public function getCountry();
}
