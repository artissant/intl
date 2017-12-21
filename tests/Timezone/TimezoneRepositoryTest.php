<?php

namespace CommerceGuys\Intl\Tests\Timezone;

use CommerceGuys\Intl\Timezone\TimezoneRepository;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \CommerceGuys\Intl\Timezone\TimezoneRepository
 */
class TimezoneRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base country definitions.
     *
     * @var array
     */
    protected $baseDefinitions = [
        'Europe/London' => [
            'id' => 'Europe/London',
            'country' => 'GB',
        ],
        'Europe/Lisbon' => [
            'id' => 'Europe/Lisbon',
            'country' => 'PT',
        ],
    ];

    /**
     * English country definitions.
     *
     * @var array
     */
    protected $englishDefinitions = [
        'Europe/London' => [
            'name' => 'London',
        ],
        'Europe/Lisbon' => [
            'name' => 'Lisbon',
        ],
    ];

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        // Mock the existence of JSON definitions on the filesystem.
        $root = vfsStream::setup('resources');
        vfsStream::newFile('timezone/base.json')->at($root)->setContent(json_encode($this->baseDefinitions));
        vfsStream::newFile('timezone/en.json')->at($root)->setContent(json_encode($this->englishDefinitions));

        // Instantiate the timezone repository and confirm that the definition path
        // was properly set.
        $timezoneRepository = new TimezoneRepository('vfs://resources/timezone/');
        $definitionPath = $this->getObjectAttribute($timezoneRepository, 'definitionPath');
        $this->assertEquals('vfs://resources/timezone/', $definitionPath);

        return $timezoneRepository;
    }

    /**
     * @covers ::get
     * @covers ::loadDefinitions
     * @covers ::createTimezoneFromDefinition
     *
     * @uses \CommerceGuys\Intl\Timezone\Timezone
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @depends testConstructor
     */
    public function testGet($timezoneRepository)
    {
        $timezone = $timezoneRepository->get('Europe/London');
        $this->assertInstanceOf('CommerceGuys\\Intl\\Timezone\\Timezone', $timezone);
        $this->assertEquals('Europe/London', $timezone->getTimezone());
        $this->assertEquals('London', $timezone->getName());
        $this->assertEquals('GB', $timezone->getCountry());
        $this->assertEquals('en', $timezone->getLocale());
    }

    /**
     * @covers ::get
     * @covers ::loadDefinitions
     *
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @expectedException \CommerceGuys\Intl\Exception\UnknownTimezoneException
     * @depends testConstructor
     */
    public function testGetInvalidTimezone($timezoneRepository)
    {
        $timezoneRepository->get('Europe/Birmingham');
    }

    /**
     * @covers ::getAll
     * @covers ::loadDefinitions
     * @covers ::createTimezoneFromDefinition
     *
     * @uses \CommerceGuys\Intl\Timezone\Timezone
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @depends testConstructor
     */
    public function testGetAll($timezoneRepository)
    {
        $timezones = $timezoneRepository->getAll();
        $this->assertArrayHasKey('Europe/London', $timezones);
        $this->assertArrayHasKey('Europe/Lisbon', $timezones);
        $this->assertEquals('Europe/London', $timezones['Europe/London']->getTimezone());
        $this->assertEquals('Europe/Lisbon', $timezones['Europe/Lisbon']->getTimezone());
    }

    /**
     * @covers ::getList
     * @covers ::loadDefinitions
     *
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @depends testConstructor
     */
    public function testGetList($timezoneRepository)
    {
        $list = $timezoneRepository->getList();
        $expectedList = ['Europe/London' => 'London', 'Europe/Lisbon' => 'Lisbon'];
        $this->assertEquals($expectedList, $list);
    }
}
