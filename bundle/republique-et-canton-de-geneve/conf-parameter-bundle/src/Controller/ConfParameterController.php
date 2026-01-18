<?php

namespace EtatGeneve\ConfParameterBundle\Controller;

use App\Param;
use App\Parameter as ParameterClass;
use App\Parameter2;
use App\Security\Action;
use Doctrine\ORM\EntityManagerInterface;
use EtatGeneve\ConfParameterBundle\Entity\ConfParameterEntity;
use EtatGeneve\ConfParameterBundle\Service\ConfParameterManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConfParameterController extends AbstractController
{



    /**
     * @return FormInterface<mixed>
     */
    protected function buildForm(Param $annotation, ConfParameterEntity $dbParameter): FormInterface
    {
        // Create the form with the '$dbParameter' object
        $builder = $this->createFormBuilder($dbParameter)
            ->add('name', HiddenType::class);

        switch ($annotation->type) {
            case Param::TEXTAREA:
                $builder->add('value', TextareaType::class, ['required' => false, 'attr' => ['rows' => '6']])
                    ->add('defaultValue', TextareaType::class, [
                        'required' => false,
                        'attr' => ['rows' => '6'],
                        'mapped' => false,
                        'disabled' => true,
                    ]);
                break;
            case Param::RADIO:
                $builder->add(
                    'value',
                    ChoiceType::class,
                    [
                        'required' => true,
                        'choices' => ['0 (Non)' => '0', '1 (Oui)' => 1],
                        'expanded' => true,
                        'multiple' => false,
                    ]
                )->add('defaultValue', TextType::class, [
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                ]);

                break;
            default:
                $builder->add('value', TextType::class, ['required' => false])
                    ->add('defaultValue', TextType::class, [
                        'required' => false,
                        'mapped' => false,
                        'disabled' => true,
                    ]);
        }

        return $builder->getForm();
    }

    public function index(Parameter2 $parameter2, ConfParameterManager $confParameterManager): Response
    {
        // Refuse l'accès si l'utilisateur n'a pas le droit de lire les paramètres
        $this->denyAccessUnlessGranted(Action::ADMIN_PARAMETER);
        $confParameters = $confParameterManager->getConfParameters();

        return $this->render('@ConfParameter/index.html.twig', [
            'confParameters' => $confParameters,
            'canChange' => $this->isGranted(Action::ADMIN_PARAMETER_WRITE.'x'),
        ]);
    }

    // #[Route('/{name}/edit', name: 'admin_parameter_edit', methods: ['GET', 'POST'])]
    // public function edit(
    //     Request $request,
    //     string $name,
    //     EntityManagerInterface $entityManager,
    //     ParameterClass $parameterClass,
    //     ValidatorInterface $validator,
    // ): Response {
    //     $this->denyAccessUnlessGranted(Action::ADMIN_PARAMETER_WRITE);
    //     $annotation = $parameterClass->getAnnotations()[$name];
    //     if ($annotation->readOnly) {
    //         return $this->redirectToRoute('admin_parameter', [], Response::HTTP_SEE_OTHER);
    //     }
    //     $defaultValues = $parameterClass->getDefaultValues();
    //     $currentValues = $parameterClass->getCurrentValues();
    //     $defaultValue = $defaultValues[$name];
    //     $currentValueState = array_key_exists($name, $currentValues);
    //     // Select the record from the table that has the value 'name'
    //     $query = $entityManager->createQueryBuilder();
    //     $query->select('parameter')
    //         ->from(ConfParameter::class, 'parameter')
    //         ->where('parameter.name = :name')
    //         ->setParameter('name', $name);
    //     /** @var ConfParameter[] $dbParameters */
    //     $dbParameters = $query->getQuery()->getResult();
    //     if ($dbParameters) {
    //         $dbParameter = $dbParameters[0];
    //     } else {
    //         $dbParameter = new ConfParameter();
    //         $dbParameter->setName($name);
    //         $dbParameter->setValue($defaultValue);
    //         $entityManager->persist($dbParameter);
    //     }
    //     $form = $this->buildForm($annotation, $dbParameter);
    //     $form->get('defaultValue')->setData($defaultValue);
    //     $form->handleRequest($request);
    //     $errors = $validator->validatePropertyValue($parameterClass, $name, $dbParameter->getValue());
    //     $editOk = false;
    //     if (!count($errors) && $form->isSubmitted() && $form->isValid()) {
    //         if ('update' == $request->request->get('action')) {
    //             $editOk = true;
    //             $entityManager->flush();
    //             $parameterClass->clearCache();

    //             $this->logger->info(
    //                 'Parameter edited : ["Name" = "' . $dbParameter->getName() . '", "Value" = "'
    //                 . $dbParameter->getValue() . '", "Default Value" = "' . $defaultValue . '"]'
    //             );
    //         } elseif ('delete' == $request->request->get('action')) {
    //             $editOk = true;
    //             $entityManager->remove($dbParameter);
    //             $entityManager->flush();
    //             $parameterClass->clearCache();

    //             $this->logger->info(
    //                 'Parameter deleted, assgign to default value : ["Name" = "' . $dbParameter->getName() . '",
    //                 "Default Value" = "' . $defaultValue . '"]'
    //             );
    //         }
    //     }
    //     if ($editOk) {
    //         return $this->redirectToRoute('admin_parameter', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('admin/parameter/edit.html.twig', [
    //         'name' => $dbParameter->getName(),
    //         'parameter' => $parameterClass,
    //         'form' => $form,
    //         'currentValueState' => $currentValueState,
    //         'defaultValue' => $defaultValue,
    //         'annotation' => $annotation,
    //         'errors' => $errors,
    //     ]);
    // }
}
