<?php


namespace Framework\Template;


class PhpRenderer implements TemplateRenderer
{
    private $path;
    private $extend;
    private $blocks = [];
    private $blockNames;

    public function __construct($path)
    {
        $this->path = $path;
        $this->blockNames = new \SplStack();
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
        if(array_key_exists($name, $this->blocks)) {
            return;
        }
        return $this->blocks[$name] = $content;
    }

    public function renderBlock($name)
    {
        return $this->blocks[$name] ?? '';
    }
}