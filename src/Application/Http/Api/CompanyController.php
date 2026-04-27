<?php

declare(strict_types=1);

namespace App\Application\Http\Api;

use App\Application\Cqs\Company\Query\GetCompanyByInnQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController
{
    public function __construct(private GetCompanyByInnQuery $query)
    {
    }

    #[Route('/api/companies/by-inn/{inn}', methods: ['GET'])]
    public function __invoke(string $inn): JsonResponse
    {
        return new JsonResponse(
            $this->query->execute($inn)->toArray(),
            Response::HTTP_OK
        );
    }
}
