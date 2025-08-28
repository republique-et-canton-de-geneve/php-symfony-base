<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Step\Then;
use Behat\Step\When;
use DOMElement;
use Exception;
use FriendsOfBehat\SymfonyExtension\Driver\SymfonyDriver;
use Symfony\Component\BrowserKit\Exception\JsonException;
use Throwable;

class BaseContext extends MinkContext implements Context
{
    /**
     * Test the value of an element selected by his css.
     *
     * @param string $element element css selector
     * @param string $value   expected value
     *
     * @throws Exception
     */
    #[Then('the :element element should have the value :value')]
    public function iShouldSeeValueElement(string $element, string $value): void
    {
        $page = $this->getSession()->getPage();
        $element_value = $page->find('css', "$element")?->getValue();
        if (!is_string($element_value)) {
            $msg = 'Value ' . $value . ' not found in element ' . $element;
            throw new Exception($msg);
        }
        if (!str_contains($element_value, $value)) {
            $msg = 'Value ' . $value . ' not found in element ' . $element .
                ', which had a value of ' . $element_value . '.';
            throw new Exception($msg);
        }
    }

    /**
     * Click on css element
     * Example: When I click on "div a:first-child" element.
     *
     * @throws Exception
     */
    #[When('I click on :element element')]
    public function iClickOnElement(string $element): void
    {
        $page = $this->getSession()->getPage();
        $node = $page->find('css', $element);
        if (!$node) {
            throw new Exception("Element '$element' not found.");
        }
        $node->click();
    }

    /**
     * Simulate an ajax call.
     *
     * @param string $page url
     */
    #[When('I simule an ajax to :page')]
    public function iSimuleAnAjaxTo(string $page): void
    {
        $this->getSession()->setRequestHeader('x-requested-with', 'XMLHttpRequest');
        $this->visitPath($page);
    }

    /**
     * Test if the response is a Json.
     *
     * @throws JsonException
     * @throws Exception
     */
    #[Then('I receive a json response')]
    public function iReceiveAJsonResponse(): void
    {
        /** @var SymfonyDriver $driver */
        $driver = $this->getSession()->getDriver();
        $client = $driver->getClient();
        $response = $client->getInternalResponse();
        $type = $response->getHeader('content-type');
        if ('application/json' !== $type) {
            throw new Exception('not a valid JSON response');
        }
        $data = $response->toArray();
        if (!$data) {
            throw new Exception('not a valid JSON data response');
        }
    }

    /**
     * Change the state of a checkbox.
     *  When I click on checkbox 'yes'.
     *
     * @throws Exception
     */
    #[When('I click on checkbox :name')]
    public function iClickOnCheckbox(string $option): void
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $node = $page->find('named', ['field', $option]);
        if (!$node) {
            throw new Exception("Element '$option' not found.");
        }
        $value = $node->getValue();
        if (null !== $value) {
            $page->uncheckField($option);
        } else {
            $page->checkField($option);
        }
    }

    /**
     * Change an attribut on css element
     * Example: When I change "class" attribut with "active" value  attribut on "#button" element.
     *
     * @throws Exception
     */
    #[When('I change :name attribut with :value value on :element element')]
    public function iChangeAttributWithValueOnElement(string $name, string $value, string $element): void
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();
        $nodes = $page->findAll('css', $element);
        if (!$nodes) {
            throw new Exception("Element '$element' not found.");
        }
        try {
            foreach ($nodes as $node) {
                /** @var DOMElement $element */
                $element = $crawler->filterXPath($node->getXpath())->getNode(0);
                $element->setAttribute($name, $value);
            }
        } catch (Throwable) {
            throw new Exception('Attribut not changed');
        }
    }

    /**
     * Remove an attribut on css element
     * Example: When I remove "disabled" attribut on "#button:send" element.
     *
     * @throws Exception
     */
    #[When('I remove :name attribut on :element element')]
    public function iRemoveAttributOnElement(string $name, string $element): void
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();
        $nodes = $page->findAll('css', $element);
        if (!$nodes) {
            throw new Exception("Element '$element' not found.");
        }
        try {
            foreach ($nodes as $node) {
                /** @var DOMElement $element */
                $element = $crawler->filterXPath($node->getXpath())->getNode(0);
                $element->removeAttribute($name);
            }
        } catch (Throwable) {
            throw new Exception('Attribut not removed');
        }
    }

    /**
     * Add a option to a select input
     * Example: When I add option "choix 1" for name and "1" for value from "id-select" select.
     *
     *  Be carefull, if you have many select, use all "add option" before select instruction
     *
     * @throws Exception
     */
    #[When('I add option :name for name and :value for value from :select select')]
    public function iAddOptionForNameAndForValueFromSelect(string $name, string $value, string $select): void
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();

        $node = $page->findField($select);
        if (!$node) {
            throw new Exception("Field '$select' not found.");
        }
        try {
            /** @var DOMElement $element */
            $element = $crawler->filterXPath($node->getXpath())->getNode(0);
            $option = new DOMElement('option', $name);
            $element->appendChild($option);
            $option->setAttribute('value', $value);
        } catch (Throwable) {
            throw new Exception('Option not added');
        }
    }

    /**
     * Add a option to a select input
     * Example: When I add a checkbox "name" for name and "1" for value in "css-select" element.
     *
     * @throws Exception
     */
    #[When('I add a checkbox :name for name and :value for value in :element element')]
    public function iAddCheckboxForNameAndForValueFromElement(string $name, string $value, string $cssElement): void
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();

        $node = $page->find('css', $cssElement);
        if (!$node) {
            throw new Exception("Element '$cssElement' not found.");
        }
        try {
            /** @var DOMElement $element */
            $element = $crawler->filterXPath($node->getXpath())->getNode(0);
            $input = new DOMElement('input');
            $input->setAttribute('type', 'checkbox');
            $input->setAttribute('value', $value);
            $input->setAttribute('name', $name);
            $input->setAttribute('checked', 'checked');
            $element->appendChild($input);
        } catch (Throwable $t) {
            throw new Exception('Option not added', $t->getCode(), $t);
        }
    }

    /**
     * Remove elements
     * Example: When I remove "css-select" element.
     *
     * @throws Exception
     */
    #[When('I remove :element element')]
    public function iRemoveElement(string $cssElement): void
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();
        $nodes = $page->findAll('css', $cssElement);
        if (!$nodes) {
            throw new Exception("Element '$cssElement' not found.");
        }
        try {
            foreach ($nodes as $node) {
                /** @var DOMElement $element */
                $element = $crawler->filterXPath($node->getXpath())->getNode(0);
                $element->nodeValue = null;
            }
        } catch (Throwable $t) {
            throw new Exception('Element not remove', $t->getCode(), $t);
        }
    }

    /**
     * Add a input field.
     *
     * Example: When I add a field "name" for name and "1" for value in "css-select" element.
     *
     * @throws Exception
     */
    #[When('I add a field :name for name and :value for value in :element element')]
    public function iAddFieldForNameAndForValueFromElement(string $name, string $value, string $cssElement): void
    {
        $session = $this->getSession();
        /** @var SymfonyDriver $driver */
        $driver = $session->getDriver();
        $page = $session->getPage();
        $client = $driver->getClient();
        $crawler = $client->getCrawler();

        $node = $page->find('css', $cssElement);
        if (!$node) {
            throw new Exception("Element '$cssElement' not found.");
        }
        try {
            /** @var DOMElement $element */
            $element = $crawler->filterXPath($node->getXpath())->getNode(0);
            $input = new DOMElement('input');
            $input->setAttribute('type', 'text');
            $input->setAttribute('value', $value);
            $input->setAttribute('name', $name);
            $element->appendChild($input);
        } catch (Throwable $t) {
            throw new Exception('Option not added', $t->getCode(), $t);
        }
    }
}
