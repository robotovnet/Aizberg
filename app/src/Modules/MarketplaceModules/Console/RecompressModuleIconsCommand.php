<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Console;

use Illuminate\Console\Command;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Services\MarketplaceModuleIconService;
use Throwable;

class RecompressModuleIconsCommand extends Command
{
    protected $signature = 'marketplace-modules:recompress-icons
                            {--dry-run : Только показать, какие иконки будут пережаты}';

    protected $description = 'Пережать загруженные иконки модулей маркетплейса до размера из конфига (icon_width x icon_height)';

    public function handle(MarketplaceModuleIconService $iconService): int
    {
        $dryRun = (bool)$this->option('dry-run');
        $recompressed = 0;
        $skipped = 0;
        $failed = 0;

        // Один файл может использоваться несколькими модулями, поэтому идём по уникальным путям.
        $paths = MarketplaceModule::query()
            ->whereNotNull('icon_path')
            ->where('icon_path', '!=', '')
            ->distinct()
            ->orderBy('icon_path')
            ->pluck('icon_path');

        foreach ($paths as $path) {
            try {
                if ($iconService->recompressStored((string)$path, $dryRun)) {
                    $recompressed++;
                    $this->line(($dryRun ? 'Будет пережата: ' : 'Пережата: ') . $path);
                } else {
                    $skipped++;
                }
            } catch (Throwable $exception) {
                $failed++;
                $this->warn($exception->getMessage());
            }
        }

        $this->info(($dryRun ? 'Будет пережато: ' : 'Пережато: ') . $recompressed);
        $this->info("Пропущено (уже нужного размера / SVG): {$skipped}");
        $this->info("Ошибок: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
