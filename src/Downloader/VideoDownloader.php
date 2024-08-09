<?php

declare(strict_types=1);

namespace App\Downloader;

use FFMpeg\FFMpeg;
use FFMpeg\Media\Video;
use FFMpeg\Media\Audio;

final class VideoDownloader 
{

    public function __construct(private FFMpeg $ffmpeg) 
    {
    }

    public function download(string $url) : ?Video
    {
        $video = null;

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL');
        }

        try {
            $video = $this->ffmpeg->open($url);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Failed to download file: ' . $e->getCode());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Unsupported file format');
        }

        if (!($video instanceof Video)) {
            throw new \RuntimeException('Expected video file, got audio');
        }

        return $video;
    }
}