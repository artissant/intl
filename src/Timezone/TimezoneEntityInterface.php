<?php

namespace CommerceGuys\Intl\Timezone;

interface TimezoneEntityInterface extends TimezoneInterface
{
    /**
     * Sets the timezone ID.
     *
     * @param string $timezone The two-letter country code.
     *
     * @return self
     */
    public function setTimezone($timezone);

    /**
     * Sets the country name.
     *
     * @param string $name The country name.
     *
     * @return self
     */
    public function setName($name);

    /**
     * Sets the two-letter country code.
     *
     * @param string $twoLetterCode The two-letter country code.
     *
     * @return self
     */
    public function setCountry($twoLetterCode);
}
