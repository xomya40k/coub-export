<?php

declare(strict_types=1);

namespace App\Downloader\Test;

use App\Downloader\AudioDownloader;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use Spatie\TemporaryDirectory\TemporaryDirectory;
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
        $savePath = 'savePath';
        $fileName = 'audio_file.mp3';
        $ffmpegMock = $this->createMock(FFMpeg::class);
        $audioMock = $this->createMock(Audio::class);
        $tempDirectoryMock = $this->createMock(TemporaryDirectory::class);
        $ffmpegMock->method('open')
            ->willReturn($audioMock);
        $audioMock->method('getTemporaryDirectory')
            ->willReturn($tempDirectoryMock);
        $tempDirectoryMock->method('create')
            ->willReturn($tempDirectoryMock);
        $tempDirectoryMock->method('path')
            ->willReturn($savePath);

        $audioDownloader = new AudioDownloader($ffmpegMock);
        $this->assertEquals($savePath . '\\' . $fileName, $audioDownloader->download('http://host/' . $fileName));
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