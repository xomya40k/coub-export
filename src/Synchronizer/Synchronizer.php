<?php

declare(strict_types=1);

namespace App\Synchronizer;

use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Video;
use FFMpeg\Media\Audio;
use FFMpeg\Media\AbstractMediaType;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\DefaultVideo;
use FFMpeg\Format\Audio\DefaultAudio;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Audio\Mp3;

final class Synchronizer 
{
    public const float MAX_VIDEO_DURATION = 10.0;
    
    private FFMpeg $ffmpeg;
    private DefaultVideo $videoFormat;
    private DefaultAudio $audioFormat;

    public function __construct(FFMpeg $ffmpeg)
    {
        $this->ffmpeg = $ffmpeg;
        $this->videoFormat = new X264('libmp3lame');
        $this->audioFormat = new Mp3();
    }

    public function sync(string $videoPath, string $audioPath, ?float $sampleDuration = null) : Video
    {
        if (file_exists($videoPath)) {
            $video = $this->ffmpeg->open($videoPath);
        } else {
            throw new \InvalidArgumentException($videoPath . ': No such file or directory');
        }
        
        if (file_exists($audioPath)) {
            $audio = $this->ffmpeg->open($audioPath);
        } else {
            throw new \InvalidArgumentException($audioPath . ': No such file or directory');
        }
        

        if (!isset($sampleDuration)) {
            $video = $this->loopVideo($video, self::MAX_VIDEO_DURATION);
        }
        
        $videoDuration = (float) $video->getFormat()->get('duration');
        $audio = $this->cutAudio($audio, $videoDuration);
        
        return $this->merge($video, $audio);
    }

    private function cutAudio(Audio $audio, float $duration) : Audio
    {
        $audio->filters()->clip(
            TimeCode::fromSeconds(0), 
            TimeCode::fromSeconds($duration)
        );

        $newFilePath = $this->getNewFilePath($audio);

        $audio->save($this->audioFormat, $newFilePath);
        $this->cleanupTemporaryFile($audio->getPathfile());

        return $this->ffmpeg->open($newFilePath);
    }

    private function cutVideo(Video $video, float $duration) : Video
    {
        $video->filters()->clip(
            TimeCode::fromSeconds(0), 
            TimeCode::fromSeconds($duration)
        );

        $newFilePath = $this->getNewFilePath($video);

        $video->save($this->videoFormat, $newFilePath);
        $this->cleanupTemporaryFile($video->getPathfile());

        return $this->ffmpeg->open($newFilePath);
    }

    private function loopVideo(Video $video, float $maxDuration = self::MAX_VIDEO_DURATION) : Video
    {        
        $videoCopies = [];
        $videoPath = $video->getPathfile();
        $videoDuration = (float) $video->getFormat()->get('duration');
        $videoLoops = (int) $maxDuration / $videoDuration;

        for ($i=0; $i < $videoLoops; $i++) {
            $videoCopies[] = $videoPath;
        }

        $newFilePath = $this->getNewFilePath($video);

        $video->concat($videoCopies)->saveFromSameCodecs($newFilePath);
        $this->cleanupTemporaryFile($video->getPathfile());
        $video = $this->ffmpeg->open($newFilePath);
        
        return $this->cutVideo($video, $maxDuration);
    }

    private function merge(Video $video, Audio $audio) : Video
    {
        $mergeMedia = $this->ffmpeg->openAdvanced(
            [
                $video->getPathfile(), 
                $audio->getPathfile()
            ]);

        $newFilePath = $this->getNewFilePath($video, false);
        
        $mergeMedia->filters()->custom('[0:v]', 'copy', '[v]');
        $mergeMedia->map(
            array('1:a', '[v]'), 
            $this->videoFormat, 
            $newFilePath
        )->save();
        
        $this->cleanupTemporaryFile($video->getPathfile());
        $this->cleanupTemporaryFile($audio->getPathfile());
        
        return $this->ffmpeg->open($newFilePath);
    }

    private function getNewFilePath(AbstractMediaType $mediaFile) : string
    {   
        $outputPath = $mediaFile->getTemporaryDirectory()->create()->path();
        $fileExtension = pathinfo($mediaFile->getPathfile(), PATHINFO_EXTENSION);
        $newFileName = mt_rand().'-'.str_replace([' ', '.'], '', microtime()) . '.' .$fileExtension;
        
        return $outputPath . DIRECTORY_SEPARATOR . $newFileName;
    }

    private function cleanupTemporaryFile(string $filename) : bool
    {
        if (file_exists($filename) && is_writable($filename)) {
            unlink($filename);
            
            return true;
        }

        return false;
    }
}