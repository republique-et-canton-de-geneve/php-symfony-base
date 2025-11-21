Feature: Test DEMO

  @DEMO @TEST
  Scenario: Demo
    # en tant qu'utilisateur avec un rôle ADMIN
    Given I am login as "utilisateur" with role "ADMIN"
    # Je test l'accès à la home page et je vois mon nom d'utilisateur
    When I go to "/"
    Then I should be on "/"
    Then I should see a "body#page-home" element
    And the response should contain "utilisateur"

    # Je test les pages de log
    When I go to "/admin/log"
    Then I should be on "/admin/log"
    Then I should see a "body#page-admin-log-list" element
    Then I should see "logs" in the "h2" element
    # je clique sur le premier lien pour consulter le log
    When I click on "div.block-main a:first-child" element
    Then I should see a "body#page-admin-log-content" element
    Then I should see a "body pre" element

    # je test d'affichier le contenu d'un fichier qui n'exsite pas ( hacking),
      # on obtient à la placela liste des fichiers logs
    When I go to "/admin/log/XySvefhhfuwhbcw"
    Then I should see a "body#page-admin-log-list" element
    Then I should see "logs" in the "h2" element


    # je test la page info
    When I go to "/info"
    Then I should be on "/info"
    Then I should see a "body#page-info" element
    Then I should see "Information" in the "h1" element


    # je test la page to do
    When I go to "/todo"
    Then I should be on "/todo"
    Then I should see a "body#page-todo" element


    # je test la page demo de l'editeur
    When I go to "/editor"
    Then I should be on "/editor"
    Then I should see a "body#page-editor" element

    # je test la page qui simule une Erreur 500
    When I go to "/500"
    Then I should be on "/500"
    Then the response status code should be 500
    Then I should see a "body#page-error500" element


    # je test une url inconnu
    When I go to "/hfkjhashufhiurhiuerhgiuehhiuehrui"
    Then I should be on "/hfkjhashufhiurhiuerhgiuehhiuehrui"
    Then the response status code should be 404
    Then I should see a "body#page-error404" element

    # je test la page admin
    When I go to "/admin"
    Then I should be on "/admin"
    Then I should see a "body#page-admin-index" element


    # je test la page test email
    When I go to "/admin/mailtest"
    Then I should be on "/admin/mailtest"
    Then I should see a "body#page-admin-mail-test" element
    When I fill in "form_adresse" with "test"
    Then I press "action"
    Then I should be on "/admin/mailtest"
    Then I should see a "body#page-admin-mail-test" element
    #Then I should see "Le mail n'a pas été envoyé, l'erreur suivante a été détectée :" in the "#flash-messages div.alert.alert-danger" element
    Then I should see "Le mail a été envoyé" in the "#flash-messages div.alert.alert-success" element

    When server type is "prod"
    Then I press "action"
    Then I should be on "/admin/mailtest"
    Then I should see a "body#page-admin-mail-test" element
    #Then I should see "Le mail a été envoyé" in the "#flash-messages div.alert.alert-success" element
    Then I should see "Le mail n'a pas été envoyé, l'erreur suivante a été détectée :" in the "#flash-messages div.alert.alert-danger" element
    When server type is "test"

    # je test la page parameter
    When I go to "/admin/parameter"
    Then I should be on "/admin/parameter"
    Then I should see a "body#page-admin-parameter-index" element
    Then I should see "Paramètres de l'application" in the "h1" element

    # j'édite un paramètre smtpRedirectMailTo
    When I go to "/admin/parameter/smtpRedirectMailTo/edit"
    Then I should be on "/admin/parameter/smtpRedirectMailTo/edit"
    Then I should see a "body#page-admin-parameter-edit" element

    # je modifie la valeur du paramètre et le sauve
    When I fill in "form_value" with "x@mydomain.com"
    When I press "Enregistrer"
    Then I should be on "/admin/parameter"
    Then I should see a "body#page-admin-parameter-index" element

    # j'édite un paramètre smtpRedirectMailTo ( Paramètre de type Text)
    When I go to "/admin/parameter/smtpRedirectMailTo/edit"
    Then I should be on "/admin/parameter/smtpRedirectMailTo/edit"
    Then I should see a "body#page-admin-parameter-edit" element

    # je reprends la valeur par défaut du paramètre
    When I press "Revenir à la valeur par défaut"
    Then I should be on "/admin/parameter"
    Then I should see a "body#page-admin-parameter-index" element

    # j'édite un paramètre modeMaintenance ( Paramètre de type Radio)
    When I go to "/admin/parameter/modeMaintenance/edit"
    Then I should be on "/admin/parameter/modeMaintenance/edit"
    Then I should see a "body#page-admin-parameter-edit" element

    # j'édite un paramètre modeMaintenance ( Paramètre de type Textarea)
    When I go to "/admin/parameter/pageInfo/edit"
    Then I should be on "/admin/parameter/pageInfo/edit"
    Then I should see a "body#page-admin-parameter-edit" element

    # je test la page ApplicationInfo
    When I go to "/admin/applicationInfo"
    Then I should be on "admin/applicationInfo"
    Then I should see a "body#page-admin_application_info" element
    Then I should see "ApplicationInfo" in the "h2" element

# un utilisateur avec un seul rôle unconnu
# peut accéder à la home page
    Given I am login as "inconnu" with role "UNKNOW"
    When I go to "/"
    Then I should be on "/"
    Then I should see a "body#page-home" element
    And the response should contain "inconnu"
# ne peut accéder à la page admin, une page erreur est affichée
    When I go to "/admin"
    Then I should be on "/admin"
    Then I should see a "body#page-error403" element


# test des services application Info
    When I test applicationInfo service


  @DB
  Scenario: Simule une erreur de la DB, la table parameter est HS
    # on part d'une situation "saine", les caches sont à jour et en fonction
    Given I am login as "utilisateur" with role "ADMIN"
    When I go to "/"
    # je simule une erreur de la DB pour les accès lecture à table parameter
    # j'edite un paramètre ce qui provoqu une lecture de la DB
    Then I throw exception when sql match pattern "FROM parameter"
    When I go to "/admin/parameter/smtpRedirectMailTo/edit"
    Then I should be on "/admin/parameter/smtpRedirectMailTo/edit"
    Then I should see a "body#page-error500" element
    # je désactive la simulation des erreur de la DB
    Then I reset throw exception when sql match pattern

  @MAINTENANCE
  Scenario: Mode Mainteance
    # en tant qu'utilisateur avec un rôle ADMIN
    Given I am login as "utilisateur" with role "ADMIN"
    # le parametre mainteance est activé
    When I set parameter "modeMaintenance" to 1
    #un admin peut aller sur le site
    When I go to "/"
    Then I should be on "/"
    Then I should see a "body#page-home" element
    And the response should contain "utilisateur"
    # en tant qu'utilisateur avec un rôle utilisateur
    Given I am login as "utilisateur" with role "UTILISATEUR"
    # je test la page qui simule une Erreur 500
    When I go to "/"
    Then I should be on "/"
    #une page de mainteance est affichée
    Then the response status code should be 503
    Then I should see a "body#page-error503" element


#Then print last response
