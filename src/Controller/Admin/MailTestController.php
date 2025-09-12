<?php

namespace App\Controller\Admin;

use App\Application;
use App\Entity\MailTest;
use App\Parameter;
use App\Security\Action;
use App\Service\Mail;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/admin/mailtest')]
class MailTestController extends BaseAdminController
{
    /**
     * @return FormInterface<mixed>
     */
    protected function buildForm(MailTest $mailTest): FormInterface
    {
        // Create the form with
        $builder = $this->createFormBuilder($mailTest)
            ->add('adresse', TextType::class);

        return $builder->getForm();
    }

    #[Route('', name: 'admin_mail_test')]
    public function mailTest(Request $request, Mail $mail, Application $application, Parameter $parameter): Response
    {
        $this->denyAccessUnlessGranted(Action::ADMIN_MAIL_TEST);
        $mailTest = new MailTest();
        $form = $this->buildForm($mailTest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $mail->sendMail(
                    $mailTest->getAdresse() . '@mydomain.com',
                    "Test envoi d'email",
                    'Ce mail est un test'
                );
                $this->addFlash('success', 'Le mail a été envoyé');

                return $this->redirectToRoute('admin_mail_test');
            } catch (Throwable $e) {
                $this->addFlash(
                    'danger',
                    "Le mail n'a pas été envoyé, l'erreur suivante a été détectée : " . $e->getMessage()
                );
            }
        }
        if ('prod' === $application->getServerType()) {
            $info = 'Attention, le mail sera réelement envoyé à cette adresse saisie !';
        } else {
            $info = "Attention, le mail sera redirigé à l'adresse suivante : " . $parameter->smtpRedirectMailTo;
        }

        return $this->render('admin/mail_test.html.twig', [
            'form' => $form,
            'info' => $info,
        ]);
    }
}
