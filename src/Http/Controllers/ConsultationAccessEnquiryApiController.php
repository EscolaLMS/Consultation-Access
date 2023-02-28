<?php

namespace EscolaLms\ConsultationAccess\Http\Controllers;

use EscolaLms\ConsultationAccess\Http\Controllers\Swagger\ConsultationAccessEnquiryApiSwagger;
use EscolaLms\ConsultationAccess\Http\Requests\ListConsultationAccessEnquiryRequest;
use EscolaLms\ConsultationAccess\Http\Resources\ConsultationAccessEnquiryResource;
use EscolaLms\ConsultationAccess\Http\Requests\CreateConsultationAccessEnquiryRequest;
use EscolaLms\ConsultationAccess\Services\Contracts\ConsultationAccessEnquiryServiceContract;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use Illuminate\Http\JsonResponse;

class ConsultationAccessEnquiryApiController extends EscolaLmsBaseController implements ConsultationAccessEnquiryApiSwagger
{
    private ConsultationAccessEnquiryServiceContract $service;

    public function __construct(ConsultationAccessEnquiryServiceContract $service)
    {
        $this->service = $service;
    }

    public function index(ListConsultationAccessEnquiryRequest $request): JsonResponse
    {
        $result = $this->service->findByUser($request->getCriteriaDto(), $request->getPaginationDto(), auth()->id());

        return $this->sendResponseForResource(ConsultationAccessEnquiryResource::collection($result));
    }

    public function create(CreateConsultationAccessEnquiryRequest $request): JsonResponse
    {
        $result = $this->service->create($request->toDto());

        return $this->sendResponseForResource(ConsultationAccessEnquiryResource::make($result), __('Created successfully'));
    }
}
