<?php

declare(strict_types=1);

namespace App\Downloader\Test;

use App\Downloader\AudioDownloader;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use InvalidArgumentException;
use RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AudioDownloader::class)]
final class AudioDownloaderTest extends TestCase
{
    function testSuccess() : void
    {
        $ffmpegMock = $this->createMock(FFMpeg::class);
        $audioMock = $this->createMock(Audio::class);
        $ffmpegMock->method('open')
            ->willReturn($audioMock);

        $audioDownloader = new AudioDownloader($ffmpegMock);
        $this->assertEquals($audioMock, $audioDownloader->download('http://host/audio_file.mp3'));
    }

    function testInvalidUrl() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');

        $ffmpegMock = $this->createMock(FFMpeg::class);

        (new AudioDownloader($ffmpegMock))->download('not url');
    }

    function testInvalidFile() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported file format');

        $ffmpegMock = $this->createMock(FFMpeg::class);
        $ffmpegMock->method('open')
            ->will($this->throwException(new InvalidArgumentException));

        (new AudioDownloader($ffmpegMock))->download('http://host/text_file.txt');
    }

    function testUnexpectedVideoFile() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected audio file, got video');

        $ffmpegMock = $this->createMock(FFMpeg::class);
        $videoMock = $this->createMock(Video::class);
        $ffmpegMock->method('open')
            ->willReturn($videoMock);

        (new AudioDownloader($ffmpegMock))->download('http://host/video_file.mp4');
    }
}