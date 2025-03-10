<?php

namespace WPMailSMTP\Vendor\Aws\Multipart;

use WPMailSMTP\Vendor\Aws\AwsClientInterface as Client;
use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Aws\CommandPool;
use WPMailSMTP\Vendor\Aws\Exception\AwsException;
use WPMailSMTP\Vendor\Aws\Exception\MultipartUploadException;
use WPMailSMTP\Vendor\Aws\Result;
use WPMailSMTP\Vendor\Aws\ResultInterface;
use WPMailSMTP\Vendor\GuzzleHttp\Promise;
use WPMailSMTP\Vendor\GuzzleHttp\Promise\PromiseInterface;
use InvalidArgumentException as IAE;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * Encapsulates the execution of a multipart upload to S3 or Glacier.
 *
 * @internal
 */
abstract class AbstractUploadManager implements \WPMailSMTP\Vendor\GuzzleHttp\Promise\PromisorInterface
{
    const DEFAULT_CONCURRENCY = 5;
    /** @var array Default values for base multipart configuration */
    private static $defaultConfig = ['part_size' => null, 'state' => null, 'concurrency' => self::DEFAULT_CONCURRENCY, 'prepare_data_source' => null, 'before_initiate' => null, 'before_upload' => null, 'before_complete' => null, 'exception_class' => \WPMailSMTP\Vendor\Aws\Exception\MultipartUploadException::class];
    /** @var Client Client used for the upload. */
    protected $client;
    /** @var array Configuration used to perform the upload. */
    protected $config;
    /** @var array Service-specific information about the upload workflow. */
    protected $info;
    /** @var PromiseInterface Promise that represents the multipart upload. */
    protected $promise;
    /** @var UploadState State used to manage the upload. */
    protected $state;
    /**
     * @param Client $client
     * @param array  $config
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\AwsClientInterface $client, array $config = [])
    {
        $this->client = $client;
        $this->info = $this->loadUploadWorkflowInfo();
        $this->config = $config + self::$defaultConfig;
        $this->state = $this->determineState();
    }
    /**
     * Returns the current state of the upload
     *
     * @return UploadState
     */
    public function getState()
    {
        return $this->state;
    }
    /**
     * Upload the source using multipart upload operations.
     *
     * @return Result The result of the CompleteMultipartUpload operation.
     * @throws \LogicException if the upload is already complete or aborted.
     * @throws MultipartUploadException if an upload operation fails.
     */
    public function upload()
    {
        return $this->promise()->wait();
    }
    /**
     * Upload the source asynchronously using multipart upload operations.
     *
     * @return PromiseInterface
     */
    public function promise() : \WPMailSMTP\Vendor\GuzzleHttp\Promise\PromiseInterface
    {
        if ($this->promise) {
            return $this->promise;
        }
        return $this->promise = \WPMailSMTP\Vendor\GuzzleHttp\Promise\Coroutine::of(function () {
            // Initiate the upload.
            if ($this->state->isCompleted()) {
                throw new \LogicException('This multipart upload has already ' . 'been completed or aborted.');
            }
            if (!$this->state->isInitiated()) {
                // Execute the prepare callback.
                if (\is_callable($this->config["prepare_data_source"])) {
                    $this->config["prepare_data_source"]();
                }
                $result = (yield $this->execCommand('initiate', $this->getInitiateParams()));
                $this->state->setUploadId($this->info['id']['upload_id'], $result[$this->info['id']['upload_id']]);
                $this->state->setStatus(\WPMailSMTP\Vendor\Aws\Multipart\UploadState::INITIATED);
            }
            // Create a command pool from a generator that yields UploadPart
            // commands for each upload part.
            $resultHandler = $this->getResultHandler($errors);
            $commands = new \WPMailSMTP\Vendor\Aws\CommandPool($this->client, $this->getUploadCommands($resultHandler), ['concurrency' => $this->config['concurrency'], 'before' => $this->config['before_upload']]);
            // Execute the pool of commands concurrently, and process errors.
            (yield $commands->promise());
            if ($errors) {
                throw new $this->config['exception_class']($this->state, $errors);
            }
            // Complete the multipart upload.
            (yield $this->execCommand('complete', $this->getCompleteParams()));
            $this->state->setStatus(\WPMailSMTP\Vendor\Aws\Multipart\UploadState::COMPLETED);
        })->otherwise($this->buildFailureCatch());
    }
    private function transformException($e)
    {
        // Throw errors from the operations as a specific Multipart error.
        if ($e instanceof \WPMailSMTP\Vendor\Aws\Exception\AwsException) {
            $e = new $this->config['exception_class']($this->state, $e);
        }
        throw $e;
    }
    private function buildFailureCatch()
    {
        if (\interface_exists("Throwable")) {
            return function (\Throwable $e) {
                return $this->transformException($e);
            };
        } else {
            return function (\Exception $e) {
                return $this->transformException($e);
            };
        }
    }
    protected function getConfig()
    {
        return $this->config;
    }
    /**
     * Provides service-specific information about the multipart upload
     * workflow.
     *
     * This array of data should include the keys: 'command', 'id', and 'part_num'.
     *
     * @return array
     */
    protected abstract function loadUploadWorkflowInfo();
    /**
     * Determines the part size to use for upload parts.
     *
     * Examines the provided partSize value and the source to determine the
     * best possible part size.
     *
     * @throws \InvalidArgumentException if the part size is invalid.
     *
     * @return int
     */
    protected abstract function determinePartSize();
    /**
     * Uses information from the Command and Result to determine which part was
     * uploaded and mark it as uploaded in the upload's state.
     *
     * @param CommandInterface $command
     * @param ResultInterface  $result
     */
    protected abstract function handleResult(\WPMailSMTP\Vendor\Aws\CommandInterface $command, \WPMailSMTP\Vendor\Aws\ResultInterface $result);
    /**
     * Gets the service-specific parameters used to initiate the upload.
     *
     * @return array
     */
    protected abstract function getInitiateParams();
    /**
     * Gets the service-specific parameters used to complete the upload.
     *
     * @return array
     */
    protected abstract function getCompleteParams();
    /**
     * Based on the config and service-specific workflow info, creates a
     * `Promise` for an `UploadState` object.
     */
    private function determineState() : \WPMailSMTP\Vendor\Aws\Multipart\UploadState
    {
        // If the state was provided via config, then just use it.
        if ($this->config['state'] instanceof \WPMailSMTP\Vendor\Aws\Multipart\UploadState) {
            return $this->config['state'];
        }
        // Otherwise, construct a new state from the provided identifiers.
        $required = $this->info['id'];
        $id = [$required['upload_id'] => null];
        unset($required['upload_id']);
        foreach ($required as $key => $param) {
            if (!$this->config[$key]) {
                throw new \InvalidArgumentException('You must provide a value for "' . $key . '" in ' . 'your config for the MultipartUploader for ' . $this->client->getApi()->getServiceFullName() . '.');
            }
            $id[$param] = $this->config[$key];
        }
        $state = new \WPMailSMTP\Vendor\Aws\Multipart\UploadState($id);
        $state->setPartSize($this->determinePartSize());
        return $state;
    }
    /**
     * Executes a MUP command with all of the parameters for the operation.
     *
     * @param string $operation Name of the operation.
     * @param array  $params    Service-specific params for the operation.
     *
     * @return PromiseInterface
     */
    protected function execCommand($operation, array $params)
    {
        // Create the command.
        $command = $this->client->getCommand($this->info['command'][$operation], $params + $this->state->getId());
        // Execute the before callback.
        if (\is_callable($this->config["before_{$operation}"])) {
            $this->config["before_{$operation}"]($command);
        }
        // Execute the command asynchronously and return the promise.
        return $this->client->executeAsync($command);
    }
    /**
     * Returns a middleware for processing responses of part upload operations.
     *
     * - Adds an onFulfilled callback that calls the service-specific
     *   handleResult method on the Result of the operation.
     * - Adds an onRejected callback that adds the error to an array of errors.
     * - Has a passedByRef $errors arg that the exceptions get added to. The
     *   caller should use that &$errors array to do error handling.
     *
     * @param array $errors Errors from upload operations are added to this.
     *
     * @return callable
     */
    protected function getResultHandler(&$errors = [])
    {
        return function (callable $handler) use(&$errors) {
            return function (\WPMailSMTP\Vendor\Aws\CommandInterface $command, ?\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request = null) use($handler, &$errors) {
                return $handler($command, $request)->then(function (\WPMailSMTP\Vendor\Aws\ResultInterface $result) use($command) {
                    $this->handleResult($command, $result);
                    return $result;
                }, function (\WPMailSMTP\Vendor\Aws\Exception\AwsException $e) use(&$errors) {
                    $errors[$e->getCommand()[$this->info['part_num']]] = $e;
                    return new \WPMailSMTP\Vendor\Aws\Result();
                });
            };
        };
    }
    /**
     * Creates a generator that yields part data for the upload's source.
     *
     * Yields associative arrays of parameters that are ultimately merged in
     * with others to form the complete parameters of a  command. This can
     * include the Body parameter, which is a limited stream (i.e., a Stream
     * object, decorated with a LimitStream).
     *
     * @param callable $resultHandler
     *
     * @return \Generator
     */
    protected abstract function getUploadCommands(callable $resultHandler);
}
