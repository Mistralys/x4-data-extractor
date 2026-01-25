<?php

declare(strict_types=1);

namespace Mistralys\X4\Tests\UnitTests\ExtractedData;

use Mistralys\X4\ExtractedData\X4GameInfo;
use Mistralys\X4\Tests\Classes\BaseTestCase;

class X4GameInfoTest extends BaseTestCase
{
    private string $assetsFolder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetsFolder = __DIR__ . '/../../assets';
    }

    public function test_loadInfo() : void
    {
        $info = X4GameInfo::create();
        $path = realpath($this->assetsFolder.'/game-info.json');

        $info->setInfoFilePath($path);

        $this->assertEquals('6.00', $info->getGameVersion());

        // Check date
        $date = $info->getDate();
        $this->assertEquals('2023-11-14 20:00:00', $date->format('Y-m-d H:i:s'));
    }

    public function test_folders() : void
    {
        $info = X4GameInfo::create();
        $info->setInfoFilePath($this->assetsFolder.'/game-info.json');

        $folders = $info->getFolderCollection();
        $all = $folders->getAll();

        $this->assertCount(2, $all);

        $vanilla = $folders->getByID('vanilla');
        $this->assertNotNull($vanilla);
        $this->assertEquals('Core Game', $vanilla->getLabel());
        $this->assertFalse($vanilla->isExtension());

        $dlc = $folders->getByID('ego_dlc_boron');
        $this->assertNotNull($dlc);
        $this->assertEquals('Kingdom End', $dlc->getLabel());
        $this->assertTrue($dlc->isExtension());
    }
}
