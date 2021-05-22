<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Components\BufferedWriter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class BufferedWriterTest extends TestCase
{
    public function testReturnsOutput(): void
    {
        $writer = new BufferedWriter();
        $writer->write('Test');
        static::assertSame('Test', $writer->getOutput());
    }
}
