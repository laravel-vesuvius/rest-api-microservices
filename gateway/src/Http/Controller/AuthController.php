<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Converter\DtoConverter;
use App\Dto\User\UserDetailedDto;
use App\Facade\AuthFacade;
use App\Facade\UserFacade;
use App\Http\Request\SignUpRequest;
use App\Service\ValidationService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route(path="/auth")
 *
 * @OA\Tag(name="Auth")
 */
class AuthController extends AbstractFOSRestController
{
    /**
     * @var AuthFacade
     */
    private $authFacade;

    /**
     * @var UserFacade
     */
    private $userFacade;

    public function __construct(AuthFacade $authFacade, UserFacade $userFacade)
    {
        $this->authFacade = $authFacade;
        $this->userFacade = $userFacade;
    }

    /**
     * @Rest\Post(path="/sign-in")
     *
     * @Rest\RequestParam(name="username", allowBlank=false, nullable=false, strict=true)
     * @Rest\RequestParam(name="password", allowBlank=false, nullable=false, strict=true)
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
     * @param ParamFetcherInterface $paramFetcher
     * @return array
     */
    public function signIn(ParamFetcherInterface $paramFetcher): array
    {
        return [
            'token' => $this->authFacade->signIn($paramFetcher->get('username'), $paramFetcher->get('password')),
        ];
    }

    /**
     * @Rest\Post(path="/sign-up")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=SignUpRequest::class),
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @Model(type=UserDetailedDto::class)
     * )
     *
     * @Rest\View
     *
     * @param Request $request
     * @param DtoConverter $converter
     * @param ValidationService $validationService
     * @return UserDetailedDto
     */
    public function signUp(Request $request, DtoConverter $converter, ValidationService $validationService): UserDetailedDto
    {
        $signUpRequest = $validationService->validate(
            $converter->convertToDto(SignUpRequest::class, $request->request->all())
        );

        return $this->userFacade->signUp($signUpRequest);
    }
}
