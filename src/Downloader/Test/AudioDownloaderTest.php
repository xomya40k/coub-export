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
    private $ffmpegMock;
    private $videoMock;
    private $audioMock;
    private $temporaryDirectoryMock;

    protected function setUp() : void
    {
        $this->ffmpegMock = $this->createMock(FFMpeg::class);
        $this->videoMock = $this->createMock(Video::class);
        $this->audioMock = $this->createMock(Audio::class);
        $this->temporaryDirectoryMock = $this->createMock(TemporaryDirectory::class);
        
        $this->ffmpegMock->method('open')
            ->willReturnCallback(function ($filePath) {
                if (pathinfo($filePath, PATHINFO_EXTENSION) === 'mp4') {
                    return $this->videoMock;
                } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'mp3') {
                    return $this->audioMock;
                } else {
                    throw new InvalidArgumentException();
                }
            }
        );

        $this->audioMock->method('getTemporaryDirectory')
            ->willReturn($this->temporaryDirectoryMock);
        
        $this->temporaryDirectoryMock->method('create')
            ->willReturnSelf();
        $this->temporaryDirectoryMock->method('path')
            ->willReturn('savePath');
    }

    public function testSuccess() : void
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
        $this->assertEquals($savePath . DIRECTORY_SEPARATOR . $fileName, 
            $audioDownloader->download('http://host/' . $fileName));
    }

    public function testInvalidUrl() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');

        (new AudioDownloader($this->ffmpegMock))->download('not url');
    }

    public function testInvalidFile() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported file format');

        (new AudioDownloader($this->ffmpegMock))->download('http://host/text_file.txt');
    }

    public function testUnexpectedVideoFile() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected audio file, got video');

        (new AudioDownloader($this->ffmpegMock))->download('http://host/video_file.mp4');
    }
}