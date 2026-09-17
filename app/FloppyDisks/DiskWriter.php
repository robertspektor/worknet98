<?php

namespace App\FloppyDisks;

use App\Game\ActionRefused;
use App\Models\DiskFile;
use App\Models\PlayerFloppyDisk;
use Illuminate\Support\Facades\DB;

class DiskWriter
{
    public function save(PlayerFloppyDisk $disk, string $name, string $body): DiskFile
    {
        return DB::transaction(function () use ($disk, $name, $body): DiskFile {
            $locked = PlayerFloppyDisk::query()->lockForUpdate()->findOrFail($disk->id);
            $this->refuseWriteProtected($locked);
            $this->refuseOverflow($locked, $name, strlen($body));

            return DiskFile::updateOrCreate(
                ['player_floppy_disk_id' => $locked->id, 'name' => $name],
                ['kind' => DiskFileKind::Text, 'body' => $body, 'content_key' => null, 'program' => null, 'size_bytes' => strlen($body)],
            );
        });
    }

    public function erase(DiskFile $file): void
    {
        $this->refuseWriteProtected($file->disk);
        $file->delete();
    }

    private function refuseWriteProtected(PlayerFloppyDisk $disk): void
    {
        if ($disk->is_write_protected) {
            throw new ActionRefused(FloppyDiskRefusal::WriteProtected);
        }
    }

    private function refuseOverflow(PlayerFloppyDisk $disk, string $name, int $sizeBytes): void
    {
        $replacedBytes = (int) $disk->files()->where('name', $name)->value('size_bytes');

        if ($disk->usedBytes() - $replacedBytes + $sizeBytes > PlayerFloppyDisk::CAPACITY_BYTES) {
            throw new ActionRefused(FloppyDiskRefusal::DiskFull);
        }
    }
}
