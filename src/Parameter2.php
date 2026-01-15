<?php

namespace App;

use EtatGeneve\ConfParameterBundle\ConfParameter;

/**
 * The parameters are of type string, because they will be stored in the DB
 * as PHP is an untyped language this is not a problem :-)
 * for boolean, use '0' or '1' rather than 'true' or 'false'.
 */
class Parameter2 extends ConfParameter
{
    #[Param(description: 'Met le site d\'édition des avis en mode maintenance', type: Param::RADIO)]
    public string $modeMaintenance = '0';
    #[Param(
        description: "Bandeau d'information général affiché sur chaque page. par exemple: " .
        'Le site sera en maintenance à partir de demain',
        valeur: 'texte ou vide',
        type: Param::TEXTAREA
    )]
    public string $pageInfo = '';
    #[Param(
        description: 'Force la redirection des e-mails pour le serveur de Prod, ' .
        'pour les autres serveurs les e-amil sont toujours redirigés',
        type: Param::RADIO
    )]
    public string $smtpForceRedirection = '0';

    #[Param(
        description: 'Adresse e-mail de redirection, ' .
        'si la redirection est activé tous les e-mails sont envoyés à cette adresse',
        valeur: 'Adresse e-mail du domaine',
        type: Param::INPUT
    )]
    public string $smtpRedirectMailTo = 'redirection@mydomain.com';
}