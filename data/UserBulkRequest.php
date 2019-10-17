<?php
namespace lepota\data;

use stdClass;
use Functional;
use Yii;
use lepota\data\BulkRequest;
use lepota\domain\User;

/**
 * Bulk load users from user service (using userClient dependency)
 */
class UserBulkRequest extends BulkRequest
{

    public function loadEntities(array $ids): array
    {
        $users = Functional\map(
            Yii::$app->userClient->getCollection('user', ['id' => $ids], false)->users,
            function (stdClass $user): User {
                return User::createFromObject($user);
            }
        );
        return array_combine(Functional\pluck($users, 'id'), $users);
    }

}
