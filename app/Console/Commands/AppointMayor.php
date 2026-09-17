<?php

namespace App\Console\Commands;

use App\Auth\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mayor:appoint {email : E-mail address of the account}')]
#[Description('Give an account the mayor role, which unlocks the CITYNET terminal')]
class AppointMayor extends Command
{
    public function handle(): int
    {
        $player = User::query()->where('email', $this->argument('email'))->first();

        if ($player === null) {
            $this->error('No account with that e-mail address.');

            return self::FAILURE;
        }

        $player->update(['role' => Role::Mayor]);
        $this->info("{$player->email} is now the mayor.");

        return self::SUCCESS;
    }
}
