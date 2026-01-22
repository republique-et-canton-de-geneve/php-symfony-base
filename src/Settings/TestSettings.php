<?php
// src/Settings/TestSettings.php

namespace App\Settings;

use Jbtronics\SettingsBundle\Settings\Settings;
use Jbtronics\SettingsBundle\Settings\SettingsParameter;
use Jbtronics\SettingsBundle\ParameterTypes\StringType;
use Jbtronics\SettingsBundle\ParameterTypes\IntType;
use Jbtronics\SettingsBundle\Storage\JSONFileStorageAdapter;
use Jbtronics\SettingsBundle\Settings\SettingsTrait;
use Symfony\Component\Validator\Constraints as Assert;


#[Settings(storageAdapter: JSONFileStorageAdapter::class)] // The settings attribute makes a simple class to settings
class TestSettings {
    use SettingsTrait; // Disable constructor and __clone methods

     //The properties are public here for simplicity, but it can also be protected or private

    //In many cases this attribute with zero config is enough, the type mapper is then derived from the declared type of the property
    #[SettingsParameter()]
    public string $myString = 'default value'; // The default value can be set right here in most cases

    //Or you can explicitly set the type mapper and some options
    #[SettingsParameter(type: IntType::class, label: 'My Integer', description: 'This value is shown as help in forms.')]
    #[Assert\Range(min: 5, max: 10,)] // You can use symfony/validator to restrict possible values
    public ?int $myInt = null;
}
