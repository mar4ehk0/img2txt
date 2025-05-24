<?php

namespace App\Tests\Unit\View;

use App\Entity\Image;
use App\Entity\Text;
use App\UseCase\TextSearch\TextSearchResultDto;
use App\View\TextSearchResultView;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Factory\UlidFactory;

class TextSearchResultViewTest extends TestCase
{

    /**
     * @dataProvider dataProviderSomeRender
     */
    public function testSomeRender(array $data, array $expected): void
    {
        // arrange
        $texts = [];
        foreach ($data as $item) {
            $image = $this->createMock(Image::class);
            $now = new DateTimeImmutable();
            $texts[] = new Text($item['id'], $item['text'], $image, $now, $now);
        }
        $dto = new TextSearchResultDto($texts);

        // act
        $textSearchResultView = new TextSearchResultView($dto);
        $result = $textSearchResultView->getView();

        // assert
        $this->assertEquals($expected, $result);
    }

    private function dataProviderSomeRender(): array
    {
        $ulidFactory = new UlidFactory();

        $result = [];
        $data = [];
        $expected = [];
        for ($i = 0; $i < 10; $i++) {
            $id = $ulidFactory->create();
            $content = sprintf('lorem ipsum %d', $i);
            $data[] = ['id' => $id, 'text' => $content];
            $expected[] = ['id' => $id->toString(), 'text' => $content];
            $result[] = [
                'data' => $data,
                'expected' => $expected,
            ];
        }

        return $result;
    }
}
