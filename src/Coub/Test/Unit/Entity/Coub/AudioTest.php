<?php

declare(strict_types=1);

namespace App\Coub\Test\Unit\Entity\Coub;

use App\Coub\Entity\Coub\Audio;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Audio::class)]
final class AudioTest extends TestCase
{
    public function testSuccess(): void
    {
        $url = 'https://selcdn.net/coub_storage/coub/simple/cw_file/mp3-high.mp3';
        $sampleDuration = 9.358;
        $quality = Audio::QUALITY_HIGH;
        $video = new Audio($url, $sampleDuration, $quality);

        self::assertSame($url, $video->getUrl());
        self::assertSame($sampleDuration, $video->getDuration());
        self::assertSame($quality, $video->getQuality());
    }

    public function testHighQuality(): void
    {
        $video = new Audio('https://selcdn.net/coub_storage/coub/simple/cw_file/mp3-high.mp3',
            9.358, Audio::QUALITY_HIGH);

        self::assertTrue($video->isQualityHigh());
        self::assertFalse($video->isQualityMedium());
    }

    public function testMediumQuality(): void
    {
        $video = new Audio('https://selcdn.net/coub_storage/coub/simple/cw_file/mp3-high.mp3',
            9.358, Audio::QUALITY_MEDIUM);

        self::assertTrue($video->isQualityMedium());
        self::assertFalse($video->isQualityHigh());
    }

    public function testIncorrectQuality(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Audio('https://selcdn.net/coub_storage/coub/simple/cw_file/mp3-high.mp3',
            9.358, 2);
    }
    
    public function testIncorrectURL(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Audio('not url',
            9.358, Audio::QUALITY_HIGH);
    }

    public function testIncorrectSampleDuration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Audio('https://selcdn.net/coub_storage/coub/simple/cw_file/mp3-high.mp3',
            -5.735, Audio::QUALITY_HIGH);
    }
}