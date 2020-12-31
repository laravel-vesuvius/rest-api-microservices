<?php

declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Converter\DtoConverter;
use App\Application\Factory\Command\SignUpUserCommandFactory;
use App\Application\Http\Request\User\SignUpRequest;
use App\Application\Service\ValidationService;
use App\Application\Utils\MessengerUtils;
use App\Domain\User\Query\FindUserQuery;
use App\Domain\User\UseCase\CheckUserCredentialsCommand;
use App\Domain\User\View\UserDetailedView;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Rest\Route(path="/auth")
 *
 * @OA\Tag(name="Auth")
 */
class AuthController extends AbstractFOSRestController
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
    public function signUp(Request $request, DtoConverter $converter, ValidationService $validationService): UserDetailedView
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
     * @Rest\Post(path="/check-credentials")
     *
     * @Rest\RequestParam(name="username", allowBlank=false, nullable=false, strict=true)
     * @Rest\RequestParam(name="password", allowBlank=false, nullable=false, strict=true)
     *
     * @Rest\View
     *
     * @param ParamFetcherInterface $paramFetcher
     */
    public function checkCredentials(ParamFetcherInterface $paramFetcher): void
    {
        $this->commandBus->dispatch(
            new CheckUserCredentialsCommand($paramFetcher->get('username'), $paramFetcher->get('password'))
        );
    }
}
