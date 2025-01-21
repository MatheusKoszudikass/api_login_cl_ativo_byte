<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* @NelmioApiDoc/SwaggerUi/index.html.twig */
class __TwigTemplate_50cb07cc6af55fb4fd5bab7b321f9d14 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'meta' => [$this, 'block_meta'],
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'swagger_data' => [$this, 'block_swagger_data'],
            'svg_icons' => [$this, 'block_svg_icons'],
            'header_block' => [$this, 'block_header_block'],
            'header' => [$this, 'block_header'],
            'swagger_ui' => [$this, 'block_swagger_ui'],
            'javascripts' => [$this, 'block_javascripts'],
            'swagger_initialization' => [$this, 'block_swagger_initialization'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@NelmioApiDoc/SwaggerUi/index.html.twig"));

        // line 7
        yield "
<!DOCTYPE html>
<html>
<head>
    ";
        // line 11
        yield from $this->unwrap()->yieldBlock('meta', $context, $blocks);
        // line 14
        yield "    <title>";
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>

    ";
        // line 16
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 20
        yield "
    ";
        // line 21
        yield from $this->unwrap()->yieldBlock('swagger_data', $context, $blocks);
        // line 25
        yield "</head>
<body>
    ";
        // line 27
        yield from $this->unwrap()->yieldBlock('svg_icons', $context, $blocks);
        // line 54
        yield "    
    ";
        // line 55
        yield from $this->unwrap()->yieldBlock('header_block', $context, $blocks);
        // line 64
        yield "
    ";
        // line 65
        yield from $this->unwrap()->yieldBlock('swagger_ui', $context, $blocks);
        // line 68
        yield "
    ";
        // line 69
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 73
        yield "
    ";
        // line 74
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke((isset($context["assets_mode"]) || array_key_exists("assets_mode", $context) ? $context["assets_mode"] : (function () { throw new RuntimeError('Variable "assets_mode" does not exist.', 74, $this->source); })()), "init-swagger-ui.js");
        yield "

    ";
        // line 76
        yield from $this->unwrap()->yieldBlock('swagger_initialization', $context, $blocks);
        // line 83
        yield "</body>
</html>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 11
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_meta(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "meta"));

        // line 12
        yield "        <meta charset=\"UTF-8\">
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 14
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["swagger_data"]) || array_key_exists("swagger_data", $context) ? $context["swagger_data"] : (function () { throw new RuntimeError('Variable "swagger_data" does not exist.', 14, $this->source); })()), "spec", [], "any", false, false, false, 14), "info", [], "any", false, false, false, 14), "title", [], "any", false, false, false, 14), "html", null, true);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 16
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 17
        yield "        ";
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke((isset($context["assets_mode"]) || array_key_exists("assets_mode", $context) ? $context["assets_mode"] : (function () { throw new RuntimeError('Variable "assets_mode" does not exist.', 17, $this->source); })()), "swagger-ui/swagger-ui.css");
        yield "
        ";
        // line 18
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke((isset($context["assets_mode"]) || array_key_exists("assets_mode", $context) ? $context["assets_mode"] : (function () { throw new RuntimeError('Variable "assets_mode" does not exist.', 18, $this->source); })()), "style.css");
        yield "
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 21
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_swagger_data(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "swagger_data"));

        // line 22
        yield "        ";
        // line 23
        yield "        <script id=\"swagger-data\" type=\"application/json\">";
        yield json_encode((isset($context["swagger_data"]) || array_key_exists("swagger_data", $context) ? $context["swagger_data"] : (function () { throw new RuntimeError('Variable "swagger_data" does not exist.', 23, $this->source); })()), 65);
        yield "</script>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 27
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_svg_icons(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "svg_icons"));

        // line 28
        yield "        <svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" id=\"swagger-ui-logos\">
            <defs>
                <symbol viewBox=\"0 0 20 20\" id=\"unlocked\">
                    <path d=\"M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V6h2v-.801C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"locked\">
                    <path d=\"M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8zM12 8H8V5.199C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"close\">
                    <path d=\"M14.348 14.849c-.469.469-1.229.469-1.697 0L10 11.819l-2.651 3.029c-.469.469-1.229.469-1.697 0-.469-.469-.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-.469-.469-.469-1.228 0-1.697.469-.469 1.228-.469 1.697 0L10 8.183l2.651-3.031c.469-.469 1.228-.469 1.697 0 .469.469.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c.469.469.469 1.229 0 1.698z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"large-arrow\">
                    <path d=\"M13.25 10L6.109 2.58c-.268-.27-.268-.707 0-.979.268-.27.701-.27.969 0l7.83 7.908c.268.271.268.709 0 .979l-7.83 7.908c-.268.271-.701.27-.969 0-.268-.269-.268-.707 0-.979L13.25 10z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"large-arrow-down\">
                    <path d=\"M17.418 6.109c.272-.268.709-.268.979 0s.271.701 0 .969l-7.908 7.83c-.27.268-.707.268-.979 0l-7.908-7.83c-.27-.268-.27-.701 0-.969.271-.268.709-.268.979 0L10 13.25l7.418-7.141z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 24 24\" id=\"jump-to\">
                    <path d=\"M19 7v4H5.83l3.58-3.59L8 6l-6 6 6 6 1.41-1.41L5.83 13H21V7z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 24 24\" id=\"expand\">
                    <path d=\"M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z\"></path>
                </symbol>
            </defs>
        </svg>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 55
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_header_block(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "header_block"));

        // line 56
        yield "        <header>
            ";
        // line 57
        yield from $this->unwrap()->yieldBlock('header', $context, $blocks);
        // line 62
        yield "        </header>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 57
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_header(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "header"));

        // line 58
        yield "                <a id=\"logo\" href=\"https://github.com/nelmio/NelmioApiDocBundle\">
                    <img src=\"";
        // line 59
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke((isset($context["assets_mode"]) || array_key_exists("assets_mode", $context) ? $context["assets_mode"] : (function () { throw new RuntimeError('Variable "assets_mode" does not exist.', 59, $this->source); })()), "logo.png");
        yield "\" alt=\"NelmioApiDocBundle\">
                </a>
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 65
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_swagger_ui(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "swagger_ui"));

        // line 66
        yield "        <div id=\"swagger-ui\" class=\"api-platform\"></div>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 69
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 70
        yield "        ";
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke((isset($context["assets_mode"]) || array_key_exists("assets_mode", $context) ? $context["assets_mode"] : (function () { throw new RuntimeError('Variable "assets_mode" does not exist.', 70, $this->source); })()), "swagger-ui/swagger-ui-bundle.js");
        yield "
        ";
        // line 71
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke((isset($context["assets_mode"]) || array_key_exists("assets_mode", $context) ? $context["assets_mode"] : (function () { throw new RuntimeError('Variable "assets_mode" does not exist.', 71, $this->source); })()), "swagger-ui/swagger-ui-standalone-preset.js");
        yield "
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 76
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_swagger_initialization(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "swagger_initialization"));

        // line 77
        yield "        <script type=\"text/javascript\">
            window.onload = () => {
                loadSwaggerUI(";
        // line 79
        yield json_encode((isset($context["swagger_ui_config"]) || array_key_exists("swagger_ui_config", $context) ? $context["swagger_ui_config"] : (function () { throw new RuntimeError('Variable "swagger_ui_config" does not exist.', 79, $this->source); })()), 65);
        yield ");
            };
        </script>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@NelmioApiDoc/SwaggerUi/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  350 => 79,  346 => 77,  336 => 76,  326 => 71,  321 => 70,  311 => 69,  302 => 66,  292 => 65,  281 => 59,  278 => 58,  268 => 57,  259 => 62,  257 => 57,  254 => 56,  244 => 55,  211 => 28,  201 => 27,  190 => 23,  188 => 22,  178 => 21,  168 => 18,  163 => 17,  153 => 16,  136 => 14,  127 => 12,  117 => 11,  107 => 83,  105 => 76,  100 => 74,  97 => 73,  95 => 69,  92 => 68,  90 => 65,  87 => 64,  85 => 55,  82 => 54,  80 => 27,  76 => 25,  74 => 21,  71 => 20,  69 => 16,  63 => 14,  61 => 11,  55 => 7,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# This file is part of the API Platform project.

(c) Kévin Dunglas <dunglas@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code. #}

<!DOCTYPE html>
<html>
<head>
    {% block meta %}
        <meta charset=\"UTF-8\">
    {% endblock meta %}
    <title>{% block title %}{{ swagger_data.spec.info.title }}{% endblock title %}</title>

    {% block stylesheets %}
        {{ nelmioAsset(assets_mode, 'swagger-ui/swagger-ui.css') }}
        {{ nelmioAsset(assets_mode, 'style.css') }}
    {% endblock stylesheets %}

    {% block swagger_data %}
        {# json_encode(65) is for JSON_UNESCAPED_SLASHES|JSON_HEX_TAG to avoid JS XSS #}
        <script id=\"swagger-data\" type=\"application/json\">{{ swagger_data|json_encode(65)|raw }}</script>
    {% endblock swagger_data %}
</head>
<body>
    {% block svg_icons %}
        <svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" id=\"swagger-ui-logos\">
            <defs>
                <symbol viewBox=\"0 0 20 20\" id=\"unlocked\">
                    <path d=\"M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V6h2v-.801C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"locked\">
                    <path d=\"M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8zM12 8H8V5.199C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"close\">
                    <path d=\"M14.348 14.849c-.469.469-1.229.469-1.697 0L10 11.819l-2.651 3.029c-.469.469-1.229.469-1.697 0-.469-.469-.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-.469-.469-.469-1.228 0-1.697.469-.469 1.228-.469 1.697 0L10 8.183l2.651-3.031c.469-.469 1.228-.469 1.697 0 .469.469.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c.469.469.469 1.229 0 1.698z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"large-arrow\">
                    <path d=\"M13.25 10L6.109 2.58c-.268-.27-.268-.707 0-.979.268-.27.701-.27.969 0l7.83 7.908c.268.271.268.709 0 .979l-7.83 7.908c-.268.271-.701.27-.969 0-.268-.269-.268-.707 0-.979L13.25 10z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 20 20\" id=\"large-arrow-down\">
                    <path d=\"M17.418 6.109c.272-.268.709-.268.979 0s.271.701 0 .969l-7.908 7.83c-.27.268-.707.268-.979 0l-7.908-7.83c-.27-.268-.27-.701 0-.969.271-.268.709-.268.979 0L10 13.25l7.418-7.141z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 24 24\" id=\"jump-to\">
                    <path d=\"M19 7v4H5.83l3.58-3.59L8 6l-6 6 6 6 1.41-1.41L5.83 13H21V7z\"></path>
                </symbol>
                <symbol viewBox=\"0 0 24 24\" id=\"expand\">
                    <path d=\"M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z\"></path>
                </symbol>
            </defs>
        </svg>
    {% endblock svg_icons %}
    
    {% block header_block %}
        <header>
            {% block header %}
                <a id=\"logo\" href=\"https://github.com/nelmio/NelmioApiDocBundle\">
                    <img src=\"{{ nelmioAsset(assets_mode, 'logo.png') }}\" alt=\"NelmioApiDocBundle\">
                </a>
            {% endblock header %}
        </header>
    {% endblock header_block %}

    {% block swagger_ui %}
        <div id=\"swagger-ui\" class=\"api-platform\"></div>
    {% endblock %}

    {% block javascripts %}
        {{ nelmioAsset(assets_mode, 'swagger-ui/swagger-ui-bundle.js') }}
        {{ nelmioAsset(assets_mode, 'swagger-ui/swagger-ui-standalone-preset.js') }}
    {% endblock javascripts %}

    {{ nelmioAsset(assets_mode, 'init-swagger-ui.js') }}

    {% block swagger_initialization %}
        <script type=\"text/javascript\">
            window.onload = () => {
                loadSwaggerUI({{ swagger_ui_config|json_encode(65)|raw }});
            };
        </script>
    {% endblock swagger_initialization %}
</body>
</html>
", "@NelmioApiDoc/SwaggerUi/index.html.twig", "/home/koszudikas/Documentos/Project/api_login_cl_ativo_byte/vendor/nelmio/api-doc-bundle/templates/SwaggerUi/index.html.twig");
    }
}
