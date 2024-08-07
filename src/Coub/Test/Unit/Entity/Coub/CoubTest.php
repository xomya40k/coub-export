<?php

declare(strict_types=1);

namespace App\Coub\Test\Unit\Entity\Coub;

use App\Coub\Entity\Coub\Coub;
use App\Coub\Entity\Coub\VideoCollection;
use App\Coub\Entity\Coub\AudioCollection;
use App\Coub\Test\Builder\VideoBuilder;
use App\Coub\Test\Builder\AudioBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Coub::class)]
final class CoubTest extends TestCase
{
    public function testSuccess(): void
    {
        $videoCollection = new VideoCollection([(new VideoBuilder())->build()]);
        $audioCollection = new AudioCollection([(new AudioBuilder())->build()]);

        $coub = new Coub($id = 1, $url = 'https://coub.com/coubid', $title = 'Coub', 
            $videoCollection, $audioCollection);
        
        $this->assertEquals($id, $coub->getId());
        $this->assertEquals($url, $coub->getUrl());
        $this->assertEquals($title, $coub->getTitle());
        $this->assertEquals($videoCollection, $coub->getVideo());
        $this->assertEquals($audioCollection, $coub->getAudio());
    }

    public function testIncorrectURL(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $videoCollection = new VideoCollection([(new VideoBuilder())->build()]);

        new Coub(1, 'not url', 'Coub', $videoCollection);
    }
}