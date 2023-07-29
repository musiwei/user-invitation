<?php

namespace Musiwei\UserInvitation\Commands;

use Illuminate\Console\Command;

class UserInvitationCommand extends Command
{
    public $signature = 'user-invitation';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
