<?php

namespace App\Repositories;

use App\Models\SiteConfigs\Config;
use App\Models\SiteConfigs\Exceptions\ConfigNotFound;
use App\Models\Users\User;
use PDO;

class SiteConfigRepository
{
    public const HOMEPAGE_DESCRIPTION = 'homepage-description';

    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @throws ConfigNotFound
     */
    public function getConfigByName(string $configName): Config
    {
        $stmt = $this->db->prepare("
            SELECT * FROM configs WHERE id = :id
        ");

        $stmt->execute(['id' => $configName]);

        if ($stmt->rowCount() === 0) {
            throw ConfigNotFound::withName($configName);
        }

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return new Config(
            $data->id,
            $data->value
        );
    }

    /**
     * @throws ConfigNotFound
     */
    public function getHomepageDescriptionConfig(): Config
    {
        return $this->getConfigByName(self::HOMEPAGE_DESCRIPTION);
    }

    public function saveConfig(Config $config, User $user): void
    {
        $stmt = $this->db->prepare("
            UPDATE configs
            SET value = :value,
                updated_at = NOW(),
                actor_id = :actorId
            WHERE id = :id
        ");

        $stmt->execute([
            'value' => $config->value,
            'actorId' => $user->id,
            'id' => $config->id,
        ]);
    }
}
