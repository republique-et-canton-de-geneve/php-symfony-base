# Behat

- Given (Etant donné que) : décrit le contexte initial
- When (Lorsque) : décrit l’action faite par l’utilisateur à partir du contexte initial
- Then (Alors) : décrit le comportement attendu suite à l’action faite par l’utilisateur
- And (Et) : Ajoute une condition au Given, When ou Then

Commandes utilisées :

## Standard Mink

- Navigation
    - Given I am on "/"
    - When I go to "url"
    - Then I should be on "url"
    - Then the url should match "superman is dead"
    - When I reload the page
    - Then the response status code should be 200
    - Then the response status code should not be 500

- Formulaire, link, boutons
    - When I follow "link name"  -> click
    - When I press "button name"
    - When I fill in "username" with "bwayne"
    - Then the "username" field should contain "bwayne"
    - Then the "username" field should not contain "batman"
    - When I select "Bats" from "user_fears"
    - When I additionally select "Deceased" from "parents_alive_status"
    - When I check "Pearl Necklace"
    - When I uncheck "Broadway Plays"
    - Then the "newsletter" checkbox should be unchecked
    - Then the "newsletter" checkbox should not be checked
    - Then the "remember_me" checkbox should be checked
    - Then the "newsletter" checkbox is unchecked
    - When I attach the file "bwayne_profile.png" to "profileImageUpload"
    - _When I fill in "bwayne" for "username"_
    - _When I fill in the following" <br>
      | username | bruceWayne |<br>
      | password | iLoveBats123 |_


- Analyse HTML et texte
    - Then I should see "Who is the Batman"
    - Then I should not see "Batman is Bruce Wayne"
    - Then I should see text matching "Batman, the vigilante"
    - Then I should not see text matching "Bruce Wayne, the vigilante"
    - Then the response should contain "Batman is the hero Gotham deserves."
    - Then the response should not contain "Bruce Wayne is a billionaire"
    - Then I should see "Batman" in the "heroes_list" element
    - Then I should not see "Bruce Wayne" in the "heroes_alter_egos" element
    - Then the "body" element should contain "style=\"color:black;\""
    - Then the "body" element should not contain "style=\"color:black;\""
    - Then I should see a "body" element  ( test si css element existe)
    - Then I should not see a "canvas" element
    - Then I should see 5 "div" elements


- Debug
    - Then print current URL
    - Then print last response
      _- Then show last response_

### Base Context

- Navigation
    - When I simule an ajax to "url"
    - Then I receive a json response
    - Then the "input" element should have the value "batman"
    - When I click on "div a:first-child" element
    - When I click on checkbox 'yes'
- Formulaire, link, boutons
    - When I change "class" attribut with "active" value attribut on "#button" element
    - When I remove "disabled" attribut on "#button" element
    - When I add a field "name" for name and "1" for value in "css-select" element.
    - When I add option "choix 1" for name and "1" for value from "id-select" select.
    - When I add a checkbox "name" for name and "1" for value in "css-select" element.

### Symfony Context

- Login
    - Given I am login as "test" with role "APP.EDG.ADMIN"
    - When I will be login as "test" with role "APP.EDG.UTILISATEUR"
- Formulaire, link, boutons
    - When I read field 'comment'<br>
      And I save it into "myField"
    - When I read "id" attribut on "#button:send" element<br>
      And I save it into "value"
    - Given a value "50"<br>
      And I save it into "myvar"<br>
      When I go to "/dg/<<myvar>>"
    - Given values "test" and "1"<br>
      And I save it into "url,id"<br>
      When I go to "/<<url>>/<<id>>"
    - Then <<x>> should equal "5"

- Debug, simulations
    - Then I commit db
    - Then I throw exception when sql match pattern "SELECT * FROM MYTABLE"
    - Then I reset throw exception when sql match pattern
    - Given server type is "prod"
    - When I get root dir<br>
      And I save it into "rootdir"
    - When I execute sql query "UPDATE table SET column1 = 'value1', column2 ='value2'"
    - When I test

### Appcontext

- When I set parameter "modeMaintenance" to "1"
