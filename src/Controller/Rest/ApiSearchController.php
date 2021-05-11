<?php

namespace App\Controller\Rest;

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiSearchController extends AbstractFOSRestController
{
    /**
     * @Route("/postSearch", name="postSearch")
     */
    public function post(Request $request, UserRepository $userRepository): JsonResponse
    {
        $values = json_decode($request->getContent(), true);

        foreach ($values as $key => $value) {
            if ("" === trim($value)) {
                unset($values[$key]);
            }
        }
        if (null != $values && $users = $userRepository->searchByValueFields($values)) {
            return new JsonResponse(json_encode($users, true),202);
        }
        return new JsonResponse([],204);
    }
}