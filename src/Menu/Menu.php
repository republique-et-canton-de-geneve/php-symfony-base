<?php

namespace App\Menu;

use App\Application;
use App\Security\Action;
use App\Security\User;
use Symfony\Bundle\SecurityBundle\Security;

class Menu extends MenuBase
{
    public function __construct(MenuTwig $menuTwig, Security $security, Application $application)
    {
        parent::__construct($menuTwig);
        /** @var ?User $user */
        $user = $security->getUser();

        $menuTwig->addItem(
            'redaction_right',
            new MenuItem(
                'idConnexion',
                'Bonjour ' . ($user ? '<strong>' . $user->getUserIdentifier() . '</strong>' : ''),
                'info'
            )
        );
        if ($user?->hasLogout()) {
            $menuTwig->addItem('redaction_right', new MenuItem('id_disconnection', 'Se déconnecter', 'saml_logout'));
        }
        if ($security->isGranted(Action::NAVBAR_ENVIRONNEMENT)) {
            $menuTwig->addItem(
                'redaction_right',
                new MenuItem(
                    'environnement',
                    '<span class="badge rounded-pill ' . $application->getServerType() .
                    '" data-bs-toggle="tooltip" title="Vous êtes dans l\'environnement de ' .
                    $application->getServerType() . "\nVersion : " . $application->getVersion() .
                    '">' . $application->getServerType() . '</span>'
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
