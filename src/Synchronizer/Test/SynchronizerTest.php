<?php

declare(strict_types=1);

namespace App\Synchronizer;

use App\Synchronizer\Synchronizer;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Video;
use FFMpeg\Media\Audio;
use FFMpeg\Media\AdvancedMedia;
use FFMpeg\Media\Concat;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Filters\Audio\AudioFilters;
use FFMpeg\Filters\AdvancedMedia\ComplexFilters;
use FFMpeg\FFProbe\DataMapping\AbstractData;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Synchronizer::class)]
final class SynchronizerTest extends TestCase
{   
    private $ffmpegMock;
    private $videoMock;
    private $audioMock;
    private $advancedMediaMock;
    private $temporaryDirectoryMock;

    protected function setUp() : void
    {
        global $mockGlobalFunctions;
        $mockGlobalFunctions = true;

        $this->ffmpegMock = $this->createMock(FFMpeg::class);
        $this->videoMock = $this->createMock(Video::class);
        $this->audioMock = $this->createMock(Audio::class);
        $this->advancedMediaMock = $this->createMock(AdvancedMedia::class);
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

        $this->ffmpegMock->method('openAdvanced')
            ->willReturn($this->advancedMediaMock);

        $this->configureVideoMock();
        $this->configureAudioMock();
        $this->configureAdvancedMediaMock();
        $this->configureTemporaryDirectoryMock();
    }

    private function configureVideoMock() : void
    {   
        $formatMock = $this->createMock(AbstractData::class);
        $formatMock->method('get')
            ->with('duration')
            ->willReturn(rand(11,100) / 10);
        $this->videoMock->method('getTemporaryDirectory')
            ->willReturn($this->temporaryDirectoryMock);
        $this->videoMock->method('getFormat')
            ->willReturn($formatMock);
        $this->videoMock->method('getPathfile')
            ->willReturn('path' . DIRECTORY_SEPARATOR .'video_file.mp4');
        $this->videoMock->method('concat')
            ->willReturn($this->createMock(Concat::class));
        $this->videoMock->method('filters')
            ->willReturn($this->createMock(VideoFilters::class));
    }

    private function configureAudioMock() : void
    {
        $this->audioMock->method('getTemporaryDirectory')
            ->willReturn($this->temporaryDirectoryMock);
        $this->audioMock->method('getPathfile')
            ->willReturn('path' . DIRECTORY_SEPARATOR .'audio_file.mp3');
        $this->audioMock->method('filters')
            ->willReturn($this->createMock(AudioFilters::class));
    }

    private function configureAdvancedMediaMock() : void
    {
        $this->advancedMediaMock->method('filters')
            ->willReturn($this->createMock(ComplexFilters::class));
        $this->advancedMediaMock->method('map')
            ->willReturnSelf();
    }

    private function configureTemporaryDirectoryMock() : void
    {
        $this->temporaryDirectoryMock->method('create')
            ->willReturnSelf();
        $this->temporaryDirectoryMock->method('path')
            ->willReturn('path');
    }

    public function testSuccess() : void
    {
        $path = 'path';
        $videoName = 'video_file.mp4';
        $audioName = 'audio_file.mp3';
        
        $synchronizer = new Synchronizer($this->ffmpegMock);
        $result = $synchronizer->sync(
            $path . DIRECTORY_SEPARATOR .$videoName, 
            $path . DIRECTORY_SEPARATOR .$audioName,
            0.5
        );

        $this->assertEquals($this->videoMock, $result);
    }

    public function testInvalidVideo() : void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $path = 'path';
        $audioName = 'audio_file.mp3';
        $notPath = 'not path';
        
        (new Synchronizer($this->ffmpegMock))
            ->sync($notPath, $path . DIRECTORY_SEPARATOR .$audioName);
    }

    public function testInvalidAudio() : void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $path = 'path';
        $videoName = 'video_file.mp4';
        $notPath = 'not path';
        
        (new Synchronizer($this->ffmpegMock))
            ->sync($path . DIRECTORY_SEPARATOR . $videoName, $notPath);
    }
}

// Overwriting `file_exists`
function file_exists(string $path) : bool
{
    global $mockGlobalFunctions;

    if (isset($mockGlobalFunctions) && $mockGlobalFunctions === true) {
        if ($path == 'not path') {
            return false;
        }
        return true;
    } else {
        return call_user_func_array('\file_exists', func_get_args());
    }
}