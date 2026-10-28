
VERBOSE="false"; export $VERBOSE
echo "Teststart - Verbose-Mode: ${VERBOSE}";
echo
echo "##################### DatabaseTest ##########################"
./tools/phpunit.phar test/DatabaseTest.php

echo
echo "##################### MkKundeTest  ##########################"
./tools/phpunit.phar test/MkKundeTest.php

echo
echo "##################### MkAdresseTest  ##########################"
./tools/phpunit.phar test/MkAdresseTest.php

echo
echo "##################### MkTafelTest  ##########################"
./tools/phpunit.phar test/MkTafelTest.php

echo
echo "##################### MkKontaktTest  ##########################"
./tools/phpunit.phar test/MkKontaktTest.php

echo
echo "##################### MkPersonTest  ##########################"
./tools/phpunit.phar test/MkPersonTest.php

echo
echo "##################### MkKontakteTest  ##########################"
./tools/phpunit.phar test/MkKontakteTest.php

echo "#############################################################"
echo

