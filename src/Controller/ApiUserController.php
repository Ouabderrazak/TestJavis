<?php

namespace App\Controller;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiUserController extends AbstractController
{
    
    /**
     * @Route("/api/users", name="api_users_index", methods={"Get"} )
     */
    public function index(UserRepository $userRepositorty)
    {    // get All user  
        $users = $userRepositorty->findAll();
        $response = $this->json ($users,200,[],['groups'=>'user:read']);
        return $response;
    }

    /**
     * @Route("/api/user/{id}", name="api_Oneuser", methods={"Get"} )
     */
    public function getUser(User $user)
    {   // get one user
        $response = $this->json ($user,200,[],['groups'=>'user:read']);
        return $response;
    }


     /**
     * @Route("/api/user", name="api_user_creat", methods={"Post"} )
     */
    public function creatUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, 
    ValidatorInterface $validator , LoggerInterface $logger)
    {    //Add new User
        $jsonRecu = $request->getContent();
            try{
                $user = $serializer->deserialize($jsonRecu, User::class, 'json');
                $user->setCreationdate(new \DateTime());
                $user->setUpdatedate(new \DateTime());
                
                $errors = $validator->validate($user);
                // We check if we have error
                    if(count($errors) > 0){
                        $logger->error('An error occurred');
                        return $this->json($errors,400);
                    }
        
                $em->persist($user);
                $em->flush();
                $logger->info('user has been added');

                return $this->json($user,201,[],['groups' =>'post:read']);
                
            } catch(NotEncodableValueException $e){  // We check if we have Not Encodable Value
            return $this->json([
                'status' =>400,
                'message' => $e->getMessage()

            ],400);
            $logger->error('Syntax no Valide');
            }
    }
     /**
     * @Route("/api/user/update/{id}", name="api_user_update", methods={"PUT"} )
     */
    public function UpdateUser(?User $user,Request $request ,SerializerInterface $serializer, EntityManagerInterface $em, 
    ValidatorInterface $validator , LoggerInterface $logger)
    {    //update User
        $jsonRecu = json_decode($request->getContent());
        $code = 200 ;

        if(!$user){ // if we don't have user
            $user = new User();
            $code = "201";
        }
            
        $user->setFirstname($jsonRecu->firstname);
        $user->setLastname($jsonRecu->lastname);
        $user->setUpdatedate(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $logger->info('user has been Update');

        return new Response('OK' , $code);  
    }
    /**
     * @Route("/api/user/delete/{id}", name="api_users_delete", methods={"DELETE"} )
     */
    public function DeleteUser(?user $user,EntityManagerInterface $em, LoggerInterface $logger)
    {    // Remove user  
        if(!$user){ // if we don't have user
            return new Response('User not found');
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $logger->info('user has been deleted');
            return new Response('This user has been deleted');
        }
    }
}