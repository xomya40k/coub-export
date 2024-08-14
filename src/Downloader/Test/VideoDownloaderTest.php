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

        $this->videoMock->method('getTemporaryDirectory')
            ->willReturn($this->temporaryDirectoryMock);
        
        $this->temporaryDirectoryMock->method('create')
            ->willReturnSelf();
        $this->temporaryDirectoryMock->method('path')
            ->willReturn('savePath');
    }

    public function testSuccess() : void
    {   
        $savePath = 'savePath';
        $fileName = 'video_file.mp4';
        
        $videoDownloader = new VideoDownloader($this->ffmpegMock);
        $this->assertEquals($savePath . DIRECTORY_SEPARATOR . $fileName, 
            $videoDownloader->download('http://host/' . $fileName));
    }

    public function testInvalidUrl() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');

        (new VideoDownloader($this->ffmpegMock))->download('not url');
    }

    public function testInvalidFile() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported file format');

        (new VideoDownloader($this->ffmpegMock))->download('http://host/text_file.txt');
    }

    public function testUnexpectedAudioFile() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected video file, got audio');

        (new VideoDownloader($this->ffmpegMock))->download('http://host/audio_file.mp3');
    }
}