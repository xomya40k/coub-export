<?php

declare(strict_types=1);

namespace App\Coub\Test\Unit\Entity\Coub;

use App\Coub\Entity\Coub\Video;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Video::class)]
final class VideoTest extends TestCase
{
    public function testSuccess(): void
    {
        $url = 'https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4';
        $quality = Video::QUALITY_HIGH;
        $video = new Video($url, $quality);

        self::assertSame($url, $video->getUrl());
        self::assertSame($quality, $video->getQuality());
    }

    public function testHighQuality(): void
    {
        $video = new Video('https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4',
            Video::QUALITY_HIGH);

        self::assertTrue($video->isQualityHigh());
        self::assertFalse($video->isQualityMedium());
    }

    public function testMediumQuality(): void
    {
        $video = new Video('https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4',
            Video::QUALITY_MEDIUM);

        self::assertTrue($video->isQualityMedium());
        self::assertFalse($video->isQualityHigh());
    }

    public function testIncorrectQuality(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Video('https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4',
            2);
    }
    
    public function testIncorrectURL(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Video('not url', Video::QUALITY_HIGH);
    }

    public function testIncorrectWidth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Video('https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4',
            Video::QUALITY_HIGH);
    }

    public function testIncorrectHeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Video('https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4',
            Video::QUALITY_HIGH);
    }
}