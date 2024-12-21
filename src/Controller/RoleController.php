<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dto\Create\RoleCreateDto;
use App\Dto\Response\RoleResponseDto;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class RoleController extends AbstractController
{
    private RoleRepository $_roleRepostory;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->_roleRepostory = $roleRepository;
    }

    #[Route('/api/role/add', name: 'app_role_add')]
    public function addRole(#[MapRequestPayload] RoleCreateDto $role): JsonResponse
    {
        $result = $this->_roleRepostory->addRole($role);

        return $this->json($result);
    }

    #[Route('/api/role/findAll', methods:['GET'], name:'app_role_findAll')]
    public function findAll():JsonResponse
    {
        return $this->json($this->_roleRepostory->findAll());
    }
}
