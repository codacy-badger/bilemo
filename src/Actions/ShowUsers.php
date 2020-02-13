<?php

namespace App\Actions;

use App\Domain\Helpers\PaginationHelper;
use App\Domain\Services\SerializerService;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Responder\JsonResponder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowUsers
 * @package App\Actions
 *
 * @Route("api/customers/{id}/users", name="api_show_users", methods={"GET"})
 */
final class ShowUsers
{

    /** @var UserRepository */
    protected $userRepo;

    /** @var SerializerService */
    protected $serializer;

    /** @var PaginationHelper */
    protected $paginationHelper;

    /**
     * ShowUsers constructor.
     * @param UserRepository $userRepo
     * @param SerializerService $serializer
     * @param PaginationHelper $paginationHelper
     */
    public function __construct(
        UserRepository $userRepo,
        SerializerService $serializer,
        PaginationHelper $paginationHelper
    ) {
        $this->userRepo = $userRepo;
        $this->serializer = $serializer;
        $this->paginationHelper = $paginationHelper;
    }

    /**
     * Show users of a customer
     *
     * @SWG\Response(
     *     response="200",
     *     description="Return all users of a customer.",
     *     @Model(type=User::class, groups={"showUser"})
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Return a 404 not found if the page parameter don't exist.",
     *     examples={"status": "404 Ressource introuvable", "message": "Liste introuvable !"}
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="Unique identifier of the customer.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="path",
     *     type="integer",
     *     description="Page of the list.",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="filter",
     *     in="path",
     *     type="string",
     *     description="Filter by slug or email of the user.",
     *     required=false
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param JsonResponder $responder
     * @param Customer $customer
     * @return Response
     */
    public function __invoke(Request $request, JsonResponder $responder, Customer $customer): Response
    {
        $page = $this->paginationHelper->checkPage(
            $request,
            $this->userRepo->findByCustomer($customer),
            User::LIMIT_PER_PAGE
        );

        if (is_array($page)) {
            return $responder($page, Response::HTTP_NOT_FOUND);
        }

        $users = $this->userRepo->findAllUser($page, $customer, $request->query->get('filter'));
        $data = $this->serializer->serializer(
            $users,
            [
                'groups' => ['showUser', 'listUser'],
                'page' => $page,
                'customer' => $customer
            ]
        );

        return $responder($data, Response::HTTP_OK);
    }
}
