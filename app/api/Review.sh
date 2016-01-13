#!/bin/bash

BASE_BRANCH=${1-"origin/develop"}
WORKSPACE=${dev_workspace}
if [ -d "${WORKSPACE}/olcs-devtools" ]; then
    DIR="${WORKSPACE}/olcs-devtools"
else
    WORKSPACE="~/git"
    if [ -d "${WORKSPACE}/olcs-devtools" ]; then
        DIR="${WORKSPACE}/olcs-devtools"
    else
	WORKSPACE="../"
	if [ -d "${WORKSPACE}/olcs-devtools" ]; then
            DIR="${WORKSPACE}/olcs-devtools"
        else
	    echo "Unable to find workspace, please set env variable for dev_workspace"
	    exit 1;
       fi
   fi
fi

if [ ! -d "${DIR}/vendor" ]; then
    if [ ! -f "${DIR}/composer.phar" ]; then
        wget https://getcomposer.org/composer.phar -O "${DIR}/composer.phar"
    fi
    (cd "${DIR}" && php "composer.phar" update)
fi

PHPCS="${DIR}/vendor/bin/phpcs"
PHPUNIT="vendor/bin/phpunit"
PHPCOV="${DIR}/vendor/bin/phpcov"
PHPMD="${DIR}/vendor/bin/phpmd"
DEVTOOLS="${DIR}"

# this parses the 'project name' from the git remote url
PROJECT=$(git remote -v | head -n1 | awk '{print $2}' | sed 's/.*\///' | sed 's/\.git//');

REVISION=$(git rev-parse --short HEAD)
BRANCH=$(git rev-parse --abbrev-ref HEAD)
NOW=$(date)

echo "{panel:title=$PROJECT|borderStyle=solid|borderColor=#000|titleBGColor=#75e069|bgColor=#efefef}"

echo "||h6. GIT revision||h6. GIT branch||h6. Time||"
echo "|${REVISION}|${BRANCH}|${NOW}|"

echo "h2.Check PHP syntax"

echo "{code}"

for file in $(git diff $BASE_BRANCH --name-only);
do
	if [ -f $file ]
		then
		if [[ ${file: -4} == ".php" ]]
			then
			php -l $file;
		fi
	fi
done

echo "{code}"

echo "h2.Check Coding Standards"

echo "{code}"

for file in $(git diff $BASE_BRANCH --name-only);
do
	if [ -f $file ]
		then
		if [[ ${file: -4} == ".php" ]]
			then
			 $PHPCS --standard="${dev_workspace}/sonar-configuration/Profiles/DVSA/CS/ruleset.xml" $file
		fi
	fi
done

echo "{code}"

echo "h2.Detecting Mess"

echo "{code}"

for file in $(git diff $BASE_BRANCH --name-only);
do
	if [ -f $file ]
		then
		if [[ ${file: -4} == ".php" ]]
			then
			 $PHPMD $file "Devtools\CustomTextRenderer" "${dev_workspace}/sonar-configuration/Profiles/DVSA/PMD/ruleset.xml"
		fi
	fi
done

echo "{code}"

echo "h2.Run unit tests"

echo "{code}"

$PHPUNIT -c test/phpunit-review.xml

echo "{code}"

echo "h2.Checking coverage of diff"

echo "{code}"

git diff -w $BASE_BRANCH > test/review/patch.txt && $PHPCOV patch-coverage --patch test/review/patch.txt --path-prefix `pwd`/ test/review/coverage.cov

echo "{code}"

echo "{panel}"
