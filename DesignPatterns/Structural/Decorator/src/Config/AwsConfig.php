<?php
declare(strict_types=1);

namespace App\Config;

final class AwsConfig
{
    public function __construct(
        public readonly string $key,
        public readonly string $secret,
        public readonly string $region,
        public readonly string $bucket,
        public readonly ?string $endpoint = null,
        public readonly bool $public = false,
    ) {}

    public static function fromIni(string $path): self
    {
        if (!is_file($path)) {
            throw new \RuntimeException("AWS config not found: {$path}");
        }

        $data = parse_ini_file($path, false, INI_SCANNER_RAW) ?: [];

        $required = ['AWS_KEY', 'AWS_SECRET', 'AWS_REGION', 'AWS_BUCKET'];
        foreach ($required as $k) {
            if (!isset($data[$k]) || trim((string)$data[$k]) === '') {
                throw new \RuntimeException("Missing required AWS key: {$k}");
            }
        }

        $public = filter_var($data['AWS_PUBLIC'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
        $endpoint = isset($data['AWS_ENDPOINT']) && trim((string)$data['AWS_ENDPOINT']) !== ''
            ? (string)$data['AWS_ENDPOINT']
            : null;

        return new self(
            key: (string)$data['AWS_KEY'],
            secret: (string)$data['AWS_SECRET'],
            region: (string)$data['AWS_REGION'],
            bucket: (string)$data['AWS_BUCKET'],
            endpoint: $endpoint,
            public: $public
        );
    }
}
