<?php

namespace app\upload\libs\Events;

use app\upload\libs\Storge\File;
use app\upload\libs\Header\Request;
use app\upload\libs\Header\Response;
use Symfony\Component\EventDispatcher\Event;

class BaseEvent extends Event
{
    /** @var File */
    protected $file;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /**
     * Get file.
     *
     * @return File
     */
    public function getFile() : File
    {
        return $this->file;
    }

    /**
     * Get request.
     *
     * @return Request
     */
    public function getRequest() : Request
    {
        return $this->request;
    }

    /**
     * Get response.
     *
     * @return Response
     */
    public function getResponse() : Response
    {
        return $this->response;
    }
}
