<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Symfony\Component\Form\FormFactoryInterface;

trait FormFactoryTrait
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }
}
