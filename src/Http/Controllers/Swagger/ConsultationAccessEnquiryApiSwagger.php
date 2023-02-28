<?php

namespace EscolaLms\ConsultationAccess\Http\Controllers\Swagger;

use EscolaLms\ConsultationAccess\Http\Requests\CreateConsultationAccessEnquiryRequest;
use EscolaLms\ConsultationAccess\Http\Requests\ListConsultationAccessEnquiryRequest;
use Illuminate\Http\JsonResponse;

interface ConsultationAccessEnquiryApiSwagger
{
    /**
     * @OA\Get(
     *      path="/api/consultation-access-enquiries",
     *     summary="Get my consultation access enquiries",
     *     tags={"Consultation Access"},
     *      security={
     *          {"passport": {}},
     *      },
     *     @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="limit",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="consultation_id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="number",
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="status",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"pending", "approved"}
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfull operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="success",
     *                      type="boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/ConsultationAccessEnquiryResource")
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(ListConsultationAccessEnquiryRequest $request): JsonResponse;

    /**
     * @OA\Post(
     *      path="/api/consultation-access-enquiries",
     *      summary="Store a newly Consultation Access Enqiury",
     *      tags={"Consultation Access"},
     *      description="Store Consultation Access Enqiury",
     *      security={
     *          {"passport": {}},
     *      },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/CreateConsultationAccessEnquiryRequest")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successfull operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="success",
     *                      type="boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/ConsultationAccessEnquiryResource"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function create(CreateConsultationAccessEnquiryRequest $request): JsonResponse;
}
