<?php

namespace AppBundle\Listener;

use AppBundle\Exception\AppException;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /**
     * @var Logger $_logger
     */
    private $_logger;

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof AppException) {
            $this->_logger->err($exception->getLogMessage());
            return;
        }
        return;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->_logger = $logger;
    }
}
