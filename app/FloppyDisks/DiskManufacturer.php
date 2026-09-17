<?php

namespace App\FloppyDisks;

use App\Models\FloppyDisk;
use App\Models\Order;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiskManufacturer
{
    public function make(User $player, FloppyDisk $catalogDisk, DiskSource $source, ?Order $order = null): PlayerFloppyDisk
    {
        return DB::transaction(function () use ($player, $catalogDisk, $source, $order): PlayerFloppyDisk {
            $disk = PlayerFloppyDisk::create([
                'user_id' => $player->id,
                'floppy_disk_id' => $catalogDisk->id,
                'order_id' => $order?->id,
                'source' => $source,
                'is_write_protected' => $catalogDisk->kind->isWriteProtectedOnDelivery(),
            ]);

            foreach ($catalogDisk->catalogFiles() as $file) {
                $disk->files()->create([
                    'name' => $file->name,
                    'kind' => $file->kind,
                    'content_key' => $file->contentKey,
                    'program' => $file->program,
                    'size_bytes' => $file->sizeBytes ?? $this->textSize($file, $player->locale),
                ]);
            }

            return $disk;
        });
    }

    private function textSize(CatalogFile $file, string $locale): int
    {
        return $file->contentKey === null ? 0 : strlen(DiskText::translate($file->contentKey, $locale));
    }
}
