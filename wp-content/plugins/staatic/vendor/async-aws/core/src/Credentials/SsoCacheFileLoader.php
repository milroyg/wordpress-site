<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use DateTimeImmutable;
use Exception;
use Staatic\Vendor\AsyncAws\Core\EnvVar;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
final class SsoCacheFileLoader
{
    public const KEY_ACCESS_TOKEN = 'accessToken';
    public const KEY_EXPIRES_AT = 'expiresAt';
    private $logger;
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }
    public function loadSsoCacheFile(string $ssoStartUrl): array
    {
        $filepath = \sprintf('%s/.aws/sso/cache/%s.json', $this->getHomeDir(), sha1($ssoStartUrl));
        if (\false === $contents = @file_get_contents($filepath)) {
            $this->logger->warning('The sso cache file {path} is not readable.', ['path' => $filepath]);
            return [];
        }
        $tokenData = json_decode($contents, \true);
        if (!isset($tokenData[self::KEY_ACCESS_TOKEN], $tokenData[self::KEY_EXPIRES_AT])) {
            $this->logger->warning('Token file at {path} must contain an accessToken and an expiresAt.', ['path' => $filepath]);
            return [];
        }
        try {
            $expiration = new DateTimeImmutable($tokenData[self::KEY_EXPIRES_AT]);
        } catch (Exception $e) {
            $this->logger->warning('Cached SSO credentials returned an invalid expiresAt value.');
            return [];
        }
        if ($expiration < new DateTimeImmutable()) {
            $this->logger->warning('Cached SSO credentials returned an invalid expiresAt value.');
            return [];
        }
        return $tokenData;
    }
    private function getHomeDir(): string
    {
        if (null !== $homeDir = EnvVar::get('HOME')) {
            return $homeDir;
        }
        $homeDrive = EnvVar::get('HOMEDRIVE');
        $homePath = EnvVar::get('HOMEPATH');
        return $homeDrive && $homePath ? $homeDrive . $homePath : '/';
    }
}
