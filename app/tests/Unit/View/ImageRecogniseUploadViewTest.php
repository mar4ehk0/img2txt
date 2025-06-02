<?php

namespace App\Tests\Unit\View;

use App\UseCase\TextRecognizer\TextRecognizerResultDto;
use App\View\ImageRecogniseUploadView;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Factory\UlidFactory;
use Symfony\Component\Uid\Ulid;

class ImageRecogniseUploadViewTest extends TestCase
{

    #[DataProvider('dataProviderSomeRender')]
    public function testSomeRender(Ulid $imageId, Ulid $textId, string $text, array $expected): void
    {
        $dto = new TextRecognizerResultDto($imageId, $textId, $text);

        $view = new ImageRecogniseUploadView($dto);
        $result = $view->getView();

        $this->assertEquals($expected, $result);
    }

    public static function dataProviderSomeRender(): array
    {
        $ulidFactory = new UlidFactory();

        $result = [];

        for ($i = 0; $i < 10; $i++) {
            $imageId = $ulidFactory->create();
            $textId = $ulidFactory->create();
            $text = sprintf('recognized text %d', $i);

            $expected = [
                'image_id' => $imageId->toString(),
                'text_id' => $textId->toString(),
                'text' => $text,
            ];

            $result[] = [
                $imageId,
                $textId,
                $text,
                $expected,
            ];
        }

        return $result;
    }

}
