<?php
namespace Jihe\Infrastructure\Storage;

class AliossService
    implements StorageServiceContract
{
    /**
     * OSS Client
     * @var \Aliyun\OSS\OSSClient
     */
    private $client;

    /**
     * default bucket name
     * @var string
     */
    private $bucket;

    /**
     * base url for accessing stored objects(typically, files)
     * @var string
     */
    private $baseUrl;

    /**
     * base image url for accessing stored objects(typically, files)
     * @var string
     */
    private $baseImageUrl;

    /**
     *
     * @param OSSClient $client    client to communicat with AliOSS server
     * @param array $config        configurations.
     *                             - bucket   default buck name (defaults to "default")
     *                             - base_url         base url for accessing stored objects
     *                             - base_image_url   base image url for accessing stored objects
     */
    public function __construct(OSSClient $client, array $config)
    {
        $this->client       = $client;
        $this->bucket       = array_get($config, 'bucket', 'default');
        $this->baseUrl      = rtrim($config['base_url'], '/');
        $this->baseImageUrl = rtrim($config['base_image_url'], '/');
    }

    /**
     * (non-PHPdoc)
     * @see \Jihe\Contracts\Services\Storage\StorageService::isTmp()
     */
    public function isTmp($identifier)
    {
        if (empty($identifier)) {
            return false;
        }

        $tmpBaseUrl = $this->baseUrl . '/tmp/';
        $tmpBaseImageUrl = $this->baseImageUrl . '/tmp/';

        return 0 === strncasecmp($identifier, $tmpBaseUrl, strlen($tmpBaseUrl)) ||
        0 === strncasecmp($identifier, $tmpBaseImageUrl, strlen($tmpBaseImageUrl));
    }

    /**
     * {@inheritdoc}
     * @see \Jihe\Contracts\Services\Storage\StorageService::storeTmp()
     */
    public function storeTmp($file, array $options = [])
    {
        // add 'tmp/' prefix to identity
        $options['id'] = 'tmp/' . $this->createFileIdentity($file, $options);

        return $this->store($file, $options);
    }

    /**
     * {@inheritdoc}
     * @param array $options    extended options are:
     *                          - identity        provides an identity so that it will not
     *                                            be generated
     *
     * @see \Jihe\Contracts\Services\Storage\StorageService::copy()
     */
    public function copy($source, array $options = [])
    {
        $source = $this->guessObjectName($source);
        $identifier = $this->createFileIdentity($source, $options);

        $result = $this->client->copyObject([
            OSSOptions::SOURCE_BUCKET => $this->getBucket(array_get($options, 'source_bucket')),
            OSSOptions::SOURCE_KEY    => $source,
            OSSOptions::DEST_BUCKET   => $this->getBucket(array_get($options, 'dest_bucket')),
            OSSOptions::DEST_KEY      => $identifier,
        ]);

        // all info is requested to be returned
        if (array_get($options, 'id_only', true))  {
            return $this->getPortal($identifier,
                [
                    'bucket' => array_get($options, 'bucket'),
                    'as_image' => array_get($options, 'as_image', false),
                ]);
        }

        /** @var $result \Aliyun\OSS\Models\PutObjectResult */
        return [
            'id'   => $this->getPortal($identifier,
                [
                    'bucket' => array_get($options, 'bucket'),
                    'as_image' => array_get($options, 'as_image', false),
                ]),
            'etag' => $result->getETag()
        ];
    }

    /**
     * {@inheritdoc}
     * @param array $options   extended options are:
     *                         - identity        provides an identity so that it will not
     *                                           be generated
     *
     * @see \Jihe\Contracts\Services\Storage\StorageService::store()
     */
    public function store($file, array $options = [])
    {
        $identifier = $this->createFileIdentity($file, $options);

        // putObject() returns an instance of \Aliyun\OSS\Models\PutObjectResult, which
        // contains the eTag of the file
        $result = $this->client->putObject([
            OSSOptions::BUCKET         => $this->getBucket(array_get($options, 'bucket')),
            OSSOptions::KEY            => $identifier,
            OSSOptions::CONTENT        => fopen($file, 'r'),
            OSSOptions::CONTENT_LENGTH => filesize($file),
            OSSOptions::CONTENT_TYPE   => $this->guessMime($file, $options),
        ]);

        // all info is requested to be returned
        if (array_get($options, 'id_only', true))  {
            return $this->getPortal($identifier,
                [
                    'bucket'   => array_get($options, 'bucket'),
                    'as_image' => array_get($options, 'as_image', false),
                ]);
        }

        /** @var $result \Aliyun\OSS\Models\CopyObjectResult */
        return [
            'id'   => $this->getPortal($identifier,
                [
                    'bucket' => array_get($options, 'bucket'),
                    'as_image' => array_get($options, 'as_image', false),
                ]),
            'etag' => $result->getETag()
        ];
    }

    private function guessMime($file, $options = [])
    {
        if ($mime = array_get($options, 'mime')) {
            return $mime;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file);
        finfo_close($finfo);

        return $mime;
    }

    /**
     * {@inheritdoc}
     * @see \Jihe\Contracts\Services\Storage\StorageService::remove()
     */
    public function remove($identifier, array $options = [])
    {
        $identifier = $this->guessObjectName($identifier);

        if (array_get($options, 'inspect') &&
            !$this->has($identifier, [ 'bucket' => array_get($options, 'bucket') ])) {
            throw new \Exception('no such file');
        }

        // deleteObject() always succeeds, even if the object to be deleted
        // does not exist
        $this->client->deleteObject([
            OSSOptions::BUCKET => $this->getBucket(array_get($options, 'bucket')),
            OSSOptions::KEY    => $identifier,
        ]);
    }

    /**
     * get portal of the file (identified by its identifier). typically it could
     * be an http url or something alike.
     *
     * @param string $identifier identifier of the file
     * @param array $options     options
     *                            - inspect   when set to true(default false), no inspection
     *                                        will be performed to check the file's existence.
     *                            - bucket    (optional) which bucket to find the file
     *                            - as_image  when set to as_image(default false),
     *                                        use base url of oss, otherwise use url
     *                                        of cdn(with image process service)
     *
     * @return string          portal url
     * @throws \Exception
     */
    private function getPortal($identifier, array $options = [])
    {
        if (array_get($options, 'inspect') &&
            !$this->has($identifier, [ 'bucket' => array_get($options, 'bucket') ])) {
            throw new \Exception('no such file');
        }

        if (array_get($options, 'as_image', false)) {
            return $this->baseImageUrl . '/' . $identifier;
        }
        return $this->baseUrl . '/' . $identifier;
    }

    private function guessObjectName($identifier)
    {
        if (strpos($identifier, $this->baseUrl) === 0) {
            return ltrim(str_replace($this->baseUrl, '', $identifier), '/');
        }

        return ltrim(str_replace($this->baseImageUrl, '', $identifier), '/');
    }

    /**
     * {@inheritdoc}
     * @see \Jihe\Contracts\Services\Storage\StorageService::get()
     */
    public function get($identifier, array $options = [])
    {
        $identifier = $this->guessObjectName($identifier);

        try {
            /* @var $object \Aliyun\OSS\Models\OSSObject */
            $object = $this->client->getObject([
                OSSOptions::BUCKET => $this->getBucket(array_get($options, 'bucket')),
                OSSOptions::KEY    => $identifier,
            ]);

            // get metadata if requested
            $metadata = null;
            if (array_get($options, 'metadata', false)) {
                $metadata = $object->getMetadata();
            }

            // get content
            if (array_get($options, 'content', true)) {
                $content = $object->getObjectContent();

                if (!$metadata) {       // no metadata (content only),
                    return $content;    // return content as stream
                }

                // both content and metadata are requested
                return [
                    'content' => $content,
                    'metadata' => $metadata,
                ];
            } else {
                if ($metadata) {  // metadata only
                    return $metadata;
                }

                throw new \InvalidArgumentException('content and metadata cannot be false meanwhile');
            }
        } catch (OSSException $ex) {
            // if no such key in OSS, suppress the exception
            if ($ex->getErrorCode() == 'NoSuchKey') {
                if (array_get($options, 'null_for_nonexistence', true)) {
                    return null;
                }
            }

            throw $ex;
        }
    }

    private function has($identifier, $bucket = null)
    {
        try {
            $this->client->getObjectMetadata([
                OSSOptions::BUCKET => $this->getBucket($bucket),
                OSSOptions::KEY    => $identifier,
            ]);

            return true;
        } catch (OSSException $ex) {
            // if no such key in OSS, suppress the exception
            if ($ex->getErrorCode() == 'NoSuchKey') {
                return false;
            }

            throw $ex;
        }
    }

    // get bucket, use defualt bucket if given bucket is empty
    private function getBucket($bucket)
    {
        return $bucket ?: $this->bucket;
    }

    // create identity for a file to be uploaded
    private function createFileIdentity($file, $options)
    {
        // check whether object identifier is provided or not
        if (array_key_exists('id', $options)) {
            return $options['id'];
        }

        // find file extension
        $ext = $this->guessFileFormat($file, $options);
        // sane extension to ensure it starts with '.'.
        $ext  = $ext ? ('.' . ltrim($ext, '.')) : $ext;

        return implode('/', [
            date('Ymd'),
            $this->generateUniqueFilename($file, $ext)
        ]);
    }

    private function guessFileFormat($file, array $options)
    {
        $ext = array_key_exists('ext', $options) ? $options['ext']
            : pathinfo($file, PATHINFO_EXTENSION);
        if ($ext) {
            return $ext;
        }

        $mime = $this->guessMime($file);

        return $this->mapFileFormat($mime);
    }

    private function mapFileFormat($mime)
    {
        $parts = explode('/', $mime);

        // we could map it, but it's not that necessary for current
        // simply return the second part
        return $parts[1];
    }

    private function generateUniqueFilename($file, $ext)
    {
        return substr(date('YmdHisu'), 0, -6) .  // resolution in microsecond. date('u') suffixes
        // with 6 zeros, so eliminate it
        StringUtil::quickRandom(6, '0123456789') .  // 6 random numbers
        $ext;
    }
}