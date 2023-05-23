<?php

namespace App\Repositories;

use App\Models\SiteConfigs\Config;
use App\Models\SiteConfigs\Exceptions\ConfigNotFound;
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
}