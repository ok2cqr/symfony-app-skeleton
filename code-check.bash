#!/bin/bash
vendor/bin/phpstan analyse -l 8 src tests
vendor/bin/phpcs --extensions=php --tab-width=4 -sp src tests -n
