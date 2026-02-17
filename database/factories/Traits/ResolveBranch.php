<?php

declare(strict_types=1);

namespace Database\Factories\Traits;

use App\Models\Branch;
use Illuminate\Support\Facades\Context;

trait ResolveBranch
{
    public function configure(): self
    {
        return parent::configure()
            ->forBranch();
    }

    public function forBranch(?Branch $branch = null): self
    {
        $contextBranch = Context::get('branch');
        $branch ??= $contextBranch instanceof Branch ? $contextBranch : null;
        $branch ??= Branch::factory();

        return $this->for($branch);
    }
}
