#!/bin/sh

# URLs and repositories to download.
CURRENCY_URL="http://www.currency-iso.org/dam/downloads/lists/list_one.xml"
TIMEZONE_URL="https://timezonedb.com/files/timezonedb.csv.zip"
REPOSITORIES=(
    "https://github.com/unicode-cldr/cldr-core.git"
    "https://github.com/unicode-cldr/cldr-numbers-full.git"
    "https://github.com/unicode-cldr/cldr-localenames-full.git"
    "https://github.com/unicode-cldr/cldr-dates-full.git"
)

# Check we have the necessary commands.
if command -v wget >/dev/null 2>&1; then
    HAS_WGET=1
else
    HAS_WGET=0
fi

if command -v curl >/dev/null 2>&1; then
    HAS_CURL=1
else
    HAS_CURL=0
fi

if command -v git >/dev/null 2>&1; then
    HAS_GIT=1
else
    HAS_GIT=0
fi

if command -v unzip >/dev/null 2>&1; then
    HAS_UNZIP=1
else
    HAS_UNZIP=0
fi

if [ ${HAS_WGET} -eq 0 ] && [ ${HAS_CURL} -eq 0 ] ; then
    echo "I require wget or curl but neither are installed.  Aborting."
    exit 1
fi

if [ ${HAS_UNZIP} -eq 0 ] ; then
    echo "I require unzip but it is not installed.  Aborting."
    exit 1
fi

if [ ${HAS_GIT} -eq 0 ] ; then
    echo "I require git but it is not installed.  Aborting."
    exit 1
fi

# Recreate the assets folder and move into it.
rm -fR assets
mkdir assets
cd assets

for repo in "${REPOSITORIES[@]}"
do
   git clone ${repo}
done

if [ ${HAS_CURL} -eq 1 ]
then
    curl $CURRENCY_URL > c2.xml
    curl $TIMEZONE_URL > t.zip
else
    wget $CURRENCY_URL -O c2.xml
    wget $TIMEZONE_URL -O t.zip
fi

unzip t.zip
