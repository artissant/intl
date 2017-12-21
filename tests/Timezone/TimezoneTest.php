<?php

namespace CommerceGuys\Intl\Tests\Timezone;

use CommerceGuys\Intl\Timezone\Timezone;

/**
 * @coversDefaultClass \CommerceGuys\Intl\Timezone\Timezone
 */
class TimezoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Timezone
     */
    protected $timezone;

    public function setUp()
    {
        $this->timezone = new Timezone();
    }

    /**
     * @covers ::getTimezoneCode
     * @covers ::setTimezoneCode
     * @covers ::__toString
     */
    public function testTimezone()
    {
        $this->timezone->setTimezone('Europe/London');
        $this->assertEquals('Europe/London', $this->timezone->getTimezone());
        $this->assertEquals('Europe/London', (string) $this->timezone);
    }

    /**
     * @covers ::getName
     * @covers ::setName
     */
    public function testName()
    {
        $this->timezone->setName('London');
        $this->assertEquals('London', $this->timezone->getName());
    }


    /**
     * @covers ::getCurrencyCode
     * @covers ::setCurrencyCode
     */
    public function testCountry()
    {
        $this->timezone->setCountry('GB');
        $this->assertEquals('GB', $this->timezone->getCountry());
    }

    /**
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testLocale()
    {
        $this->timezone->setLocale('en');
        $this->assertEquals('en', $this->timezone->getLocale());
    }
}
