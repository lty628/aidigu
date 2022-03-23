<?php
namespace app\upload\libs\Storge;

use app\upload\libs\Config;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Aws\S3\S3Client;
use app\upload\libs\Storge\File,
    app\upload\libs\Cache\Cacheable,
    app\upload\libs\Exception;

class S3File
extends File
{
    const PREFIX = "";

    const UPLOAD_MIN_SIZE = 5242880;

    const PARTS = "Parts";
    const UPLOAD_ID = "UploadId";
    const FINAL_FILE_KEY = "FinalFileKey";

    protected $s3Adapter;

    protected $uploadId;

    /**
     * File constructor.
     *
     * @param string|null    $name
     * @param Cacheable|null $cache
     */
    public function __construct(string $name = null, Cacheable $cache = null)
    {
        $this->s3Adapter = $this->getS3Adapter();
        $this->name  = $name;
        $this->cache = $cache;
    }

    protected function getS3Adapter()
    {
        $s3Config = Config::get('storge.S3FileConfig');
        $S3client = new S3Client($s3Config);
        // 用户存储路径
        return new AwsS3Adapter($S3client, $s3Config['Bucket'], $s3Config['Path']);
    }

    protected function getS3() : AwsS3Adapter
    {
        return $this->s3Adapter;
    }

    protected function getRedis() : Predis
    {
        return $this->redis;
    }

    public function upload(int $totalBytes) : int
    {
        if ($this->offset === $totalBytes) {
            return $this->offset;
        }

        $key    = $this->getKey();
        $input  = $this->open($this->getInputStream(), self::READ_BINARY);

        try {
            // check size of upload and get output stream
            $output = tmpfile();
            $bytes = 0;
            while ( ! feof($input)) {
                $data  = $this->read($input, self::CHUNK_SIZE);
                $bytes += $this->write($output, $data, self::CHUNK_SIZE);
            }

            // check min multipart size if is not last part
            if($bytes + $this->offset < $totalBytes && $bytes < self::UPLOAD_MIN_SIZE)
                throw new \Exception(sprintf("Your proposed upload is smaller than the minimum allowed size. (Proposal size: %s, MinSizeAllowed: %s).", $bytes, self::UPLOAD_MIN_SIZE));;

            // check what is next part
            $part = $this->getNextPart($key);

            // create s3 multipart
            if($this->offset == 0) {
                $uploadId = $this->createUpload($key);
            }

            $etag = $this->setUpload($key, $part, $output, $bytes);

            $this->offset += $bytes;

            $this->cache->set($key, ['offset' => $this->offset]);

            if ($this->offset > $totalBytes) {
                throw new Exception\OutOfRangeException('The uploaded file is corrupt.');
            }

        } finally {
            $this->close($input);
            $this->close($output);
        }

        if ($this->offset === $totalBytes) {
            $this->completeUpload($key);
        }

        return $this->offset;
    }

    protected function createUpload(string $key)
    {
        if($this->getS3()->has($this->S3key($key)))
            $this->getS3()->delete($this->S3key($key));

        $result = $this->getS3()->getClient()->createMultipartUpload([
            'Bucket'       => $this->getS3()->getBucket(),
            'Key'          => $this->getS3()->applyPathPrefix($this->S3key($key)),
            'StorageClass' => 'REDUCED_REDUNDANCY',
            'Metadata'     => []
        ]);

        $this->cache->set($key, ["key" => $key]);
        $this->setUploadId($key, $result['UploadId']);

        return $result['UploadId'];
    }

    public function setUpload(string $key, int $part, $stream, $filesize)
    {
        $uploadId = $this->getUploadId($key);

        fseek($stream, 0);

        $result = $this->getS3()->getClient()->uploadPart([
            'Bucket'       => $this->getS3()->getBucket(),
            'Key'          => $this->getS3()->applyPathPrefix($this->S3key($key)),
            'UploadId'     => $uploadId,
            'PartNumber'   => $part,
            'Body'         => fread($stream, $filesize),
        ]);

        $this->setLastPart($key, $part, $result['ETag']);

        return $result['ETag'];
    }

    public function completeUpload($key)
    {
        $data = $this->cache->get($key);

        $uploadId = $data['UploadId'];
        $parts = $data['Parts'];

        $result = $this->getS3()->getClient()->completeMultipartUpload([
            'Bucket'        => $this->getS3()->getBucket(),
            'Key'           => $this->getS3()->applyPathPrefix($this->S3key($key)),
            'UploadId'      => $uploadId,
            'MultipartUpload'    => ['Parts' => $parts],
        ]);

        $this->cache->set($key, [self::FINAL_FILE_KEY => $this->S3key($key)]);
    }

    protected function setLastPart($key, $part, $etag)
    {
        $parts = $this->getParts($key);
        $parts[$part] = [
            'PartNumber' => $part,
            'ETag' => $etag,
        ];

        $this->cache->set($key, [self::PARTS => $parts]);
    }

    protected function getParts($key)
    {
        $data = $this->cache->get($key);

        return isset($data[self::PARTS])? $data[self::PARTS]  : [];
    }

    protected function getLastPart($key) : int
    {
        $parts = $this->getParts($key);

        return sizeof($parts);
    }

    protected function getNextPart($key)
    {
        return $this->getLastPart($key) + 1;
    }

    protected function setUploadId($key, string $uploadId)
    {
        $this->cache->set($key, [self::UPLOAD_ID => $uploadId]);
    }

    protected function getUploadId($key): string
    {
        $data = $this->cache->get($key);

        return isset($data[self::UPLOAD_ID])? $data[self::UPLOAD_ID] : "";
    }

    protected function S3key($key) : string
    {
        return sprintf("%s/%s.%s", self::PREFIX, $key, $this->getExtension());
    }

    protected function getExtension() : string
    {
        return pathinfo($this->getName(), PATHINFO_EXTENSION);
    }

}
