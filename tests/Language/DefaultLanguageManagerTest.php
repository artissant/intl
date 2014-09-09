<?php

namespace CommerceGuys\Intl\Tests\Language;

use CommerceGuys\Intl\Language\DefaultLanguageManager;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \CommerceGuys\Intl\Language\DefaultLanguageManager
 */
class DefaultLanguageManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * English language definitions.
     *
     * @var array
     */
    protected $englishDefinitions = array(
        'en' => array(
            'code' => 'en',
            'name' => 'English',
        ),
        'fr' => array(
            'code' => 'fr',
            'name' => 'French',
        ),
    );

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        // Mock the existence of JSON definitions on the filesystem.
        $root = vfsStream::setup('resources');
        vfsStream::newFile('language/en.json')->at($root)->setContent(json_encode($this->englishDefinitions));

        // Instantiate the language manager and confirm that the definition path
        // was properly set.
        $languageManager = new DefaultLanguageManager('vfs://resources/language/');
        $definitionPath = $this->getObjectAttribute($languageManager, 'definitionPath');
        $this->assertEquals($definitionPath, 'vfs://resources/language/');

        return $languageManager;
    }

    /**
     * @covers ::get
     * @covers ::loadDefinitions
     * @covers ::createLanguageFromDefinition
     * @uses \CommerceGuys\Intl\Language\Language::getLanguageCode
     * @uses \CommerceGuys\Intl\Language\Language::setLanguageCode
     * @uses \CommerceGuys\Intl\Language\Language::getName
     * @uses \CommerceGuys\Intl\Language\Language::setName
     * @uses \CommerceGuys\Intl\Language\Language::getLocale
     * @uses \CommerceGuys\Intl\Language\Language::setLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::resolveLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::getLocaleVariants
     * @depends testConstructor
     */
    public function testGet($languageManager)
    {
        $language = $languageManager->get('en');
        $this->assertInstanceOf('CommerceGuys\\Intl\\Language\\Language', $language);
        $this->assertEquals($language->getLanguageCode(), 'en');
        $this->assertEquals($language->getName(), 'English');
        $this->assertEquals($language->getLocale(), 'en');
    }

    /**
     * @covers ::get
     * @covers ::loadDefinitions
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::resolveLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::getLocaleVariants
     * @expectedException \CommerceGuys\Intl\Language\UnknownLanguageException
     * @depends testConstructor
     */
    public function testGetInvalidLanguage($languageManager)
    {
        $languageManager->get('de');
    }

    /**
     * @covers ::getAll
     * @covers ::loadDefinitions
     * @covers ::createLanguageFromDefinition
     * @uses \CommerceGuys\Intl\Language\Language::getLanguageCode
     * @uses \CommerceGuys\Intl\Language\Language::setLanguageCode
     * @uses \CommerceGuys\Intl\Language\Language::setName
     * @uses \CommerceGuys\Intl\Language\Language::setLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::resolveLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::getLocaleVariants
     * @depends testConstructor
     */
    public function testGetAll($languageManager)
    {
        $languages = $languageManager->getAll();
        $this->assertArrayHasKey('en', $languages);
        $this->assertArrayHasKey('fr', $languages);
        $this->assertEquals($languages['en']->getLanguageCode(), 'en');
        $this->assertEquals($languages['fr']->getLanguageCode(), 'fr');
    }
}
