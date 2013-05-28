<?php
namespace DbDiff;

use Fwk\Core\Components\ResultType\ResultType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds Twig templates support to Fwk/Core
 * @see Fwk\Core\Components\ResultType\ResultType
 * 
 * @category Result Types
 * @package  TodoApp
 * @author   Julien Ballestracci
 */
class TwigResultType implements ResultType
{
    /**
     * Twig environment
     * 
     * @var \Twig_Environment
     */
    protected $twig;
    
    /**
     * Initialize the Twig Result Type
     * 
     * @param array $params Twig configuration options
     * 
     * @return void
     */
    public function __construct(array $params = array())
    {
        $loader = new \Twig_Loader_Filesystem($params['templatesDir']);
        $this->twig = new \Twig_Environment($loader, $params);
    }
    
    /**
     * Renders a twig template and returns a Response
     * 
     * @param array $actionData Data from the Action Controller
     * @param array $params     Parameters defined in the <result /> block of the 
     * action
     * 
     * @return Response 
     */
    public function getResponse(array $actionData = array(), 
        array $params = array()
    ) {
        if (!isset($params['template']) || empty($params['template'])) {
            throw new \RuntimeException(
                sprintf('Missing template "template" parameter')
            );
        } 
        
        $file = $params['template'];
        
        return new Response($this->twig->render($file, $actionData));
    }
}