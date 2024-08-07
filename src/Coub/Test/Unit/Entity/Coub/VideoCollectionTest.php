<?php

declare(strict_types=1);

namespace App\Coub\Test\Unit\Entity\Coub;

use App\Coub\Entity\Coub\VideoCollection;
use App\Coub\Test\Builder\VideoBuilder;
use stdClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(VideoCollection::class)]
final class VideoCollectionTest extends TestCase
{
    public function testSuccess(): void
    {
        $videoHighQuality = (new VideoBuilder())
            ->withQuality(VideoBuilder::QUALITY_HIGH)
                ->build();
        $videoMediumQuality = (new VideoBuilder())
            ->withQuality(VideoBuilder::QUALITY_MEDIUM)
                ->build();
        
        $videoCollection = new VideoCollection([$videoHighQuality, $videoMediumQuality]);

        self::assertSame($videoHighQuality, $videoCollection->getHighQuality());
        self::assertSame($videoMediumQuality, $videoCollection->getMediumQuality());
    }

    public function testIncorrect() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $incorrectObject = new stdClass();
        $videoMediumQuality = (new VideoBuilder())
            ->withQuality(VideoBuilder::QUALITY_MEDIUM)
                ->build();
        
        new VideoCollection([$incorrectObject, $videoMediumQuality]);
    }
}