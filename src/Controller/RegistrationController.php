<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use function PHPSTORM_META\type;

class RegistrationController extends AbstractController
{
    #[Route('/registrieren', name: 'app_registration')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => 'Mitarbeiter'
            ])
            ->add('password', RepeatedType::class, [
                'type'   => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Passwort'],
                'second_options' => ['label' => 'Passwort wiederholen'],
            ])
            ->add('registrieren', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $input = $form->getData();

            $user = new User();
            $user->setUsername($input['username']);

            $user->setPassword($passwordHasher->hashPassword($user, $input['password']));

            $em = $managerRegistry->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_home'));
        }


        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
