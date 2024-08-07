<?php

declare(strict_types=1);

namespace App\Test\Unit\Entity\Coub;

use App\Coub\Entity\Coub\AudioCollection;
use App\Coub\Test\Builder\AudioBuilder;
use stdClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AudioCollection::class)]
final class AudioCollectionTest extends TestCase
{
    public function testSuccess(): void
    {
        $audioHighQuality = (new AudioBuilder())
            ->withQuality(AudioBuilder::QUALITY_HIGH)
                ->build();
        $audioMediumQuality = (new AudioBuilder())
            ->withQuality(AudioBuilder::QUALITY_MEDIUM)
                ->build();
        
        $audioCollection = new AudioCollection([$audioHighQuality, $audioMediumQuality]);

        self::assertSame($audioHighQuality, $audioCollection->getHighQuality());
        self::assertSame($audioMediumQuality, $audioCollection->getMediumQuality());
    }

    public function testIncorrect() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $incorrectObject = new stdClass();
        $audioMediumQuality = (new AudioBuilder())
            ->withQuality(AudioBuilder::QUALITY_MEDIUM)
                ->build();
        
        new AudioCollection([$incorrectObject, $audioMediumQuality]);
    }
}