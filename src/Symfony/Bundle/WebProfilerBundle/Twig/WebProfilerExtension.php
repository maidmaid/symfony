<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\WebProfilerBundle\Twig;

use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Twig extension for the profiler.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class WebProfilerExtension extends \Twig_Extension_Profiler
{
    /**
     * @var ValueExporter
     */
    private $valueExporter;

    private $help;

    /**
     * @var HtmlDumper
     */
    private $dumper;

    /**
     * @var resource
     */
    private $output;

    /**
     * @var int
     */
    private $stackLevel = 0;

    public function __construct(HtmlDumper $dumper = null, $help = false)
    {
        $this->dumper = $dumper ?: new HtmlDumper();
        $this->dumper->setOutput($this->output = fopen('php://memory', 'r+b'));
        $this->help = $help;
    }

    public function enter(\Twig_Profiler_Profile $profile)
    {
        ++$this->stackLevel;
    }

    public function leave(\Twig_Profiler_Profile $profile)
    {
        if (0 === --$this->stackLevel) {
            $this->dumper->setOutput($this->output = fopen('php://memory', 'r+b'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $profilerDump = function (\Twig_Environment $env, $value, $maxDepth = 0) {
            return $value instanceof Data ? $this->dumpData($env, $value, $maxDepth) : twig_escape_filter($env, $this->dumpValue($value));
        };

        return array(
            new \Twig_SimpleFunction('profiler_dump', $profilerDump, array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('profiler_dump_log', array($this, 'dumpLog'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('help', array($this, 'help'), array('is_safe' => array('html'))),
        );
    }

    public function dumpData(\Twig_Environment $env, Data $data, $maxDepth = 0)
    {
        $this->dumper->setCharset($env->getCharset());
        $this->dumper->dump($data, null, array(
            'maxDepth' => $maxDepth,
        ));

        $dump = stream_get_contents($this->output, -1, 0);
        rewind($this->output);
        ftruncate($this->output, 0);

        return str_replace("\n</pre", '</pre', rtrim($dump));
    }

    public function dumpLog(\Twig_Environment $env, $message, Data $context)
    {
        $message = twig_escape_filter($env, $message);

        if (false === strpos($message, '{')) {
            return '<span class="dump-inline">'.$message.'</span>';
        }

        $replacements = array();
        foreach ($context->getRawData()[1] as $k => $v) {
            $v = '{'.twig_escape_filter($env, $k).'}';
            $replacements['&quot;'.$v.'&quot;'] = $replacements[$v] = $this->dumpData($env, $context->seek($k));
        }

        return '<span class="dump-inline">'.strtr($message, $replacements).'</span>';
    }

    /**
     * @deprecated since 3.2, to be removed in 4.0. Use the dumpData() method instead.
     */
    public function dumpValue($value)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 3.2 and will be removed in 4.0. Use the dumpData() method instead.', __METHOD__), E_USER_DEPRECATED);

        if (null === $this->valueExporter) {
            $this->valueExporter = new ValueExporter();
        }

        return $this->valueExporter->exportValue($value);
    }

    public function help($links)
    {
        if (!$this->help) {
            return '';
        }

        $attrs = [];

        if (isset($links['doc'])) {
            $attrs['data-help-doc'] = sprintf(
                'https://symfony.com/doc/%s/%s',
                Kernel::MAJOR_VERSION.'.'.Kernel::MINOR_VERSION,
                $links['doc']
            );
        }

        if (isset($links['api'])) {
            $attrs['data-help-api'] = sprintf(
                'http://api.symfony.com/%s/%s',
                Kernel::MAJOR_VERSION.'.'.Kernel::MINOR_VERSION,
                $links['api']
            );
        }

        $html = '';
        foreach ($attrs as $k => $v) {
            $html .= $k.'="'.$v.'" ';
        }

        return $html;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'profiler';
    }
}
