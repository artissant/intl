<?php

namespace CommerceGuys\Intl\Timezone;

class Timezone implements TimezoneEntityInterface
{
    /**
     * The timezone ID.
     *
     * @var string
     */
    protected $timezone;

    /**
     * The timezone name.
     *
     * @var string
     */
    protected $name;

    /**
     * The two-letter country code.
     *
     * @var string
     */
    protected $countryCode;

    /**
     * The country locale (i.e. "en_US").
     *
     * The country name is locale specific.
     *
     * @var string
     */
    protected $locale;

    /**
     * Returns the string representation of the Timezone.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTimezone();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry($twoLetterCode)
    {
        $this->countryCode = $twoLetterCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
