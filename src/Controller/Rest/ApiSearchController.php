<?php

namespace App\Controller\Rest;

use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiSearchController extends AbstractFOSRestController
{
    /**
     * @Route("/search", name="api_search")
     */
    public function searchApi(Request $request, UserRepository $userRepository, ApiTokenRepository $apiTokenRepository): JsonResponse
    {
        if($apiTokenRepository->hasBearerAuthorization($request)){
            return new JsonResponse([
                'message' => "Authorization required"
            ], 401);
        }

        $token = str_replace("Bearer ","", $request->headers->get('Authorization'));

        $apiToken = $apiTokenRepository->findOneByToken($token);
        if(null === $apiToken){
            return new JsonResponse([
                'message' => "Authorization failed"
            ], 403);
        }

        if( $apiToken->isExpired()){
            return new JsonResponse([
                'message' => "Token expired"
            ], 403);
        }

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