<?php

namespace WPMailSMTP\Vendor\Aws;

use WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface;
use WPMailSMTP\Vendor\Aws\Credentials\CredentialSources;
/**
 * A placeholder for gathering metrics in a request.
 *
 * @internal
 */
final class MetricsBuilder
{
    const WAITER = "B";
    const PAGINATOR = "C";
    const RETRY_MODE_LEGACY = "D";
    const RETRY_MODE_STANDARD = "E";
    const RETRY_MODE_ADAPTIVE = "F";
    const S3_TRANSFER = "G";
    const S3_CRYPTO_V1N = "H";
    const S3_CRYPTO_V2 = "I";
    const S3_EXPRESS_BUCKET = "J";
    const GZIP_REQUEST_COMPRESSION = "L";
    const ENDPOINT_OVERRIDE = "N";
    const ACCOUNT_ID_ENDPOINT = "O";
    const ACCOUNT_ID_MODE_PREFERRED = "P";
    const ACCOUNT_ID_MODE_DISABLED = "Q";
    const ACCOUNT_ID_MODE_REQUIRED = "R";
    const SIGV4A_SIGNING = "S";
    const RESOLVED_ACCOUNT_ID = "T";
    const FLEXIBLE_CHECKSUMS_REQ_CRC32 = "U";
    const FLEXIBLE_CHECKSUMS_REQ_CRC32C = "V";
    const FLEXIBLE_CHECKSUMS_REQ_CRC64 = "W";
    const FLEXIBLE_CHECKSUMS_REQ_SHA1 = "X";
    const FLEXIBLE_CHECKSUMS_REQ_SHA256 = "Y";
    const CREDENTIALS_CODE = "e";
    const CREDENTIALS_ENV_VARS = "g";
    const CREDENTIALS_ENV_VARS_STS_WEB_ID_TOKEN = "h";
    const CREDENTIALS_STS_ASSUME_ROLE = "i";
    const CREDENTIALS_STS_ASSUME_ROLE_WEB_ID = "k";
    const CREDENTIALS_PROFILE = "n";
    const CREDENTIALS_PROFILE_STS_WEB_ID_TOKEN = "q";
    const CREDENTIALS_HTTP = "z";
    const CREDENTIALS_IMDS = "0";
    const CREDENTIALS_PROFILE_PROCESS = "v";
    const CREDENTIALS_PROFILE_SSO = "r";
    const CREDENTIALS_PROFILE_SSO_LEGACY = "t";
    /** @var int */
    private static $MAX_METRICS_SIZE = 1024;
    // 1KB or 1024 B
    /** @var string */
    private static $METRIC_SEPARATOR = ",";
    /** @var array $metrics */
    private $metrics;
    /** @var int $metricsSize */
    private $metricsSize;
    public function __construct()
    {
        $this->metrics = [];
        // The first metrics does not include the separator
        // therefore it is reduced by default.
        $this->metricsSize = -\strlen(self::$METRIC_SEPARATOR);
    }
    /**
     * Build the metrics string value.
     *
     * @return string
     */
    public function build() : string
    {
        if (empty($this->metrics)) {
            return "";
        }
        return $this->encode();
    }
    /**
     * Encodes the metrics by separating each metric
     * with a comma. Example: for the metrics[A,B,C] then
     * the output would be "A,B,C".
     *
     * @return string
     */
    private function encode() : string
    {
        return \implode(self::$METRIC_SEPARATOR, \array_keys($this->metrics));
    }
    /**
     * Appends a metric to the internal metrics holder after validating it.
     * Increases the current metrics size by the length of the new metric
     * plus the length of the encoding separator.
     * Example: $currentSize = $currentSize + len($newMetric) + len($separator)
     *
     * @param string $metric The metric to append.
     *
     * @return void
     */
    public function append(string $metric) : void
    {
        if (!$this->canMetricBeAppended($metric)) {
            return;
        }
        $this->metrics[$metric] = \true;
        $this->metricsSize += \strlen($metric) + \strlen(self::$METRIC_SEPARATOR);
    }
    /**
     * Receives a feature group and a value to identify which one is the metric.
     * For example, a group could be `signature` and a value could be `v4a`,
     * then the metric will be `SIGV4A_SIGNING`.
     *
     * @param string $featureGroup the feature group such as `signature`.
     * @param mixed $value the value for identifying the metric.
     *
     * @return void
     */
    public function identifyMetricByValueAndAppend(string $featureGroup, $value) : void
    {
        if (empty($value)) {
            return;
        }
        static $appendMetricFns = ['signature' => 'appendSignatureMetric', 'request_compression' => 'appendRequestCompressionMetric', 'request_checksum' => 'appendRequestChecksumMetric', 'credentials' => 'appendCredentialsMetric'];
        $fn = $appendMetricFns[$featureGroup];
        $this->{$fn}($value);
    }
    /**
     * Appends the signature metric based on the signature value.
     *
     * @param string $signature
     *
     * @return void
     */
    private function appendSignatureMetric(string $signature) : void
    {
        if ($signature === 'v4-s3express') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::S3_EXPRESS_BUCKET);
        } elseif ($signature === 'v4a') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::SIGV4A_SIGNING);
        }
    }
    /**
     * Appends the request compression metric based on the format resolved.
     *
     * @param string $format
     *
     * @return void
     */
    private function appendRequestCompressionMetric(string $format) : void
    {
        if ($format === 'gzip') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::GZIP_REQUEST_COMPRESSION);
        }
    }
    /**
     * Appends the request checksum metric based on the algorithm.
     *
     * @param string $algorithm
     *
     * @return void
     */
    private function appendRequestChecksumMetric(string $algorithm) : void
    {
        if ($algorithm === 'crc32') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::FLEXIBLE_CHECKSUMS_REQ_CRC32);
        } elseif ($algorithm === 'crc32c') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::FLEXIBLE_CHECKSUMS_REQ_CRC32C);
        } elseif ($algorithm === 'crc64') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::FLEXIBLE_CHECKSUMS_REQ_CRC64);
        } elseif ($algorithm === 'sha1') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::FLEXIBLE_CHECKSUMS_REQ_SHA1);
        } elseif ($algorithm === 'sha256') {
            $this->append(\WPMailSMTP\Vendor\Aws\MetricsBuilder::FLEXIBLE_CHECKSUMS_REQ_SHA256);
        }
    }
    /**
     * Appends the credentials metric based on the type of credentials
     * resolved.
     *
     * @param CredentialsInterface $credentials
     *
     * @return void
     */
    private function appendCredentialsMetric(\WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface $credentials) : void
    {
        $source = $credentials->toArray()['source'] ?? null;
        if (empty($source)) {
            return;
        }
        static $credentialsMetricMapping = [\WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::STATIC => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_CODE, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::ENVIRONMENT => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_ENV_VARS, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::ENVIRONMENT_STS_WEB_ID_TOKEN => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_ENV_VARS_STS_WEB_ID_TOKEN, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::STS_ASSUME_ROLE => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_STS_ASSUME_ROLE, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::STS_WEB_ID_TOKEN => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_STS_ASSUME_ROLE_WEB_ID, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::PROFILE => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_PROFILE, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::IMDS => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_IMDS, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::ECS => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_HTTP, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::PROFILE_STS_WEB_ID_TOKEN => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_PROFILE_STS_WEB_ID_TOKEN, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::PROFILE_PROCESS => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_PROFILE_PROCESS, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::PROFILE_SSO => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_PROFILE_SSO, \WPMailSMTP\Vendor\Aws\Credentials\CredentialSources::PROFILE_SSO_LEGACY => \WPMailSMTP\Vendor\Aws\MetricsBuilder::CREDENTIALS_PROFILE_SSO_LEGACY];
        if (isset($credentialsMetricMapping[$source])) {
            $this->append($credentialsMetricMapping[$source]);
        }
    }
    /**
     * Validates if a metric can be appended by ensuring the total size,
     * including the new metric and separator, does not exceed the limit.
     * Also checks that the metric does not already exist.
     * Example: Appendable if:
     *  $currentSize + len($newMetric) + len($separator) <= MAX_SIZE
     *  and:
     * $newMetric not in $existingMetrics
     *
     * @param string $newMetric The metric to validate.
     *
     * @return bool True if the metric can be appended, false otherwise.
     */
    private function canMetricBeAppended(string $newMetric) : bool
    {
        if ($newMetric === "") {
            return \false;
        }
        if ($this->metricsSize + (\strlen($newMetric) + \strlen(self::$METRIC_SEPARATOR)) > self::$MAX_METRICS_SIZE) {
            return \false;
        }
        if (isset($this->metrics[$newMetric])) {
            return \false;
        }
        return \true;
    }
    /**
     * Returns the metrics builder from the property @context of a command.
     *
     * @param Command $command
     *
     * @return MetricsBuilder
     */
    public static function fromCommand(\WPMailSMTP\Vendor\Aws\CommandInterface $command) : \WPMailSMTP\Vendor\Aws\MetricsBuilder
    {
        return $command->getMetricsBuilder();
    }
    /**
     * Helper method for appending a metrics capture middleware into a
     * handler stack given. The middleware appended here is on top of the
     * build step.
     *
     * @param HandlerList $handlerList
     * @param $metric
     *
     * @return void
     */
    public static function appendMetricsCaptureMiddleware(\WPMailSMTP\Vendor\Aws\HandlerList $handlerList, $metric) : void
    {
        $handlerList->appendBuild(\WPMailSMTP\Vendor\Aws\Middleware::tap(function (\WPMailSMTP\Vendor\Aws\CommandInterface $command) use($metric) {
            self::fromCommand($command)->append($metric);
        }), 'metrics-capture-' . $metric);
    }
}
