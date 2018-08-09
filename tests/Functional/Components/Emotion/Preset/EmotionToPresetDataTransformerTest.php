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

namespace Shopware\tests\Functional\Components\Emotion\Preset;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\TestCase;
use Shopware\Components\Api\Manager;
use Shopware\Components\Api\Resource\EmotionPreset;
use Shopware\Components\Emotion\Preset\EmotionToPresetDataTransformer;

/**
 * @group EmotionPreset
 */
class EmotionToPresetDataTransformerTest extends TestCase
{
    /** @var EmotionToPresetDataTransformer */
    private $transformer;

    /** @var EmotionPreset */
    private $presetResource;

    /** @var Connection */
    private $connection;

    protected function setUp()
    {
        $this->connection = Shopware()->Container()->get('dbal_connection');
        $this->connection->beginTransaction();

        $this->connection->executeQuery('DELETE FROM s_emotion_presets');
        $this->connection->executeQuery('DELETE FROM s_core_plugins');

        $this->transformer = Shopware()->Container()->get('shopware.emotion.emotion_presetdata_transformer');
        $this->presetResource = Manager::getResource('EmotionPreset');
    }

    protected function tearDown()
    {
        $this->connection->rollBack();
    }

    /**
     * @expectedException
     */
    public function testShouldFailBecauseOfMissingEmotion()
    {
        $this->expectException(NoResultException::class);
        $this->transformer->transform(null);
    }

    public function testTransformShouldSucceed()
    {
        $emotionId = $this->connection->executeQuery('SELECT id FROM s_emotion LIMIT 1')->fetchColumn();

        $data = $this->transformer->transform($emotionId);

        $this->assertArrayHasKey('presetData', $data);
        $this->assertJson($data['presetData']);

        $presetData = json_decode($data['presetData'], true);

        $this->assertArrayNotHasKey('id', $presetData);

        $this->assertArrayNotHasKey('id', $presetData['elements'][0]);
        $this->assertInternalType('string', $presetData['elements'][0]['componentId']);
        $this->assertArrayHasKey('syncKey', $presetData['elements'][0]);

        $this->assertArrayHasKey('data', $presetData['elements'][0]);

        $this->assertInternalType('array', $data['requiredPlugins']);
    }

    public function testTransformWithTranslationsShouldSucceed()
    {
        $emotionId = $this->connection->executeQuery('SELECT id FROM s_emotion LIMIT 1')->fetchColumn();

        $translation = serialize([]);
        $this->connection->insert('s_core_translations', ['objecttype' => 'emotion', 'objectdata' => 'a:1:{s:4:"name";s:11:"My homepage";}', 'objectkey' => $emotionId, 'objectlanguage' => 2, 'dirty' => 1]);

        $data = $this->transformer->transform($emotionId);

        $this->assertArrayHasKey('emotionTranslations', $data);
        $this->assertJson($data['emotionTranslations']);

        $translationData = json_decode($data['emotionTranslations'], true);

        $this->assertSame('en_GB', $translationData[0]['locale']);
        $this->assertSame('emotion', $translationData[0]['objecttype']);

        $this->assertArrayHasKey('presetData', $data);
        $this->assertJson($data['presetData']);
        $this->assertArrayHasKey('requiredPlugins', $data);
        $this->assertEmpty($data['requiredPlugins']);
    }

    public function testGettingRequiredPluginsByIdShouldSucceed()
    {
        $this->connection->insert('s_core_plugins', ['name' => 'SwagLiveShopping', 'label' => 'Live shopping', 'version' => '1.0.0']);
        $pluginId = $this->connection->fetchColumn('SELECT id FROM s_core_plugins');

        $method = new \ReflectionMethod($this->transformer, 'getRequiredPluginsById');
        $method->setAccessible(true);

        $ids = [$pluginId];

        $result = $method->invoke($this->transformer, $ids);

        $this->assertInternalType('array', $result);
        $this->assertEquals('SwagLiveShopping', $result[0]['name']);
    }
}
