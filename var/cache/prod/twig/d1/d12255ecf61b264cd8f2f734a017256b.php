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

/* @NelmioApiDoc/Redocly/index.html.twig */
class __TwigTemplate_e656d2f881fae8124d8f67888ecec6a5 extends Template
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
            'swagger_ui' => [$this, 'block_swagger_ui'],
            'javascripts' => [$this, 'block_javascripts'],
            'swagger_initialization' => [$this, 'block_swagger_initialization'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    ";
        // line 4
        yield from $this->unwrap()->yieldBlock('meta', $context, $blocks);
        // line 8
        yield "    <title>";
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>

    ";
        // line 10
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 22
        yield "
    ";
        // line 23
        yield from $this->unwrap()->yieldBlock('swagger_data', $context, $blocks);
        // line 27
        yield "</head>
<body>
    ";
        // line 29
        yield from $this->unwrap()->yieldBlock('swagger_ui', $context, $blocks);
        // line 32
        yield "
    ";
        // line 33
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 36
        yield "
    ";
        // line 37
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke(($context["assets_mode"] ?? null), "init-redocly-ui.js");
        yield "

    ";
        // line 39
        yield from $this->unwrap()->yieldBlock('swagger_initialization', $context, $blocks);
        // line 46
        yield "</body>
</html>
";
        yield from [];
    }

    // line 4
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_meta(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 5
        yield "        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"/>
    ";
        yield from [];
    }

    // line 8
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["swagger_data"] ?? null), "spec", [], "any", false, false, false, 8), "info", [], "any", false, false, false, 8), "title", [], "any", false, false, false, 8), "html", null, true);
        yield from [];
    }

    // line 10
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 11
        yield "        <link
                href=\"https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Roboto:300,400,700\"
                rel=\"stylesheet\"
        />
        <style>
            body {
                margin: 0;
                padding: 0;
            }
        </style>
    ";
        yield from [];
    }

    // line 23
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_swagger_data(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 24
        yield "        ";
        // line 25
        yield "        <script id=\"swagger-data\" type=\"application/json\">";
        yield json_encode(($context["swagger_data"] ?? null), 65);
        yield "</script>
    ";
        yield from [];
    }

    // line 29
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_swagger_ui(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 30
        yield "        <div id=\"swagger-ui\"></div>
    ";
        yield from [];
    }

    // line 33
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 34
        yield "        ";
        yield $this->extensions['Nelmio\ApiDocBundle\Render\Html\GetNelmioAsset']->__invoke(($context["assets_mode"] ?? null), "redocly/redoc.standalone.js");
        yield "
    ";
        yield from [];
    }

    // line 39
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_swagger_initialization(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 40
        yield "        <script type=\"text/javascript\">
            window.onload = () => {
                loadRedocly(";
        // line 42
        yield json_encode(($context["redocly_config"] ?? null), 81);
        yield ");
            };
        </script>
    ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@NelmioApiDoc/Redocly/index.html.twig";
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
        return array (  200 => 42,  196 => 40,  189 => 39,  181 => 34,  174 => 33,  168 => 30,  161 => 29,  153 => 25,  151 => 24,  144 => 23,  129 => 11,  122 => 10,  111 => 8,  104 => 5,  97 => 4,  90 => 46,  88 => 39,  83 => 37,  80 => 36,  78 => 33,  75 => 32,  73 => 29,  69 => 27,  67 => 23,  64 => 22,  62 => 10,  56 => 8,  54 => 4,  49 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@NelmioApiDoc/Redocly/index.html.twig", "/home/koszudikas/Documentos/Project/api_login_cl_ativo_byte/vendor/nelmio/api-doc-bundle/templates/Redocly/index.html.twig");
    }
}
