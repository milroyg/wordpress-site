<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Closure;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\NativeClientState;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\NativeResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\ResponseStream;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
final class NativeHttpClient implements HttpClientInterface, LoggerAwareInterface, ResetInterface
{
    use HttpClientTrait;
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $defaultOptions = self::OPTIONS_DEFAULTS;
    /**
     * @var mixed[]
     */
    private static $emptyDefaults = self::OPTIONS_DEFAULTS;
    /**
     * @var NativeClientState
     */
    private $multi;
    public function __construct(array $defaultOptions = [], int $maxHostConnections = 6)
    {
        $this->defaultOptions['buffer'] = $this->defaultOptions['buffer'] ?? Closure::fromCallable([self::class, 'shouldBuffer']);
        if ($defaultOptions) {
            [, $this->defaultOptions] = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
        }
        $this->multi = new NativeClientState();
        $this->multi->maxHostConnections = 0 < $maxHostConnections ? $maxHostConnections : \PHP_INT_MAX;
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        [$url, $options] = self::prepareRequest($method, $url, $options, $this->defaultOptions);
        if ($options['bindto']) {
            if (file_exists($options['bindto'])) {
                throw new TransportException(__CLASS__ . ' cannot bind to local Unix sockets, use e.g. CurlHttpClient instead.');
            }
            if (strncmp($options['bindto'], 'if!', strlen('if!')) === 0) {
                throw new TransportException(__CLASS__ . ' cannot bind to network interfaces, use e.g. CurlHttpClient instead.');
            }
            if (strncmp($options['bindto'], 'host!', strlen('host!')) === 0) {
                $options['bindto'] = substr($options['bindto'], 5);
            }
        }
        $hasContentLength = isset($options['normalized_headers']['content-length']);
        $hasBody = '' !== $options['body'] || 'POST' === $method || $hasContentLength;
        $options['body'] = self::getBodyAsString($options['body']);
        if ('chunked' === substr($options['normalized_headers']['transfer-encoding'][0] ?? '', \strlen('Transfer-Encoding: '))) {
            unset($options['normalized_headers']['transfer-encoding']);
            $options['headers'] = array_merge(...array_values($options['normalized_headers']));
            $options['body'] = self::dechunk($options['body']);
        }
        if ('' === $options['body'] && $hasBody && !$hasContentLength) {
            $options['headers'][] = 'Content-Length: 0';
        }
        if ($hasBody && !isset($options['normalized_headers']['content-type'])) {
            $options['headers'][] = 'Content-Type: application/x-www-form-urlencoded';
        }
        if (\extension_loaded('zlib') && !isset($options['normalized_headers']['accept-encoding'])) {
            $options['headers'][] = 'Accept-Encoding: gzip';
        }
        if ($options['peer_fingerprint']) {
            if (isset($options['peer_fingerprint']['pin-sha256']) && 1 === \count($options['peer_fingerprint'])) {
                throw new TransportException(__CLASS__ . ' cannot verify "pin-sha256" fingerprints, please provide a "sha256" one.');
            }
            unset($options['peer_fingerprint']['pin-sha256']);
        }
        $info = ['response_headers' => [], 'url' => $url, 'error' => null, 'canceled' => \false, 'http_method' => $method, 'http_code' => 0, 'redirect_count' => 0, 'start_time' => 0.0, 'connect_time' => 0.0, 'redirect_time' => 0.0, 'pretransfer_time' => 0.0, 'starttransfer_time' => 0.0, 'total_time' => 0.0, 'namelookup_time' => 0.0, 'size_upload' => 0, 'size_download' => 0, 'size_body' => \strlen($options['body']), 'primary_ip' => '', 'primary_port' => 'http:' === $url['scheme'] ? 80 : 443, 'debug' => \extension_loaded('curl') ? '' : "* Enable the curl extension for better performance\n"];
        if ($onProgress = $options['on_progress']) {
            $lastProgress = [0, 0];
            $maxDuration = 0 < $options['max_duration'] ? $options['max_duration'] : \INF;
            $onProgress = static function (...$progress) use ($onProgress, &$lastProgress, &$info, $maxDuration) {
                if ($info['total_time'] >= $maxDuration) {
                    throw new TransportException(sprintf('Max duration was reached for "%s".', implode('', $info['url'])));
                }
                $progressInfo = $info;
                $progressInfo['url'] = implode('', $info['url']);
                unset($progressInfo['size_body']);
                if ($progress && -1 === $progress[0]) {
                    $lastProgress[0] = max($lastProgress);
                } else {
                    $lastProgress = $progress ?: $lastProgress;
                }
                $onProgress($lastProgress[0], $lastProgress[1], $progressInfo);
            };
        } elseif (0 < $options['max_duration']) {
            $maxDuration = $options['max_duration'];
            $onProgress = static function () use (&$info, $maxDuration): void {
                if ($info['total_time'] >= $maxDuration) {
                    throw new TransportException(sprintf('Max duration was reached for "%s".', implode('', $info['url'])));
                }
            };
        }
        $notification = static function (int $code, int $severity, ?string $msg, int $msgCode, int $dlNow, int $dlSize) use ($onProgress, &$info) {
            $info['total_time'] = microtime(\true) - $info['start_time'];
            if (\STREAM_NOTIFY_PROGRESS === $code) {
                $info['starttransfer_time'] = $info['starttransfer_time'] ?: $info['total_time'];
                $info['size_upload'] += $dlNow ? 0 : $info['size_body'];
                $info['size_download'] = $dlNow;
            } elseif (\STREAM_NOTIFY_CONNECT === $code) {
                $info['connect_time'] = $info['total_time'];
                $info['debug'] .= $info['request_header'];
                unset($info['request_header']);
            } else {
                return;
            }
            if ($onProgress) {
                $onProgress($dlNow, $dlSize);
            }
        };
        if ($options['resolve']) {
            $this->multi->dnsCache = $options['resolve'] + $this->multi->dnsCache;
        }
        ($nullsafeVariable1 = $this->logger) ? $nullsafeVariable1->info(sprintf('Request: "%s %s"', $method, implode('', $url))) : null;
        if (!isset($options['normalized_headers']['user-agent'])) {
            $options['headers'][] = 'User-Agent: Symfony HttpClient/Native';
        }
        if (0 < $options['max_duration']) {
            $options['timeout'] = min($options['max_duration'], $options['timeout']);
        }
        $context = ['http' => ['protocol_version' => min($options['http_version'] ?: '1.1', '1.1'), 'method' => $method, 'content' => $options['body'], 'ignore_errors' => \true, 'curl_verify_ssl_peer' => $options['verify_peer'], 'curl_verify_ssl_host' => $options['verify_host'], 'auto_decode' => \false, 'timeout' => $options['timeout'], 'follow_location' => \false], 'ssl' => array_filter(['verify_peer' => $options['verify_peer'], 'verify_peer_name' => $options['verify_host'], 'cafile' => $options['cafile'], 'capath' => $options['capath'], 'local_cert' => $options['local_cert'], 'local_pk' => $options['local_pk'], 'passphrase' => $options['passphrase'], 'ciphers' => $options['ciphers'], 'peer_fingerprint' => $options['peer_fingerprint'], 'capture_peer_cert_chain' => $options['capture_peer_cert_chain'], 'allow_self_signed' => (bool) $options['peer_fingerprint'], 'SNI_enabled' => \true, 'disable_compression' => \true], static function ($v) {
            return null !== $v;
        }), 'socket' => ['bindto' => $options['bindto'], 'tcp_nodelay' => \true]];
        $context = stream_context_create($context, ['notification' => $notification]);
        $resolver = static function ($multi) use ($context, $options, $url, &$info, $onProgress) {
            [$host, $port] = self::parseHostPort($url, $info);
            if (!isset($options['normalized_headers']['host'])) {
                $options['headers'][] = 'Host: ' . $host . $port;
            }
            $proxy = self::getProxy($options['proxy'], $url, $options['no_proxy']);
            if (!self::configureHeadersAndProxy($context, $host, $options['headers'], $proxy, 'https:' === $url['scheme'])) {
                $ip = self::dnsResolve($host, $multi, $info, $onProgress);
                $url['authority'] = substr_replace($url['authority'], $ip, -\strlen($host) - \strlen($port), \strlen($host));
            }
            return [self::createRedirectResolver($options, $host, $port, $proxy, $info, $onProgress), implode('', $url)];
        };
        return new NativeResponse($this->multi, $context, implode('', $url), $options, $info, $resolver, $onProgress, $this->logger);
    }
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof NativeResponse) {
            $responses = [$responses];
        }
        return new ResponseStream(NativeResponse::stream($responses, $timeout));
    }
    public function reset()
    {
        $this->multi->reset();
    }
    private static function getBodyAsString($body): string
    {
        if (\is_resource($body)) {
            return stream_get_contents($body);
        }
        if (!$body instanceof Closure) {
            return $body;
        }
        $result = '';
        while ('' !== $data = $body(self::$CHUNK_SIZE)) {
            if (!\is_string($data)) {
                throw new TransportException(sprintf('Return value of the "body" option callback must be string, "%s" returned.', get_debug_type($data)));
            }
            $result .= $data;
        }
        return $result;
    }
    private static function parseHostPort(array $url, array &$info): array
    {
        if ($port = parse_url($url['authority'], \PHP_URL_PORT) ?: '') {
            $info['primary_port'] = $port;
            $port = ':' . $port;
        } else {
            $info['primary_port'] = 'http:' === $url['scheme'] ? 80 : 443;
        }
        return [parse_url($url['authority'], \PHP_URL_HOST), $port];
    }
    private static function dnsResolve(string $host, NativeClientState $multi, array &$info, ?Closure $onProgress): string
    {
        if (null === $ip = $multi->dnsCache[$host] ?? null) {
            $info['debug'] .= "* Hostname was NOT found in DNS cache\n";
            $now = microtime(\true);
            if (!$ip = gethostbynamel($host)) {
                throw new TransportException(sprintf('Could not resolve host "%s".', $host));
            }
            $info['namelookup_time'] = microtime(\true) - ($info['start_time'] ?: $now);
            $multi->dnsCache[$host] = $ip = $ip[0];
            $info['debug'] .= "* Added {$host}:0:{$ip} to DNS cache\n";
        } else {
            $info['debug'] .= "* Hostname was found in DNS cache\n";
        }
        $info['primary_ip'] = $ip;
        if ($onProgress) {
            $onProgress();
        }
        return $ip;
    }
    private static function createRedirectResolver(array $options, string $host, string $port, ?array $proxy, array &$info, ?Closure $onProgress): Closure
    {
        $redirectHeaders = [];
        if (0 < $maxRedirects = $options['max_redirects']) {
            $redirectHeaders = ['host' => $host, 'port' => $port];
            $redirectHeaders['with_auth'] = $redirectHeaders['no_auth'] = array_filter($options['headers'], static function ($h) {
                return 0 !== stripos($h, 'Host:');
            });
            if (isset($options['normalized_headers']['authorization']) || isset($options['normalized_headers']['cookie'])) {
                $redirectHeaders['no_auth'] = array_filter($redirectHeaders['no_auth'], static function ($h) {
                    return 0 !== stripos($h, 'Authorization:') && 0 !== stripos($h, 'Cookie:');
                });
            }
        }
        return static function (NativeClientState $multi, ?string $location, $context) use (&$redirectHeaders, $proxy, &$info, $maxRedirects, $onProgress): ?string {
            if (null === $location || $info['http_code'] < 300 || 400 <= $info['http_code']) {
                $info['redirect_url'] = null;
                return null;
            }
            try {
                $url = self::parseUrl($location);
            } catch (InvalidArgumentException $exception) {
                $info['redirect_url'] = null;
                return null;
            }
            $url = self::resolveUrl($url, $info['url']);
            $info['redirect_url'] = implode('', $url);
            if ($info['redirect_count'] >= $maxRedirects) {
                return null;
            }
            $info['url'] = $url;
            ++$info['redirect_count'];
            $info['redirect_time'] = microtime(\true) - $info['start_time'];
            if (\in_array($info['http_code'], [301, 302, 303], \true)) {
                $options = stream_context_get_options($context)['http'];
                if ('POST' === $options['method'] || 303 === $info['http_code']) {
                    $info['http_method'] = $options['method'] = 'HEAD' === $options['method'] ? 'HEAD' : 'GET';
                    $options['content'] = '';
                    $filterContentHeaders = static function ($h) {
                        return 0 !== stripos($h, 'Content-Length:') && 0 !== stripos($h, 'Content-Type:') && 0 !== stripos($h, 'Transfer-Encoding:');
                    };
                    $options['header'] = array_filter($options['header'], $filterContentHeaders);
                    $redirectHeaders['no_auth'] = array_filter($redirectHeaders['no_auth'], $filterContentHeaders);
                    $redirectHeaders['with_auth'] = array_filter($redirectHeaders['with_auth'], $filterContentHeaders);
                    stream_context_set_option($context, ['http' => $options]);
                }
            }
            [$host, $port] = self::parseHostPort($url, $info);
            if (\false !== (parse_url($location, \PHP_URL_HOST) ?? \false)) {
                $requestHeaders = $redirectHeaders['host'] === $host && $redirectHeaders['port'] === $port ? $redirectHeaders['with_auth'] : $redirectHeaders['no_auth'];
                $requestHeaders[] = 'Host: ' . $host . $port;
                $dnsResolve = !self::configureHeadersAndProxy($context, $host, $requestHeaders, $proxy, 'https:' === $url['scheme']);
            } else {
                $dnsResolve = isset(stream_context_get_options($context)['ssl']['peer_name']);
            }
            if ($dnsResolve) {
                $ip = self::dnsResolve($host, $multi, $info, $onProgress);
                $url['authority'] = substr_replace($url['authority'], $ip, -\strlen($host) - \strlen($port), \strlen($host));
            }
            return implode('', $url);
        };
    }
    private static function configureHeadersAndProxy($context, string $host, array $requestHeaders, ?array $proxy, bool $isSsl): bool
    {
        if (null === $proxy) {
            stream_context_set_option($context, 'http', 'header', $requestHeaders);
            stream_context_set_option($context, 'ssl', 'peer_name', $host);
            return \false;
        }
        foreach ($proxy['no_proxy'] as $rule) {
            $dotRule = '.' . ltrim($rule, '.');
            if ('*' === $rule || $host === $rule || substr_compare($host, $dotRule, -strlen($dotRule)) === 0) {
                stream_context_set_option($context, 'http', 'proxy', null);
                stream_context_set_option($context, 'http', 'request_fulluri', \false);
                stream_context_set_option($context, 'http', 'header', $requestHeaders);
                stream_context_set_option($context, 'ssl', 'peer_name', $host);
                return \false;
            }
        }
        if (null !== $proxy['auth']) {
            $requestHeaders[] = 'Proxy-Authorization: ' . $proxy['auth'];
        }
        stream_context_set_option($context, 'http', 'proxy', $proxy['url']);
        stream_context_set_option($context, 'http', 'request_fulluri', !$isSsl);
        stream_context_set_option($context, 'http', 'header', $requestHeaders);
        stream_context_set_option($context, 'ssl', 'peer_name', null);
        return \true;
    }
}
