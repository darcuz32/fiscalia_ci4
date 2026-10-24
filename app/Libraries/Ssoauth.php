<?php

namespace App\Libraries;

use Config\Database;
use Config\Sso as SsoConfig;

$bridgePath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'fiscalia_bridge'
            . DIRECTORY_SEPARATOR . 'SsoAuth.php';

if (!class_exists('SsoAuth')) {
    if (!is_file($bridgePath)) {
        throw new \RuntimeException('SsoAuth bridge not found: ' . $bridgePath);
    }
    require_once $bridgePath;
}

class Ssoauth
{
    private \SsoAuth $sso;

    public function __construct(?SsoConfig $config = null)
    {
        $config ??= config('Sso');

        $db   = config('Database')->default;
        $port = !empty($db['port']) ? ';port=' . (int) $db['port'] : '';
        $dsn  = 'mysql:host=' . $db['hostname'] . $port
              . ';dbname=' . $db['database']
              . ';charset=' . ($db['charset'] ?? 'utf8mb4');

        $pdo = new \PDO($dsn, $db['username'], $db['password'], [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        $this->sso = new \SsoAuth($pdo, [
            'ttl'        => $config->ttl,
            'cookieName' => $config->cookieName,
            'path'       => $config->path,
            'domain'     => $config->domain,
            'secure'     => $config->secure,
            'sameSite'   => $config->sameSite,
        ]);
    }

    public function identity(): ?array
    {
        return $this->sso->current();
    }

    public function issue(int $userId): void
    {
        $this->sso->issue($userId);
    }

    public function revokeCurrent(): void
    {
        $this->sso->revokeCurrent();
    }

    public function revokeAllForUser(int $userId): void
    {
        $this->sso->revokeAllForUser($userId);
    }
}