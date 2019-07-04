<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorTrait
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @required
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
