<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP;

use Generator;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FaqReader
{
    public function parseFaq(string $path): array
    {
        $questions = [];
        $currentQuestion = null;

        foreach ($this->parse($path) as $line) {
            if ($line[0] === '#') {
                $currentQuestion = $this->parseTitle($line);
            } else {
                if (!$currentQuestion) {
                    throw new InvalidArgumentException(sprintf('FAQ in path "%s" is invalid', $path));
                }

                if (trim($line) === '') {
                    break;
                }

                if (!isset($questions[$currentQuestion])) {
                    $questions[$currentQuestion] = '';
                }

                $questions[$currentQuestion] .= $line;
            }
        }

        $formattedQuestions = [];

        foreach ($questions as $question => $answer) {
            $formattedQuestions[] = compact('question', 'answer');
        }

        return $formattedQuestions;
    }

    private function parse(string $path): Generator
    {
        if (!is_file($path)) {
            throw new FileNotFoundException(null, 0, null, $path);
        }

        $file = fopen($path, 'rb');

        while ($line = fgets($file)) {
            yield $line;
        }
        fclose($file);
    }

    private function parseTitle($line): string
    {
        return trim(mb_substr($line, 1));
    }
}
