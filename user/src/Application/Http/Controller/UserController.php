<?php

declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Converter\DtoConverter;
use App\Application\Factory\Command\SignUpUserCommandFactory;
use App\Application\Factory\Command\UpdateUserDataCommandFactory;
use App\Application\Http\Request\User\SignUpRequest;
use App\Application\Http\Request\User\UpdateUserDataRequest;
use App\Application\Service\ValidationService;
use App\Application\Utils\MessengerUtils;
use App\Domain\Common\Repository\PaginatedQueryResult;
use App\Domain\User\Entity\User;
use App\Domain\User\Query\FindUserQuery;
use App\Domain\User\Query\GetUsersQuery;
use App\Domain\User\UseCase\DeleteUserCommand;
use App\Domain\User\View\UserDetailedView;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Domain\User\Service\PermissionManager;
use App\Domain\User\Enum\RoleEnum;

/**
 * @Rest\Route(path="/secure/user")
 *
 * @OA\Tag(name="User")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @var MessageBusInterface
     */
    private $queryBus;

    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public function __construct(MessageBusInterface $queryBus, MessageBusInterface $commandBus)
    {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    /**
     * @Rest\Get(path="")
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", strict=true, default=15)
     * @Rest\QueryParam(name="offset", requirements="\d+", strict=true, default=0)
     *
     * @Rest\View
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return PaginatedQueryResult
     */
    public function getList(ParamFetcherInterface $paramFetcher): PaginatedQueryResult
    {
        $envelope = $this->queryBus->dispatch(new GetUsersQuery(
            (int)$paramFetcher->get('limit'),
            (int)$paramFetcher->get('offset')
        ));

        return MessengerUtils::getResultFromEnvelope($envelope);
    }

    /**
     * @Rest\Get(path="/{id}")
     *
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @Model(type=UserDetailedView::class)
     * )
     *
     * @Rest\View
     *
     * @param string $id
     * @return UserDetailedView
     */
    public function find(string $id): UserDetailedView
    {
        $result = MessengerUtils::getResultFromEnvelope(
            $this->queryBus->dispatch(new FindUserQuery($id))
        );
        if (!$result) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * @Rest\Post(path="")
     *
     * @IsGranted(attributes=RoleEnum::ROLE_ADMIN)
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
     *     @Model(type=UserDetailedView::class)
     * )
     *
     * @Rest\View
     *
     * @param Request $request
     * @param DtoConverter $converter
     * @param ValidationService $validationService
     * @return UserDetailedView
     */
    public function create(Request $request, DtoConverter $converter, ValidationService $validationService): UserDetailedView
    {
        $signUpRequest = $validationService->validate(
            $converter->convertToDto(SignUpRequest::class, $request->request->all())
        );

        $command = SignUpUserCommandFactory::createFromSignUpRequest($signUpRequest);

        $this->commandBus->dispatch($command);

        return MessengerUtils::getResultFromEnvelope(
            $this->queryBus->dispatch(new FindUserQuery($command->getId()))
        );
    }

    /**
     * @Rest\Put(path="/{id}")
     *
     * @IsGranted(attributes=PermissionManager::UPDATE_USER, subject="user")
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
     *     @Model(type=UserDetailedView::class)
     * )
     *
     * @Rest\View
     *
     * @param User $user
     * @param Request $request
     * @param DtoConverter $converter
     * @param ValidationService $validationService
     * @return UserDetailedView
     */
    public function update(
        User $user,
        Request $request,
        DtoConverter $converter,
        ValidationService $validationService
    ): UserDetailedView {
        $data = array_merge($request->request->all(), ['id' => $user->getId()]);
        $updatePersonalDataRequest = $validationService->validate($converter->convertToDto(
            UpdateUserDataRequest::class,
            $data
        ));

        $this->commandBus->dispatch(
            UpdateUserDataCommandFactory::createFromUpdateUserDataRequest($updatePersonalDataRequest)
        );

        return MessengerUtils::getResultFromEnvelope(
            $this->queryBus->dispatch(new FindUserQuery($user->getId()))
        );
    }

    /**
     * @Rest\Delete(path="/{id}")
     *
     * @IsGranted(attributes=PermissionManager::DELETE_USER, subject="user")
     *
     * @OA\Response(
     *     response=204,
     *     description="",
     * )
     *
     * @Rest\View
     *
     * @param User $user
     */
    public function delete(User $user): void
    {
        $this->commandBus->dispatch(new DeleteUserCommand($user->getId()));
    }
}
