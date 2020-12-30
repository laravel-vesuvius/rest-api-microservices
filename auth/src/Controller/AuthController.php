<?php

declare(strict_types=1);

namespace App\Controller;

use App\Converter\DtoConverter;
use App\Dto\UserDataDto;
use App\Exception\ValidationHttpException;
use App\Manager\TokenManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class AuthController extends AbstractFOSRestController
{
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct(TokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Rest\Post(path="/generate-token")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=UserDataDto::class),
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(property="token", type="string")
     *     )
     * )
     *
     * @Rest\View
     *
     * @param Request $request
     * @param DtoConverter $converter
     * @param ValidatorInterface $validator
     * @return array
     */
    public function generateToken(Request $request, DtoConverter $converter, ValidatorInterface $validator): array
    {
        $dto = $converter->convertToDto(UserDataDto::class, $request->request->all());
        $violations = $validator->validate($dto);
        if ($violations->count()) {
            throw new ValidationHttpException($violations);
        }

        return ['token' => $this->tokenManager->encode($dto)];
    }

    /**
     * @Rest\Post(path="/authenticate")
     *
     * @Rest\RequestParam(name="token", allowBlank=false, nullable=false, strict=true)
     *
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @Model(type=UserDataDto::class)
     * )
     *
     * @Rest\View
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return UserDataDto
     */
    public function authenticate(ParamFetcherInterface $paramFetcher): UserDataDto
    {
        return $this->tokenManager->decode($paramFetcher->get('token'));
    }
}
