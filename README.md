# ColorMod

A module that allows you to change hsl() css variables located the current Drupal theme styles.css file.
Changes are made in the administrator section of Drupal.

## Install

In a Drupal project:

- create a colormod directory at: web/modules/custom/colormod
- Add the repo contents in here
- enable the module inside the Drupal admin section

## Features

- dynamically generated form
- CRUD for css file

## Roadmap

- loop through all css files located in the current themes css directory (dont make functionality specific to styles.css)
- start the default values of the form fields off with the current values in the css file
- add a color swatch and color selector to the form for each css variable
- allow for othe color types, not just hsl

## Sample Images

<img width="1512" alt="Screenshot 2024-12-30 at 14 02 33" src="https://github.com/user-attachments/assets/35c16128-4a4f-4b03-8371-871c8d1d5372" />
