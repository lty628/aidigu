<?php

namespace app\upload\libs\Events;

use app\upload\libs\Storge\File;
use app\upload\libs\Header\Request;
use app\upload\libs\Header\Response;

class UploadCreated extends BaseEvent
{
    /** @var string */
    public const NAME = 'tus-server.upload.created';

    /**
     * UploadCreatedEvent constructor.
     *
     * @param File     $file
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(File $file, Request $request, Response $response)
    {
        $this->file     = $file;
        $this->request  = $request;
        $this->response = $response;
    }
}
