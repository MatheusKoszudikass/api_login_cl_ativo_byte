<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Interface\Service\ImageServiceInterface;    

class ImageController extends AbstractController
{
    private ImageServiceInterface $_imageService;

    public function __construct(ImageServiceInterface $imageService)
    {
        $this->_imageService = $imageService;
    }

    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {

        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }
}
