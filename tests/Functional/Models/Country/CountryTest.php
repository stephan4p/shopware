<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Functional\Models\Country;

use Enlight_Components_Test_Controller_TestCase;
use Shopware\Models\Country\Country;

class CountryTest extends Enlight_Components_Test_Controller_TestCase
{
    public function setUp()
    {
        Shopware()->Models()->getConnection()->beginTransaction();
        parent::setUp();
    }

    public function tearDown()
    {
        Shopware()->Models()->getConnection()->rollBack();
        parent::tearDown();
    }

    public function testNewCountryIsShippableByDefaultUsingDbal()
    {
        $connection = Shopware()->Models()->getConnection();

        $statement = $connection->prepare('INSERT INTO `s_core_countries` (countryname, countryiso, active, iso3) VALUES (:name, :iso, :active, :iso3)');
        $statement->execute([
            'name' => 'Test Country',
            'iso' => 'TS',
            'iso3' => 'TST',
            'active' => 1,
            // allow_shipping not set intentionally
        ]);

        $id = $connection->lastInsertId();

        $statement = $connection->prepare('SELECT * FROM `s_core_countries` WHERE id = :id');
        $statement->execute([
            'id' => $id,
        ]);

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertArraySubset([
            'id' => $id,
            'countryname' => 'Test Country',
            'countryiso' => 'TS',
            'iso3' => 'TST',
            'active' => 1,
            'allow_shipping' => 1, // Making sure the default value for the column is true
        ], $result[0]);
    }

    public function testNewCountryIsShippableByDefaultUsingDoctrine()
    {
        $modelManager = Shopware()->Models();

        $testCountry = new Country();
        $testCountry->setActive(true)
            ->setIso('TS')
            ->setIso3('TST')
            ->setName('Test Country');
        // allowShipping not set intentionally

        $modelManager->persist($testCountry);
        $modelManager->flush($testCountry);
        unset($testCountry);

        $id = $modelManager->getConnection()->lastInsertId();

        $testCountry = $modelManager->getRepository(Country::class)->find($id);

        $this->assertEquals(true, $testCountry->getActive());
        $this->assertEquals(true, $testCountry->getAllowShipping()); // Checking that allowShipping is true
        $this->assertEquals('TS', $testCountry->getIso());
        $this->assertEquals('TST', $testCountry->getIso3());
    }
}
