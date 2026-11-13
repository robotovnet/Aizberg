<?php

namespace Modules\City\Services;

use Modules\BonusProgram\Entities\BonusLevel;

class BonusprogramAttachService
{
    /**
     * Returns id bonus level.
     */
    public function getEntityId(array $data): int
    {
        $bonusLevel = BonusLevel::where('is_entry', 'Y')->first(['id']);

        return $bonusLevel->id;
    }
}
