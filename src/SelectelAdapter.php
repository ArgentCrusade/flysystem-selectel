<?php

namespace ArgentCrusade\Flysystem\Selectel;

use League\Flysystem\Config;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use ArgentCrusade\Selectel\CloudStorage\Contracts\ContainerContract;
use ArgentCrusade\Selectel\CloudStorage\Exceptions\FileNotFoundException;
use ArgentCrusade\Selectel\CloudStorage\Exceptions\UploadFailedException;
use ArgentCrusade\Selectel\CloudStorage\Exceptions\ApiRequestFailedException;

class SelectelAdapter implements AdapterInterface
{
    use NotSupportingVisibilityTrait;

    /**
     * Storage container.
     *
     * @var \ArgentCrusade\Selectel\CloudStorage\Contracts\ContainerContract
     */
    protected $container;

    /**
     * Create new instance.
     *
     * @param \ArgentCrusade\Selectel\CloudStorage\Contracts\ContainerContract $container
     */
    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    /**
     * Loads file from container.
     *
     * @param string $path Path to file.
     *
     * @return \ArgentCrusade\Selectel\CloudStorage\Contracts\FileContract
     */
    protected function getFile($path)
    {
        return $this->container->files()->find($path);
    }

    /**
     * Transforms internal files array to Flysystem-compatible one.
     *
     * @param array $files Original Selectel's files array.
     *
     * @return array
     */
    protected function transformFiles($files)
    {
        $result = [];

        foreach ($files as $file) {
            $result[] = [
                'type' => $file['content_type'] === 'application/directory' ? 'dir' : 'file',
                'path' => $file['name'],
                'size' => intval($file['bytes']),
                'timestamp' => strtotime($file['last_modified']),
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return $this->container->files()->exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        try {
            $file = $this->getFile($path);
        } catch (FileNotFoundException $e) {
            return false;
        }

        $contents = $file->read();

        return compact('contents');
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        try {
            $file = $this->getFile($path);
        } catch (FileNotFoundException $e) {
            return false;
        }

        $stream = $file->readStream();

        rewind($stream);

        return compact('stream');
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        $files = $this->container->files()->withPrefix($directory)->get();
        $result = $this->transformFiles($files);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        $files = $this->listContents($path);

        return isset($files[0]) ? $files[0] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        return $this->writeToContainer('String', $path, $contents);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->writeToContainer('Stream', $path, $resource);
    }

    /**
     * Writes string or stream to container.
     *
     * @param string          $type    Upload type
     * @param string          $path    File path
     * @param string|resource $payload String content or Stream resource
     *
     * @return array|bool
     */
    protected function writeToContainer($type, $path, $payload)
    {
        try {
            $this->container->{'uploadFrom'.$type}($path, $payload);
        } catch (UploadFailedException $e) {
            return false;
        }

        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        try {
            $this->getFile($path)->rename($newpath);
        } catch (ApiRequestFailedException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        try {
            $this->getFile($path)->copy($newpath);
        } catch (ApiRequestFailedException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        try {
            $this->getFile($path)->delete();
        } catch (ApiRequestFailedException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($path)
    {
        try {
            $this->container->deleteDir($path);
        } catch (ApiRequestFailedException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        try {
            $this->container->createDir($dirname);
        } catch (ApiRequestFailedException $e) {
            return false;
        }

        return $this->getMetadata($dirname);
    }

    /**
     * Get full URL to given path.
     *
     * @param string $path = ''
     *
     * @return string
     */
    public function getUrl($path = '')
    {
        return $this->container->url($path);
    }
}
