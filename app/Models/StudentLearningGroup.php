<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $learning_group_id
 * @property string $student_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentLearningGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentLearningGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentLearningGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentLearningGroup whereLearningGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentLearningGroup whereStudentId($value)
 *
 * @mixin \Eloquent
 */
class StudentLearningGroup extends Pivot {}
