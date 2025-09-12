<?php

namespace App\Controller\Admin;

use App\Application;
use App\Kernel;
use App\Security\Action;
use App\Security\Role;
use App\Service\ApplicationInfo\Output\SymfonyOutput;
use App\Service\ApplicationInfo\Symfony;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends BaseAdminController
{
    #[Route('', name: 'admin_page')]
    public function index(Request $request, Application $application): Response
    {
        $this->logger->debug('Debug page');
        $this->denyAccessUnlessGranted(Action::ADMIN_PAGE);
        $roles = Role::getRoles();
        foreach ($roles as $key => $role) {
            if (!$this->isGranted($role)) {
                unset($roles[$key]);
            }
        }

        return $this->render(
            'admin/index.html.twig',
            [
                'phpVersion' => phpversion(),
                'symfonyVersion' => \Symfony\Component\HttpKernel\Kernel::VERSION,
                'httpGinaroles' => $request->server->get('HTTP_GINAROLES', 'pas défini'),
                'rolesHeritage' => $roles,
                'user' => $application->getUser(),
            ]
        );
    }

    /**
     * Display log.
     */
    #[Route('/log/{filename}', name: 'admin_log', methods: ['GET'])]
    public function log(Request $request, Kernel $kernel, ?string $filename = null): Response
    {
        $this->logger->info('Debug log');
        $this->denyAccessUnlessGranted(Action::ADMIN_LOG);
        $finder = new Finder();
        $finder->files()->in($kernel->getLogDir());
        if ($filename) {
            $filename = preg_replace('/[^_.\\-A-Za-z0-9]+/', '', $filename);
            if ($filename) {
                $files = $finder->name($filename);
                if (0 !== $files->count()) {
                    $array = iterator_to_array($files);
                    $file = reset($array);
                    if ($file) {
                        $this->logger->info('Debug affichage du log ' . $file->getFilename());

                        return $this->render('admin/log/content.html.twig', ['file' => $file]);
                    }
                }
            }
            $finder->name('*');
        }
        $finder->sortByModifiedTime();

        return $this->render('admin/log/list.html.twig', ['finder' => $finder]);
    }

    #[Route('/applicationInfo', name: 'applicationInfo')]
    public function applicationInfo(Request $request): Response
    {
        $this->logger->info('applicationInfo');
        $this->denyAccessUnlessGranted(Action::ADMIN_PAGE);

        $symfonyOutput = new SymfonyOutput();
        $app = new Symfony($symfonyOutput);
        unset($app);

        return $this->render(
            'admin/application_info.html.twig',
            ['html' => $symfonyOutput->getHtml()]
        );
    }
}
