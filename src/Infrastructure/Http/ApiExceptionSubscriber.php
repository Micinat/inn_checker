<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\Company\Exception\CompanyNotFoundException;
use App\Domain\Company\Exception\InvalidInnException;
use App\Infrastructure\DaData\Exception\DaDataUnavailableException;
use App\Infrastructure\DaData\Exception\UnexpectedDaDataResponseException;
use App\Infrastructure\Doctrine\Exception\DatabaseWriteException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof InvalidInnException) {
            $event->setResponse(
                $this->createResponse(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'invalid_inn',
                    $exception->getMessage()
                )
            );

            return;
        }

        if ($exception instanceof CompanyNotFoundException) {
            $event->setResponse(
                $this->createResponse(
                    Response::HTTP_NOT_FOUND,
                    'company_not_found',
                    'Company with provided INN was not found.'
                )
            );

            return;
        }

        if ($exception instanceof DaDataUnavailableException
            || $exception instanceof UnexpectedDaDataResponseException) {
            $event->setResponse(
                $this->createResponse(
                    Response::HTTP_BAD_GATEWAY,
                    'dadata_unavailable',
                    'DaData service is unavailable.'
                )
            );

            return;
        }

        if ($exception instanceof DatabaseWriteException) {
            $event->setResponse(
                $this->createResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'database_error',
                    'Failed to persist company data.'
                )
            );
        }
    }

    private function createResponse(int $statusCode, string $type, string $message): JsonResponse
    {
        return new JsonResponse(
            [
                'type' => $type,
                'message' => $message,
            ],
            $statusCode
        );
    }
}
