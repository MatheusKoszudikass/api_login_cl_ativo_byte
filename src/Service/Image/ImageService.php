<?php

namespace App\Service\Image;
use App\Dto\Create\ImageCreateDto;
use App\Entity\Enum\TypeEntitiesEnum;
use App\Entity\Enum\TypeImageEnum;
use App\Interface\Service\ImageServiceInterface;
use App\Repository\BaseRepository;
use App\Service\Mapper\MapperCreateService;
use App\Service\Util\ResultOperationService;
use App\Util\DoctrineFindParams;
use App\Util\ResultOperation;
use Doctrine\Persistence\ManagerRegistry;

class ImageService extends BaseRepository implements ImageServiceInterface
{
    private MapperCreateService $_mapperServiceCreate;
    private ResultOperationService $_resultOperationService;

    public function __construct(
        ManagerRegistry $registry,
        MapperCreateService $mapperServiceCreate,
        ResultOperationService $resultOperationService
    ) {
        parent::__construct($registry, TypeEntitiesEnum::IMAGE);
        $this->_mapperServiceCreate = $mapperServiceCreate;
        $this->_resultOperationService = $resultOperationService;
    }

    /**
     * Saves an image in the database.
     *
     * @param ImageCreateDto $imageCreateDto
     * @return ResultOperation
     */
    public function saveImage(ImageCreateDto $imageCreateDto): ResultOperation
    {
        if (!$imageCreateDto->isEmpty()) return $this->_resultOperationService->createFailure(
            'Objeto imagem não pode ser inválido.');

        $image = $this->_mapperServiceCreate->mapImage($imageCreateDto);
        $result = $this->createEntity($image);
        
        return $result
            ? $this->_resultOperationService->createSuccess(
                'Imagem salva com sucesso.')
            : $this->_resultOperationService->createFailure('Erro ao salvar imagem.');
    }

    /**
     * Retrieves an image from the repository based on the specified type and identifier.
     *
     * @param TypeImageEnum $typeImageEnum The type of the image to be retrieved.
     * @param DoctrineFindParams $identifier The parameters used to identify the image.
     * @return ResultOperation The result of the operation, indicating success or failure, along with the image data if found.
     */

    public function getImage(TypeImageEnum $typeImageEnum, DoctrineFindParams $identifier): ResultOperation
    {
        if (empty($typeImageEnum->value) || $identifier->isEmptyDoctrineFindParams()) return $this->_resultOperationService->createFailure(
            'Tipo de imagem não pode ser inválido.');
    
        $result = $this->getEntity($identifier);
        
        return $result
            ? $this->_resultOperationService->createSuccess(
                'Imagem encontrada com sucesso.', [$result])
            : $this->_resultOperationService->createFailure('Imagem não encontrada.');
    }

    /**
     * Retrieves all images from the repository.
     *
     * @param int $page The page to be retrieved.
     * @param int $size The number of images to be retrieved per page.
     * @return ResultOperation The result of the operation, indicating success or failure, along with the image data if found.
     */
    public function getImageAll(DoctrineFindParams $criteria, int $page, int $size = 10): ResultOperation
    {
        $result = $this->getEntitiesAll($criteria, $page ,$size);
        
        return $result
            ? $this->_resultOperationService->createSuccess(
                'Imagens encontradas com sucesso.', [$result])
            : $this->_resultOperationService->createFailure('Nenhuma imagem encontrada.');
    }

    /**
     * Retrieves one image from the repository based on the specified identifier.
     *
     * @param DoctrineFindParams $identifier The parameters used to identify the image.
     * @return ResultOperation The result of the operation, indicating success or failure, along with the image data if found.
     */
    public function getImageOneBy(DoctrineFindParams $criteria): ResultOperation
    {
        if ($criteria->isEmptyDoctrineFindParams()) return $this->_resultOperationService->createFailure(
            'Parâmetro de busca inválido.');
        
        $result = $this->getEntityOneBy($criteria);
        
        return $result
            ? $this->_resultOperationService->createSuccess(
                'Imagem encontrada com sucesso.', [$result])
            : $this->_resultOperationService->createFailure('Imagem não encontrada.');
    }

    /**
     * Retrieves all images from the repository based on the specified parameters.
     *
     * @param DoctrineFindParams $identifier The parameters used to identify the images.
     * @return ResultOperation The result of the operation, indicating success or failure, along with the image data if found.
     */
    public function getImagesBy(DoctrineFindParams $criteria): ResultOperation
    {
        if ($criteria->isEmptyDoctrineFindParams()) return $this->_resultOperationService->createFailure(
            'Parâmetro de busca inválido.');
        
        $result = $this->getEntitiesBy($criteria);
        
        return $result
            ? $this->_resultOperationService->createSuccess(
                'Imagens encontradas com sucesso.', [$result])
            : $this->_resultOperationService->createFailure(
                'Nenhuma imagem encontrada.');
    }

    /**
     * Uploads an image in the repository.
     *
     * @param ImageCreateDto $imageCreateDto The image to be uploaded.
     * @param DoctrineFindParams $doctrineFindParams The parameters used to identify the image.
     * @return ResultOperation The result of the operation, indicating success or failure, along with the image data if found.
     */
    public function uploadImage(ImageCreateDto $imageCreateDto, DoctrineFindParams $criteria): ResultOperation
    {
        if (!$imageCreateDto->isEmpty() || $criteria->isEmptyDoctrineFindParams())return $this->_resultOperationService->createFailure(
            'Objeto imagem não pode ser inválido.');
        
        
        $result = $this->updateEntity($imageCreateDto, $criteria);
        
        return $result
            ? $this->_resultOperationService->createSuccess(
                'Imagem atualizada com sucesso.', [$result])
            : $this->_resultOperationService->createFailure(
                'Erro ao atualizar imagem, verifique os valores do objeto.');
    }

    /**
     * Deletes an image from the repository based on the specified type and identifier.
     *
     * @param TypeImageEnum $typeImageEnum The type of the image to be deleted.
     * @param DoctrineFindParams $identifier The parameters used to identify the image.
     * @return ResultOperation The result of the operation, indicating success or failure.
     */

    public function deleteImageBy(TypeImageEnum $typeImageEnum, DoctrineFindParams $criteria): ResultOperation
    {
        if ($criteria->isEmptyDoctrineFindParams()) return $this->_resultOperationService->createFailure(
            'Tipo de imagem não pode ser inválido.');
        
        $result = $this->deleteEntity($criteria);
        
        return $result
            ? $this->_resultOperationService->createSuccess('Imagem deletada com sucesso.')
            : $this->_resultOperationService->createFailure('Erro ao deletar imagem.');  
    }
}
