<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Error;

trait TemplatingTrait
{
    /**
     * @var Twig_Environment
     */
    protected $templating;

    /**
     * @required
     * @param Twig_Environment $templating
     */
    public function setTemplating(Twig_Environment $templating): void
    {
        $this->templating = $templating;
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        if (null === $response) {
            $response = new Response();
        }

        try {
            $response->setContent($this->templating->render($view, $parameters));
        } catch (Twig_Error $exception) {
            $response->setContent($exception->getMessage());
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
