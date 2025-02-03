<?php

namespace App\Service;

use App\Dto\Create\ImageCreateDto;
use App\Entity\Enum\TypeImageEnum;
use App\Interface\ImageRepositoryInterface;
use App\Interface\Service\ImageServiceInterface;
use App\Result\ResultOperation;
use App\Dto\DoctrineFindParams;

class ImageService implements ImageServiceInterface
{
    private ImageRepositoryInterface $imageRepository;
    private MapperServiceCreate $mapperServiceCreate;

    public function __construct(ImageRepositoryInterface $imageRepository, MapperServiceCreate $mapperServiceCreate)
    {
        $this->imageRepository = $imageRepository;

        $this->mapperServiceCreate = $mapperServiceCreate;
    }

    public function saveImage(?ImageCreateDto $imageCreateDto): ResultOperation
    {
        if($imageCreateDto->isEmpty()) return self::messageImageEmpty('image'); 

        $image = $this->mapperServiceCreate->mapImage($imageCreateDto);

        $result = $this->imageRepository->create($image);
        
        if($result)
            return new ResultOperation(true, 'Imagem salva com sucesso.', [$result]);

        return new ResultOperation(false, 'Erro ao salvar imagem.', [$result]);
    }
    
    public function deleteImageById(TypeImageEnum $typeImageEnum, DoctrineFindParams $identifier): ResultOperation
    {
        if($typeImageEnum->value == '') return self::messageImageEmpty('imagem');
    
        $result = $this->imageRepository->delete($identifier);
        var_dump($result);
        return new ResultOperation(true, 'Imagem deletada com sucesso.'); 
    }
    
    public function getImage(TypeImageEnum $typeImageEnum, DoctrineFindParams $identifier): ?ResultOperation
    {
        if($typeImageEnum->value === '') return self::messageImageEmpty('imagem');
        
        $result = $this->imageRepository->getOneBy($identifier);
        
        if($result)
            return new ResultOperation(true, 'Imagem encontrada com sucesso.', [$result]);

        return new ResultOperation(false, 'Imagem não encontrada.', [$result]);
    }

    public function getImageAll(): ResultOperation
    {
        $result = $this->imageRepository->getAll();
        
        if($result)
            return new ResultOperation(true, 'Imagem encontrada com sucesso.', [$result]);

        return new ResultOperation(false, 'Imagem não encontrada.', [$result]);
    }

    public function uploadImage(ImageCreateDto $imageCreateDto, DoctrineFindParams $doctrineFindParams): ResultOperation
    {
        if($imageCreateDto->isEmpty()) return self::messageImageEmpty('update image');

        $result = $this->imageRepository->update($imageCreateDto, $doctrineFindParams);

        if(!$result) return new ResultOperation(false, 'Erro ao atualizar imagem verifique os valores do objeto.');

        return new ResultOperation(true, 'Imagem atualizada com sucesso.', [$result]);
    }
    private static function messageImageEmpty(string $message): ResultOperation
    {
        return new ResultOperation(false, `Objeto {$message} não pode ser inválido.`);
    }
}

