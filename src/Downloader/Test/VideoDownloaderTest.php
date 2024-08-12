<?php

declare(strict_types=1);

namespace App\Downloader\Test;

use App\Downloader\VideoDownloader;
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
#[CoversClass(VideoDownloader::class)]
final class VideoDownloaderTest extends TestCase
{
    function testSuccess() : void
    {   
        $savePath = 'savePath';
        $fileName = 'video_file.mp4';
        $ffmpegMock = $this->createMock(FFMpeg::class);
        $videoMock = $this->createMock(Video::class);
        $tempDirectoryMock = $this->createMock(TemporaryDirectory::class);
        $ffmpegMock->method('open')
            ->willReturn($videoMock);
        $videoMock->method('getTemporaryDirectory')
            ->willReturn($tempDirectoryMock);
        $tempDirectoryMock->method('create')
            ->willReturn($tempDirectoryMock);
        $tempDirectoryMock->method('path')
            ->willReturn($savePath);

        $videoDownloader = new VideoDownloader($ffmpegMock);
        $this->assertEquals($savePath . '\\' . $fileName, $videoDownloader->download('http://host/' . $fileName));
    }

    function testInvalidUrl() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');

        $ffmpegMock = $this->createMock(FFMpeg::class);

        (new VideoDownloader($ffmpegMock))->download('not url');
    }

    function testInvalidFile() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported file format');

        $ffmpegMock = $this->createMock(FFMpeg::class);
        $ffmpegMock->method('open')
            ->will($this->throwException(new InvalidArgumentException));

        (new VideoDownloader($ffmpegMock))->download('http://host/text_file.txt');
    }

    function testUnexpectedAudioFile() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected video file, got audio');

        $ffmpegMock = $this->createMock(FFMpeg::class);
        $audioMock = $this->createMock(Audio::class);
        $ffmpegMock->method('open')
            ->willReturn($audioMock);

        (new VideoDownloader($ffmpegMock))->download('http://host/audio_file.mp3');
    }
}