<?php

namespace App\Repositories;

use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use PDO;

class PermissionsRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function getRolesForUser(User $user): Roles
    {
        $stmt = $this->db->prepare("
            SELECT role.*
            FROM users AS user
            JOIN group_user AS groupLink
                ON user.id = groupLink.user_id
                    AND groupLink.deleted_at IS NULL
            JOIN `groups` AS userGroup
                ON groupLink.group_id = userGroup.id
                    AND userGroup.deleted_at IS NULL
            JOIN group_role AS roleLink
                ON userGroup.id = roleLink.group_id
                    AND roleLink.deleted_at IS NULL
            JOIN roles AS role
                ON roleLink.role_id = role.id
                    AND role.deleted_at IS NULL
            WHERE user.id = :id
            GROUP BY role.id
        ");

        $stmt->execute([
            'id' => $user->id,
        ]);

        return (new Roles($stmt->fetchAll(PDO::FETCH_OBJ)))
            ->map(function (object $data) {
                return new Role(
                    (int) $data->id,
                    $data->name
                );
            });
    }
}
