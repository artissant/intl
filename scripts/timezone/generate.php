<?php

/**
 * Generates the json files stored in resources/timezone.
 *
 * CLDR lists about 515 languages, many of them dead (like Latin or Old English).
 * In order to decrease the list to a reasonable size, only the languages
 * for which CLDR itself has translations are listed.
 */
set_time_limit(0);

// Downloaded from https://timezonedb.com/files/timezonedb.csv.zip
$timezoneCountriesFile = '../assets/zone.csv';
$timezoneCountries = [];

if (!file_exists($timezoneCountriesFile)) {
    die("The $timezoneCountriesFile file was not found");
}

if (($handle = fopen($timezoneCountriesFile, 'r')) !== false) {
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        $timezoneCountries[$data[2]] = $data[1];
    }

    fclose($handle);
}

// Downloaded from https://github.com/unicode-cldr/cldr-localenames-full.git
$localeDirectory = '../assets/cldr-dates-full/main/';
$enTimezones = $localeDirectory . 'en/timeZoneNames.json';

if (!is_dir($localeDirectory)) {
    die("The $localeDirectory directory was not found");
}
if (!file_exists($enTimezones)) {
    die("The $enTimezones file was not found");
}

// Locales listed without a "-" match all variants.
// Locales listed with a "-" match only those exact ones.
$ignoredLocales = [
    // Interlingua is a made up language.
    'ia',
    // Valencian differs from its parent only by a single character (è/é).
    'ca-ES-VALENCIA',
    // Special "grouping" locales.
    'root', 'en-US-POSIX', 'en-001', 'en-150', 'es-419',
];

function base_timezone_data(array $data = []) {
    static $timezones = [];

    return $timezones = array_merge($timezones, $data);
}

function flatten_timezone_data($timezone, array $timezoneCountries, $parentRegion = null) {
    $ignoredCountries = [
        'AN', // Netherlands Antilles, no longer exists.
        'BV', 'HM', 'CP', // Uninhabited islands.
        'EU', 'QO', // European Union, Outlying Oceania. Not countries.
        'ZZ', // Unknown region
    ];

    $flattened = [];

    $flattenRegion = function ($region) use ($parentRegion) {
        return isset($parentRegion) ? "{$parentRegion}/{$region}" : $region;
    };

    $findClosestMatch = function ($region) {
        $parts = explode('/', $region);
        $total = count($parts);

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $matches = 0;

            foreach ($parts as $part) {
                if (strpos($timezone, $part) !== false) {
                    $matches++;
                }
            }

            if ($matches === $total) {
                return $timezone;
            }
        }

        return $region;
    };

    if ( ! is_array($timezone)) {
        return [];
    }

    foreach ($timezone as $region => $data) {
        $flattenedRegion = $flattenRegion($region);

        if (isset($data['exemplarCity'])) {
            if ( ! isset($timezoneCountries[$flattenedRegion])) {
                $closestMatch = $findClosestMatch($flattenedRegion);

                if ($closestMatch === $flattenedRegion) {
                    continue;
                }

                $flattenedRegion = $closestMatch;
            }

            if (empty($timezoneCountries[$flattenedRegion]) or in_array($timezoneCountries[$flattenedRegion], $ignoredCountries)) {
                continue;
            }

            base_timezone_data([
                $flattenedRegion => [
                    'id' => $flattenedRegion,
                    'country' => $timezoneCountries[$flattenedRegion],
                ]
            ]);

            $flattened[$flattenedRegion] = [
                'name' => $data['exemplarCity'],
            ];
        } else {
            $flattened = array_merge($flattened, flatten_timezone_data($data, $timezoneCountries, $flattenedRegion));
        }
    }

    return $flattened;
}

$timezones = [];
// Load the "en" data first.
$timezoneData = json_decode(file_get_contents($enTimezones), true);
$timezones['en'] = flatten_timezone_data(
    $timezoneData['main']['en']['dates']['timeZoneNames']['zone'], $timezoneCountries
);

// Gather available locales.
$locales = [];
if ($handle = opendir($localeDirectory)) {
    while (false !== ($entry = readdir($handle))) {
        if (substr($entry, 0, 1) != '.') {
            $entryParts = explode('-', $entry);
            if (!in_array($entry, $ignoredLocales) && !in_array($entryParts[0], $ignoredLocales)) {
                $locales[] = $entry;
            }
        }
    }
    closedir($handle);
}

// Write out base.json.
$baseData = base_timezone_data();
ksort($baseData);
file_put_json('base.json', $baseData);

// Load the localizations.
$untranslatedCounts = [];
foreach ($locales as $locale) {
    $data = json_decode(file_get_contents($localeDirectory . $locale . '/timeZoneNames.json'), true);
    $data = flatten_timezone_data(
        $data['main'][$locale]['dates']['timeZoneNames']['zone'], $timezoneCountries
    );

    foreach ($data as $timezone => $info) {
        if (isset($timezones['en'][$timezone])) {
            if ($locale !== 'en' && $info['name'] === $timezones['en'][$timezone]['name']) {
                // Maintain a count of untranslated timezones per locale.
                $untranslatedCounts += [$locale => 0];
                $untranslatedCounts[$locale]++;
            }

            $timezones[$locale][$timezone] = $info;
        }
    }
}

// Ignore locales that are more than 80% untranslated.
foreach ($untranslatedCounts as $locale => $count) {
    $totalCount = count($timezones[$locale]);
    $untranslatedPercentage = $count * (100 / $totalCount);
    if ($untranslatedPercentage >= 80) {
        unset($timezones[$locale]);
    }
}

// Identify localizations that are the same as the ones for the parent locale.
// For example, "fr-FR" if "fr" has the same data.
$duplicates = [];
foreach ($timezones as $locale => $localizedTimezones) {
    if (strpos($locale, '-') !== false) {
        $localeParts = explode('-', $locale);
        array_pop($localeParts);
        $parentLocale = implode('-', $localeParts);

        if ( ! isset($timezones[$parentLocale])) {
            continue;
        }

        $diff = array_udiff($localizedTimezones, $timezones[$parentLocale], function ($first, $second) {
            return ($first['name'] == $second['name']) ? 0 : 1;
        });

        if (empty($diff)) {
            // The duplicates are not removed right away because they might
            // still be needed for other duplicate checks (for example,
            // when there are locales like bs-Latn-BA, bs-Latn, bs).
            $duplicates[] = $locale;
        }
    }
}
// Remove the duplicates.
foreach ($duplicates as $locale) {
    unset($timezones[$locale]);
}

// Write out the localizations.
foreach ($timezones as $locale => $localizedTimezones) {
    ksort($localizedTimezones);

    file_put_json($locale . '.json', $localizedTimezones);
}

/**
 * Converts the provided data into json and writes it to the disk.
 */
function file_put_json($filename, $data)
{
    $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // Indenting with tabs instead of 4 spaces gives us 20% smaller files.
    $data = str_replace('    ', "\t", $data);
    file_put_contents($filename, $data);
}
