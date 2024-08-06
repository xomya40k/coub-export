<?php

declare(strict_types=1);

namespace CoubExport\Query\GetCoubByUrl;

final class Coub
{
    public function __construct(public string $permaLink, public int $id, public string $title,
        public string $videoHighUrl, public string $videoMediumUrl, 
        public string $audioHighUrl, public string $audioMediumUrl)
    { 
    }
}