<?php

namespace Drupal\colormod\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Module settings form.
 */
class ColorModForm extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return "colormod_form";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Form constructor.
        $form = parent::buildForm($form, $form_state);
        // Default settings.
        $config = $this->config("colormod.settings");

        $myFilePath = $this->getCssFilePath("styles.css");

        try {
            //open file
            $myfile = fopen($myFilePath, "r");
        } catch (\Throwable $th) {
            $th->getMessage();
        }

        //Output lines until EOF is reached
        while (!feof($myfile)) {
            $line = fgets($myfile);

            //parse all css variables with hsl() properties
            $namematches = [];
            $namepattern = "/--.*:.*hsl\(/i";
            $namematch = preg_match($namepattern, $line, $namematches);

            if ($namematch) {
                //parse the css variable name
                $endindex = strpos($namematches[0], ":") + 1;
                $name = substr($namematches[0], 0, $endindex);

                $form[$name] = [
                    "#type" => "fieldset",
                    "#title" => $name,
                    "#collapsible" => false,
                    "#collapsed" => false,
                ];
                $form[$name][$name . "_hue"] = [
                    "#type" => "number",
                    "#number_type" => "integer",
                    "#title" => t("Hue"),
                    "#default_value" => 0,
                    "#max" => 360,
                    "#min" => 0,
                    "#description" => $this->t("Give your $name hue."),
                ];
                //add item to form
                $form[$name][$name . "_saturation"] = [
                    "#type" => "number",
                    "#number_type" => "integer",
                    "#title" => t("Saturation"),
                    "#default_value" => 0,
                    "#max" => 100,
                    "#min" => 0,
                    "#description" => $this->t("Give your $name saturation."),
                ];
                //add item to form
                $form[$name][$name . "_luminosity"] = [
                    "#type" => "number",
                    "#number_type" => "integer",
                    "#title" => t("Luminosity"),
                    "#max" => 100,
                    "#min" => 0,
                    "#default_value" => 0,
                    "#description" => $this->t("Give your $name luminosity."),
                ];
            }
        }

        fclose($myfile);

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        //the min max fields on the form elements do all the needed validation
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $myFilePath = $this->getCssFilePath("styles.css");

        try {
            //create backup file if not already backed up
            if (!file_exists("$myFilePath-backup")) {
                copy($myFilePath, "$myFilePath-backup");
            }
            //open the file as an array
            $myFileArray = file($myFilePath);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }

        $stringArr = $this->buildHslStrings($form_state);

        //if the line contains a hsl() css variable replace it with the one from the form
        $myFileArray = array_map(function ($line) use ($stringArr) {
            $namematches = [];
            $namepattern = "/--.*:.*hsl\(/i";
            $namematch = preg_match($namepattern, $line, $namematches);

            if ($namematch) {
                $endindex = strpos($namematches[0], ":") + 1;
                $name = substr($namematches[0], 0, $endindex);
                $newLine = $name . " " . $stringArr[$name] . ");" . "\n";
            }

            return $namematch ? $newLine : $line;
        }, $myFileArray);

        try {
            //save the modified file
            file_put_contents($myFilePath, implode("", $myFileArray));
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }

        return parent::submitForm($form, $form_state);
    }

    protected function buildHslStrings($form_state)
    {
        //build new hsl() string values from the values entered in the form
        $builtStringArr = [];
        foreach ($form_state->cleanValues()->getValues() as $key => $value) {
            $endindex = strpos($key, ":") + 1;
            $name = substr($key, 0, $endindex);

            if (!array_key_exists($name, $builtStringArr)) {
                $builtStringArr[$name] = "hsl($value";
            } else {
                $builtStringArr[$name] = "$builtStringArr[$name],$value%";
            }
        }
        return $builtStringArr;
    }

    protected function getCssFilePath($filename)
    {
        /** @var \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler */
        $themeHandler = \Drupal::service("theme_handler");
        $themePath = $themeHandler
            ->getTheme($themeHandler->getDefault())
            ->getPath();

        return "$themePath/css/$filename";
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ["colormod.settings"];
    }
}
