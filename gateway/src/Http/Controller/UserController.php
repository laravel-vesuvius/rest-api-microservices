<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Converter\DtoConverter;
use App\Dto\User\UserDetailedDto;
use App\Dto\User\UserListDto;
use App\Facade\UserFacade;
use App\Http\Query\UserListQueryParams;
use App\Http\Request\SignUpRequest;
use App\Http\Request\UpdateUserDataRequest;
use App\Service\ValidationService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route(path="/user")
 *
 * @OA\Tag(name="User")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @var UserFacade
     */
    private $userFacade;

    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

    /**
     * @Rest\Get(path="")
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", strict=true, default=15)
     * @Rest\QueryParam(name="offset", requirements="\d+", strict=true, default=0)
     *
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @Model(type=UserListDto::class)
     * )
     *
     * @Rest\View
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return UserListDto
     */
    public function findAll(ParamFetcherInterface $paramFetcher): UserListDto
    {
        return $this->userFacade->findAll(
            new UserListQueryParams((int)$paramFetcher->get('limit'), (int)$paramFetcher->get('offset'))
        );
    }

    /**
     * @Rest\Get(path="/{id}")
     *
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @Model(type=UserDetailedDto::class)
     * )
     *
     * @Rest\View
     *
     * @param string $id
     * @return UserDetailedDto
     */
    public function find(string $id): UserDetailedDto
    {
        return $this->userFacade->find($id);
    }

    /**
     * @Rest\Post(path="")
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
    public function create(Request $request, DtoConverter $converter, ValidationService $validationService): UserDetailedDto
    {
        $signUpRequest = $validationService->validate(
            $converter->convertToDto(SignUpRequest::class, $request->request->all())
        );

        return $this->userFacade->create($signUpRequest);
    }

    /**
     * @Rest\Put(path="/{id}")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=UpdateUserDataRequest::class),
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
     * @param string $id
     * @param Request $request
     * @param DtoConverter $converter
     * @param ValidationService $validationService
     * @return UserDetailedDto
     */
    public function update(
        string $id,
        Request $request,
        DtoConverter $converter,
        ValidationService $validationService
    ): UserDetailedDto {
        $updateUserDataRequest = $validationService->validate(
            $converter->convertToDto(UpdateUserDataRequest::class, $request->request->all())
        );

        return $this->userFacade->update($id, $updateUserDataRequest);
    }

    /**
     * @Rest\Delete(path="/{id}")
     *
     * @OA\Response(
     *     response=204,
     *     description="",
     * )
     *
     * @Rest\View
     *
     * @param string $id
     */
    public function delete(string $id): void
    {
        $this->userFacade->delete($id);
    }
}
