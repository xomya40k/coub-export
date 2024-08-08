<?php

declare(strict_types=1);

namespace App\Downloader;

use FFMpeg\FFMpeg;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;

final class AudioDownloader {

    public function __construct(private FFMpeg $ffmpeg) 
    {
    }

    public function download(string $url) : ?Audio
    {
        $audio = null;

        try {
            $audio = $this->ffmpeg->open($url);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Failed to download file: ' . $e->getCode());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid url. Unsupported file format');
        }

        if ($audio instanceof Video) {
            throw new \RuntimeException('Expected audio file, got video');
        }

        return $audio;
    }
}