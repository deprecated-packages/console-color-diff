<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff\Console\Formatter;

use Nette\Utils\Strings;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Most is copy-pasted from https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/src/Differ/DiffConsoleFormatter.php
 * to be used as standalone class, without need to require whole package.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @see \Symplify\ConsoleColorDiff\Tests\Console\Formatter\ColorConsoleDiffFormatterTest
 */
final class ColorConsoleDiffFormatter
{
    /**
     * @var string
     */
    private const PLUS_START_PATTERN = '#^(\+.*)#';

    /**
     * @var string
     */
    private const MINUT_START_PATTERN = '#^(\-.*)#';

    /**
     * @var string
     */
    private const AT_START_PATTERN = '#^(@.*)#';

    /**
     * @var string
     */
    private const NEWLINES_PATTERN = "#\n\r|\n#";

    /**
     * @var string
     */
    private $template;

    public function __construct()
    {
        $this->template = sprintf(
            '<comment>    ---------- begin diff ----------</comment>%s%%s%s<comment>    ----------- end diff -----------</comment>' . PHP_EOL,
            PHP_EOL,
            PHP_EOL
        );
    }

    public function format(string $diff): string
    {
        return $this->formatWithTemplate($diff, $this->template);
    }

    private function formatWithTemplate(string $diff, string $template): string
    {
        $escapedDiff = OutputFormatter::escape(rtrim($diff));

        $escapedDiffLines = Strings::split($escapedDiff, self::NEWLINES_PATTERN);

        $coloredLines = array_map(function (string $string): string {
            $string = $this->makePlusLinesGreen($string);
            $string = $this->makeMinusLinesRed($string);
            $string = $this->makeAtNoteCyan($string);

            if ($string === ' ') {
                return '';
            }

            return $string;
        }, $escapedDiffLines);

        return sprintf($template, implode(PHP_EOL, $coloredLines));
    }

    private function makePlusLinesGreen(string $string): string
    {
        return Strings::replace($string, self::PLUS_START_PATTERN, '<fg=green>$1</fg=green>');
    }

    private function makeMinusLinesRed(string $string): string
    {
        return Strings::replace($string, self::MINUT_START_PATTERN, '<fg=red>$1</fg=red>');
    }

    private function makeAtNoteCyan(string $string): string
    {
        return Strings::replace($string, self::AT_START_PATTERN, '<fg=cyan>$1</fg=cyan>');
    }
}
