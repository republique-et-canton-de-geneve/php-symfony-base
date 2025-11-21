<?php

namespace App\Menu;

use App\Application;
use App\Security\Action;
use Symfony\Bundle\SecurityBundle\Security;

class MenuAdmin extends MenuBase
{
    public function __construct(MenuTwig $menuTwig, Security $security, Application $application)
    {
        parent::__construct($menuTwig);
        $user = $security->getUser();
        if ($security->isGranted(Action::ADMIN_PAGE)) {
            $menuTwig->addItem(
                'redaction_left',
                new MenuItem('admin_page', 'Information', 'admin_page')
            );
        }
        if ($security->isGranted(Action::ADMIN_PARAMETER)) {
            $menuTwig->addItem(
                'redaction_left',
                new MenuItem('admin_parameter', 'Parameter', 'admin_parameter')
            );
        }
        if ($security->isGranted(Action::ADMIN_MAIL_TEST)) {
            $menuTwig->addItem(
                'redaction_left',
                new MenuItem('admin_mail_test', 'Test Email', 'admin_mail_test')
            );
        }

        if ($security->isGranted(Action::ADMIN_PAGE)) {
            $menuTwig->addItem('redaction_left', new MenuItem('Edit Page', 'editor', 'editor'));
        }
        if ($security->isGranted(Action::ADMIN_PAGE)) {
            $menuTwig->addItem('redaction_left', new MenuItem('todo', 'To Do', 'todo'));
        }

        $menuTwig->addItem(
            'redaction_left',
            new MenuItem('applicationInfo', 'applicationInfo', 'applicationInfo')
        );
        if ($security->isGranted(Action::ADMIN_LOG)) {
            $menuTwig->addItem('redaction_left', new MenuItem('admin_log', 'logs', 'admin_log'));
        }
        $subDemoMenus = [
            new MenuItem('demo1', 'Demo', 'homepage'),
            new MenuItem('demo2', 'ge.ch', null, null, ['url' => 'https://ge.ch']),
            new MenuItem('demo3', 'SITG', null, null, ['url' => 'https://sitg.ge.ch']),
        ];
        if ($security->isGranted(Action::ADMIN_PAGE)) {
            $menuTwig->addItem(
                'redaction_left',
                new MenuItem('admin_demo', 'Démo', null, [], [], $subDemoMenus)
            );
        }
        $menuTwig->addItem(
            'redaction_right',
            new MenuItem(
                'idConnexion',
                'Bonjour ' . ($user instanceof \Symfony\Component\Security\Core\User\UserInterface ? '<strong>' .
                    $user->getUserIdentifier() . '</strong>' : ''),
                'info'
            )
        );
        $menuTwig->addItem('redaction_right', new MenuItem('idDeconnexion', 'Se déconnecter', 'saml_logout'));
        if ($security->isGranted(Action::NAVBAR_ENVIRONNEMENT)) {
            $menuTwig->addItem(
                'redaction_right',
                new MenuItem(
                    'environnement',
                    '<span class="badge rounded-pill ' . $application->getServerType() .
                    '" data-bs-toggle="tooltip" title="Vous êtes dans l\'environnement de ' .
                    $application->getServerType() . "\nVersion : " . $application->getVersion() . '">' .
                    $application->getServerType() . '</span>'
                )
            );
        }
        $subSettingsMenus = [];
        if ($security->isGranted(Action::ADMIN_PAGE)) {
            $subSettingsMenus[] = new MenuItem('admin_page', 'Administration', 'admin_page');
        }
        if ($security->isGranted(Action::HOMEPAGE)) {
            $subSettingsMenus[] = new MenuItem('homepage', 'Accueil', 'homepage');
        }
        if ([] !== $subSettingsMenus) {
            $menuTwig->addItem(
                'redaction_right',
                new MenuItem('settings', '<i class="bi bi-gear"></i>', null, [], [], $subSettingsMenus)
            );
        }
    }
}
