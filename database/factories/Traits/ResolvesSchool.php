<?php

declare(strict_types=1);

namespace Database\Factories\Traits;

use App\Models\School;
use Illuminate\Support\Facades\Context;

trait ResolvesSchool
{
    public function configure(): self
    {
        return parent::configure()
            ->forSchool();
    }

    public function forSchool(?School $school = null): self
    {
        $school ??= Context::get('school') ?? School::factory();

        return $this->for($school);
    }
}
