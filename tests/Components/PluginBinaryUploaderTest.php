<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\PluginBinaryUploader;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Binary;
use FroshPluginUploader\Structs\CodeReview\CodeReview;
use FroshPluginUploader\Structs\Input\UploadPluginInput;
use FroshPluginUploader\Structs\Plugin as StorePlugin;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @internal
 */
class PluginBinaryUploaderTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateBinary(): void
    {
        $uploader = $this->create();

        $input = new UploadPluginInput(__DIR__, $this->createPlugin(), $this->createStorePlugin(), false, false);

        $result = $uploader->upload($input);
        static::assertTrue($result->isPassed());
    }

    public function testUpdateBinary(): void
    {
        $uploader = $this->create(true);

        $input = new UploadPluginInput(__DIR__, $this->createPlugin(), $this->createStorePlugin(), false, false);

        $result = $uploader->upload($input);
        static::assertTrue($result->isPassed());
    }

    public function testDontTriggerCodeReview(): void
    {
        $uploader = $this->create(false, true);

        $input = new UploadPluginInput(__DIR__, $this->createPlugin(), $this->createStorePlugin(), true, false);

        $result = $uploader->upload($input);
        static::assertTrue($result->isPassed());
    }

    public function testDontWaitForCodeReview(): void
    {
        $uploader = $this->create(false, false, true);

        $input = new UploadPluginInput(__DIR__, $this->createPlugin(), $this->createStorePlugin(), false, true);

        $result = $uploader->upload($input);
        static::assertTrue($result->isPassed());
    }

    private function create(bool $binaryExists = false, $skipCodeReview = false, $skipWaitingForCodeReview = false): PluginBinaryUploader
    {
        $client = $this->createMock(Client::class);

        $plugins = $this->prophesize(\FroshPluginUploader\Components\SBP\Components\Plugin::class);
        $plugins->hasVersion(Argument::any(), Argument::any())
            ->willReturn($binaryExists)
            ->shouldBeCalled()
        ;
        $plugins->getAvailableBinaries(Argument::any())
            ->shouldBeCalled()
            ->willReturn([])
        ;

        if ($binaryExists) {
            $plugins->getVersion(Argument::any(), Argument::any())
                ->shouldBeCalled()
                ->willReturn(Binary::make(['id' => 1, 'changelogs' => [['text' => 'text'], ['text' => 'text']]]))
            ;

            $plugins->updateBinaryFile(Argument::any(), Argument::any(), Argument::any())
                ->shouldBeCalled()
            ;
        } else {
            $plugins->createBinaryFile(Argument::type('string'), Argument::type('int'))
                ->shouldBeCalled()
                ->willReturn(Binary::make(['id' => 1, 'changelogs' => [['text' => 'text'], ['text' => 'text']]]))
            ;
        }

        $plugins
            ->getCodeReviewResults(Argument::type('int'), Argument::type('int'))
            ->shouldBeCalled()
            ->willReturn([])
        ;

        $plugins->updateBinary(Argument::type(Binary::class), Argument::type('int'))
            ->shouldBeCalled()
        ;

        if (!$skipCodeReview) {
            $plugins->triggerCodeReview(Argument::type('int'))
                ->shouldBeCalled()
                ->will(function () use ($plugins, $skipWaitingForCodeReview): void {
                    if ($skipWaitingForCodeReview) {
                        return;
                    }
                    $plugins
                        ->getCodeReviewResults(Argument::type('int'), Argument::type('int'))
                        ->shouldBeCalled()
                        ->willReturn([CodeReview::make(['type' => ['id' => 3], 'subCheckResults' => [
                            [
                                'passed' => true,
                                'hasWarnings' => false,
                                'message' => 'test',
                            ],
                        ]])])
                    ;
                })
            ;
        }

        $client->method('Plugins')->willReturn($plugins->reveal());

        return new PluginBinaryUploader($client);
    }

    private function createPlugin(): Plugin
    {
        return $this->createMock(Plugin::class);
    }

    private function createStorePlugin(): StorePlugin
    {
        return StorePlugin::make(['id' => 1]);
    }
}
