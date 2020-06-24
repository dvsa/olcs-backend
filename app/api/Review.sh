#!/bin/bash

PHPCS_SEVERITY=1
BASE_BRANCH="origin/master"
while getopts "hib:" opt; do
  case $opt in
    h)
        echo;
        echo Run a few checks on changed code before commiting
        echo;
        echo "  -h          Show help (this)"
        echo "  -i          Ignore new DocBlock coding standards"
        echo "  -b <branch> Base branch for comparing"
        echo;
        exit;
      ;;
    i)
        PHPCS_SEVERITY=5
      ;;
    b)
        BASE_BRANCH=$OPTARG
      ;;
  esac
done

PHPCS="vendor/bin/phpcs"
PHPUNIT="vendor/bin/phpunit"
PHPCOV="vendor/bin/phpcov"
PHPMD="vendor/bin/phpmd"

# this parses the 'project name' from the git remote url
PROJECT=$(git remote -v | head -n1 | awk '{print $2}' | sed 's/.*\///' | sed 's/\.git//');

REVISION=$(git rev-parse --short HEAD)
BRANCH=$(git rev-parse --abbrev-ref HEAD)
NOW=$(date)

echo "{panel:title=$PROJECT|borderStyle=solid|borderColor=#000|titleBGColor=#75e069|bgColor=#efefef}"

echo "||h6. GIT revision||h6. GIT branch||h6. Time||"./com
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
			 $PHPCS --severity=$PHPCS_SEVERITY --standard=vendor/olcs/coding-standards/Profiles/DVSA/CS/ruleset.xml $file
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
			 $PHPMD $file "Devtools\CustomTextRenderer" "vendor/olcs/coding-standards/Profiles/DVSA/PMD/ruleset.xml"
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
