<?php

namespace Northwestern\SysDev\SOA\Console\Commands\Concerns;

use Illuminate\Console\Command;
use Symfony\Component\Console\Terminal;

/**
 * @mixin Command
 */
trait FormatsCommandOutput
{
    protected function divider(): void
    {
        $width = (new Terminal())->getWidth();
        $width = max(60, min(140, $width));

        $this->line('<fg=gray>'.str_repeat('─', $width).'</>');
    }

    protected function styledDetail(string $label, mixed $value): void
    {
        if (is_bool($value)) {
            $styledValue = $value
                ? '<fg=green>true</>'
                : '<fg=red>false</>';

            $this->line(" <fg=gray>{$label}:</> {$styledValue}");

            return;
        }

        if ($value === null) {
            $this->line(" <fg=gray>{$label}:</> <fg=gray>—</>");

            return;
        }

        $this->line(" <fg=gray>{$label}:</> {$value}");
    }
}
