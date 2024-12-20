<?php

/**
 * @file
 * Contains \Drupal\colormod\Controller\ColorModController
 */

namespace Drupal\colormod\Controller;

use Drupal\Component\Utility\Html;
use Drupal\colormod\Service\ColorModService;

/**
 * Controller routines for Lorem ipsum pages.
 */
class ColorModController
{
    /**
     * Constructs Lorem ipsum text with arguments.
     * This callback is mapped to the path
     * 'colormod/generate/{lorem}/{ipsum}'.
     *
     * @var \Drupal\colormod\Service\ColorModService $ColorModService
     *   A call to the Lorem ipsum service.
     * @param string $paragraphs
     *   How many paragraphs of Lorem ipsum text.
     * @param string $phrases
     *   Average number of phrases per paragraph.
     */

    // The themeable element.
    protected $element = [];

    // The generate method which stores lorem ipsum text in a themeable element.
    public function generate($paragraphs, $phrases)
    {
        $ColorModService = \Drupal::service("colormod.colormod_service");
        $element = $ColorModService->generate($paragraphs, $phrases);

        return $element;
    }
}
