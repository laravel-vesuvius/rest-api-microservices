<?php

declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Utils\MessengerUtils;
use App\Domain\User\Query\FindUserQuery;
use App\Domain\User\View\UserDetailedView;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

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

    public function getList()
    {

    }

    /**
     * @Rest\Get(path="/{id}")
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

    public function create()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
