<?php

namespace CommerceGuys\Intl\Timezone;

use CommerceGuys\Intl\LocaleResolverTrait;
use CommerceGuys\Intl\Exception\UnknownTimezoneException;

/**
 * Manages countries based on JSON definitions.
 */
class TimezoneRepository implements TimezoneRepositoryInterface
{
    use LocaleResolverTrait;

    /**
     * Base country definitions.
     *
     * Contains data common to all locales, such as the country numeric,
     * three-letter, currency codes.
     *
     * @var array
     */
    protected $baseDefinitions = [];

    /**
     * Per-locale country definitions.
     *
     * @var array
     */
    protected $definitions = [];

    /**
     * Creates a TimezoneRepository instance.
     *
     * @param string $definitionPath The path to the country definitions.
     *                               Defaults to 'resources/country'.
     */
    public function __construct($definitionPath = null)
    {
        $this->definitionPath = $definitionPath ? $definitionPath : __DIR__ . '/../../resources/timezone/';
    }

    /**
     * {@inheritdoc}
     */
    public function get($timezone, $locale = null, $fallbackLocale = null)
    {
        $locale = $this->resolveLocale($locale, $fallbackLocale);
        $definitions = $this->loadDefinitions($locale);
        if (!isset($definitions[$timezone])) {
            throw new UnknownTimezoneException($timezone);
        }

        return $this->createTimezoneFromDefinition($timezone, $definitions[$timezone], $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($locale = null, $fallbackLocale = null)
    {
        $locale = $this->resolveLocale($locale, $fallbackLocale);
        $definitions = $this->loadDefinitions($locale);
        $timezones = [];
        foreach ($definitions as $timezone => $definition) {
            $timezones[$timezone] = $this->createTimezoneFromDefinition($timezone, $definition, $locale);
        }

        return $timezones;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($locale = null, $fallbackLocale = null)
    {
        $locale = $this->resolveLocale($locale, $fallbackLocale);
        $definitions = $this->loadDefinitions($locale);
        $list = [];
        foreach ($definitions as $timezone => $definition) {
            $list[$timezone] = $definition['name'];
        }

        return $list;
    }

    /**
     * Loads the country definitions for the provided locale.
     *
     * @param string $locale The desired locale.
     *
     * @return array
     */
    protected function loadDefinitions($locale)
    {
        if (!isset($this->definitions[$locale])) {
            $filename = $this->definitionPath . $locale . '.json';
            $this->definitions[$locale] = json_decode(file_get_contents($filename), true);

            // Make sure the base definitions have been loaded.
            if (empty($this->baseDefinitions)) {
                $this->baseDefinitions = json_decode(file_get_contents($this->definitionPath . 'base.json'), true);
            }
            // Merge-in base definitions.
            foreach ($this->definitions[$locale] as $timezone => $definition) {
                $this->definitions[$locale][$timezone] += $this->baseDefinitions[$timezone];
            }
        }

        return $this->definitions[$locale];
    }

    /**
     * Creates a country object from the provided definition.
     *
     * @param string $timezone The country code.
     * @param array  $definition  The country definition.
     * @param string $locale      The locale of the country definition.
     *
     * @return Timezone
     */
    protected function createTimezoneFromDefinition($timezone, array $definition, $locale)
    {
        $tz = new Timezone();
        $setValues = \Closure::bind(function ($timezone, $definition, $locale) {
            $this->timezone = $timezone;
            $this->name = $definition['name'];
            $this->locale = $locale;
            $this->countryCode = $definition['country'];
        }, $tz, '\CommerceGuys\Intl\Timezone\Timezone');
        $setValues($timezone, $definition, $locale);

        return $tz;
    }
}
