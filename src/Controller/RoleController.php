<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dto\Create\RoleCreateDto;
use App\Interface\Repository\RoleRepositoryInterface;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class RoleController extends AbstractController
{
    private RoleRepositoryInterface $_roleRepostory;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->_roleRepostory = $roleRepository;
    }

    #[Route('/api/role/add', methods: ['POST'], name: 'app_role_add')]
    public function addRole(#[MapRequestPayload] RoleCreateDto $role): JsonResponse
    {
        $result = $this->_roleRepostory->createRole($role);

        return $this->json($result);
    }

    #[Route('/api/role/exists', methods: ['GET'], name: 'app_role_exists')]
    public function roleExists(Request $request): JsonResponse
    {
         $result = $this->getRequestQuery('name', $request);

        if(empty($result)){
            $result = $this->getRequestPayload('name', $request);

            if(!empty($result))
            {
                return $this->json($this->_roleRepostory->roleExists($result));
            }

            return $this->json('Identificador não pode ser null.');
        }

        return $this->json($this->_roleRepostory->roleExists($result));
    }

    #[Route('/api/role/update', methods: ['PUT'], name: 'app_role_update')]
    public function updateRole(Request $request, #[MapRequestPayload] RoleCreateDto $role): JsonResponse
    {
         $id = $this->getRequestQuery('id', $request);
        if(empty($id)) return $this->json('Identificador não pode ser null.');

        $this->_roleRepostory->updateRole($id, $role);

        return $this->json($role);
    }

    #[Route('/api/role/findById', methods: ['GET'], name: 'app_role_findById')]
    public function findRoleById(Request $request): JsonResponse
    {
        $result = $this->getRequestQuery('id', $request);

        if(empty($result)) return $this->json('Identificador não pode ser null.');

        return $this->json($this->_roleRepostory->findRoleById($result));
    }

    #[Route('/api/role/findByName', methods: ['GET'], name: 'app_role_findByName')]
    public function findRoleByName(Request $request): JsonResponse
    {
        $result = $this->getRequestQuery('name', $request);

        if(empty($result)) return $this->json('Identificador não pode ser null.');

        return $this->json($this->_roleRepostory->findRoleByName($result));
    }

    #[Route('/api/role/findAll', methods:['GET'], name:'app_role_findAll')]
    public function findAll():JsonResponse
    {
        return $this->json($this->_roleRepostory->findRoleAll());
    }

    #[Route('/api/role/delete', methods: ['DELETE'], name: 'app_role_delete')]
    public function deleteRole(Request $request): JsonResponse
    {
        $reuslt = $this->getRequestQuery('id', $request);

        if(empty($reuslt)) return $this->json('Identificador não pode ser null.');

        return $this->json($this->_roleRepostory->deleteRole($reuslt));
    }

    private function getRequestQuery(string $identifier, Request $request): ? string
    {
        $result = $request->query->get($identifier);

        if($result == null) return null;

        return $result;
    }

    private function getRequestPayload(string $identitifier, Request $request): string
    {
        $result = json_decode($request->getContent(), true);

        return $result[$identitifier] ?? null;       
    }
}
