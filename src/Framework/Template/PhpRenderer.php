<?php


namespace Framework\Template;


use Framework\Http\Router\Router;

class PhpRenderer implements TemplateRenderer
{
    private $path;
    private $extend;
    private $blocks = [];
    private $blockNames;
    private $route;

    public function __construct($path, Router $route)
    {
        $this->path = $path;
        $this->blockNames = new \SplStack();
        $this->route = $route;
    }

    public function render($view, array $params = []): string
    {
        $templateFile = $this->path . '/' . $view . '.php';

        ob_start();
        extract($params, EXTR_OVERWRITE);
        $this->extend = null;
        require $templateFile;
        $content = ob_get_clean();

        if (!$this->extend) { //есть ли в $content
            return $content;
        }

        return $this->render($this->extend, [
            'content' => $content,
        ]);
    }

    public function extend($view): void
    {
        $this->extend = $view;
    }

    public function beginBlock($name)
    {
        $this->blockNames->push($name);
        return ob_start();
    }

    public function endBlock()
    {
        $content = ob_get_clean();
        $name = $this->blockNames->pop();
        if($this->hasBlock($name)) {
            return;
        }
        return $this->blocks[$name] = $content;
    }

    public function renderBlock($name)
    {
        $block = $this->blocks[$name] ?? null;

        if($block instanceof \Closure) {
            return $block();
        }
        return $block ?? '';
    }

    public function ensureBlock($name)
    {
        if($this->hasBlock($name)) {
            return false;
        }

        $this->beginBlock($name);
        return true;
    }

    public function hasBlock($name)
    {
        return array_key_exists($name, $this->blocks);
    }

    public function block($name, $content)
    {
        if($this->hasBlock($name)) {
            return false;
        }

        $this->blocks[$name] = $content;
    }

    public function encode($name)
    {
       return htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    public function path($name, array $params = []): string
    {
        return $this->route->generate($name, $params);
    }
}