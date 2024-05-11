<?php

namespace App\Core;

class Template
{
    protected $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function render($template)
    {
        $templatePath = base_path('resources/views/' . $template . '.tpl.php');
        if (!file_exists($templatePath)) {
            throw new \Exception("Template not found: $template");
        }

        $templateContent = file_get_contents($templatePath);
        $templateContent = $this->processDirectives($templateContent);
        $templateContent = $this->processVariables($templateContent);
        return $templateContent;
    }

    protected function processDirectives($template)
    {
        $directives = [
            'layouts' => 'processLayouts',
            'includes' => 'processIncludes',
            'each' => 'processEach',
            'if' => 'processIf',
        ];

        foreach ($directives as $directive => $method) {
            $pattern = '/@' . $directive . '\((.*?)\);/';
            $template = preg_replace_callback($pattern, [$this, $method], $template);
        }

        return $template;
    }

    protected function processVariables($template)
    {
        $pattern = '/{{\s*(\w+)\s*}}/';
        $template = preg_replace_callback($pattern, function ($matches) {
            $key = $matches[1];
            return isset($this->data[$key]) ? $this->data[$key] : '';
        }, $template);

        return $template;
    }

    protected function processLayouts($matches)
    {
        $layout = $matches[1];
        $layoutPath = base_path("resources/views/layouts/$layout.tpl.php");
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout not found: $layout");
        }

        return file_get_contents($layoutPath);
    }

    protected function processIncludes($matches)
    {
        $include = $matches[1];
        $includePath = base_path("resources/views/includes/$include.tpl.php");
        if (!file_exists($includePath)) {
            throw new \Exception("Include not found: $include");
        }

        return file_get_contents($includePath);
    }

    protected function processEach($matches)
    {
        $array = $this->data[$matches[1]] ?? [];
        $template = '';
        foreach ($array as $item) {
            $template .= $this->render("each/$matches[2].html", $item);
        }
        return $template;
    }

    protected function processIf($matches)
    {
        $condition = $matches[1];
        $template = '';
        if (!empty($this->data[$condition])) {
            $template = $this->render("if/$matches[2].html");
        }
        return $template;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __toString()
    {
        return $this->render($this->data['template'] ?? '');
    }
}
