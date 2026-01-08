<?php
declare(strict_types=1);
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
exit();
/**
 * SVG背景图像生成器 - 重构版本
 * 支持多种背景类型和动画效果
 * 
 * @version 2.0
 * @license MIT
 */

class SVGBackgroundGenerator
{
    private array $params;
    private array $config;
    
    // 支持的背景类型
    private const VALID_TYPES = ['gradient', 'animated', 'particles', 'waves', 'geometric'];
    
    // 支持的渐变方向
    private const VALID_DIRECTIONS = ['vertical', 'horizontal', 'diagonal', 'radial'];
    
    // 支持的图案类型
    private const VALID_PATTERNS = ['none', 'triangles', 'circles', 'lines', 'squares'];
    
    // 支持的动画形状
    private const VALID_SHAPES = ['wave', 'pulse', 'rotate'];

    public function __construct(array $queryParams)
    {
        $this->params = $queryParams;
        $this->config = $this->validateAndSanitizeParams();
    }

    /**
     * 验证和清理参数
     */
    private function validateAndSanitizeParams(): array
    {
        $config = [
            'type' => $this->getValidatedType(),
            'color1' => $this->validateColor($this->params['color1'] ?? '', '#A8EDEA'),
            'color2' => $this->validateColor($this->params['color2'] ?? '', '#FED6E3'),
            'width' => $this->clamp((int)($this->params['width'] ?? 2000), 100, 5000),
            'height' => $this->clamp((int)($this->params['height'] ?? 2000), 100, 5000),
            'speed' => $this->clamp((float)($this->params['speed'] ?? 2.0), 0.5, 20.0),
            'direction' => $this->getValidatedDirection(),
            'pattern' => $this->getValidatedPattern(),
            'animate' => filter_var($this->params['animate'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'shape' => $this->getValidatedShape(),
            'particleCount' => $this->clamp((int)($this->params['particleCount'] ?? 30), 10, 200),
        ];

        return $config;
    }

    /**
     * 生成SVG内容
     */
    public function generate(): string
    {
        $gradient = $this->generateGradient();
        $background = $this->generateBackgroundContent();
        $pattern = $this->generatePatternContent();
        
        return $this->wrapSVG($gradient['defs'], $background, $pattern, $gradient['use']);
    }

    /**
     * 生成渐变定义
     */
    private function generateGradient(): array
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        $color1 = $this->config['color1'];
        $color2 = $this->config['color2'];
        $direction = $this->config['direction'];

        $gradientMap = [
            'vertical' => [
                'defs' => "<linearGradient id=\"mainGradient\" gradientUnits=\"userSpaceOnUse\" 
                    x1=\"{$width/2}\" y1=\"{$height}\" x2=\"{$width/2}\" y2=\"0\">
                    <stop offset=\"0\" style=\"stop-color:{$color1}\"/>
                    <stop offset=\"1\" style=\"stop-color:{$color2}\"/>
                </linearGradient>",
                'use' => "url(#mainGradient)"
            ],
            'horizontal' => [
                'defs' => "<linearGradient id=\"mainGradient\" gradientUnits=\"userSpaceOnUse\" 
                    x1=\"0\" y1=\"{$height/2}\" x2=\"{$width}\" y2=\"{$height/2}\">
                    <stop offset=\"0\" style=\"stop-color:{$color1}\"/>
                    <stop offset=\"1\" style=\"stop-color:{$color2}\"/>
                </linearGradient>",
                'use' => "url(#mainGradient)"
            ],
            'diagonal' => [
                'defs' => "<linearGradient id=\"mainGradient\" gradientUnits=\"userSpaceOnUse\" 
                    x1=\"0\" y1=\"{$height}\" x2=\"{$width}\" y2=\"0\">
                    <stop offset=\"0\" style=\"stop-color:{$color1}\"/>
                    <stop offset=\"1\" style=\"stop-color:{$color2}\"/>
                </linearGradient>",
                'use' => "url(#mainGradient)"
            ],
            'radial' => [
                'defs' => "<radialGradient id=\"mainGradient\" 
                    cx=\"{$width/2}\" cy=\"{$height/2}\" r=\"" . max($width, $height)/2 . "\" 
                    gradientUnits=\"userSpaceOnUse\">
                    <stop offset=\"0\" style=\"stop-color:{$color1}\"/>
                    <stop offset=\"1\" style=\"stop-color:{$color2}\"/>
                </radialGradient>",
                'use' => "url(#mainGradient)"
            ]
        ];

        return $gradientMap[$direction] ?? $gradientMap['vertical'];
    }

    /**
     * 生成背景内容
     */
    private function generateBackgroundContent(): string
    {
        $methodName = 'generate' . ucfirst($this->config['type']) . 'Background';
        
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        
        return $this->generateGradientBackground();
    }

    /**
     * 生成渐变背景
     */
    private function generateGradientBackground(): string
    {
        return "<rect width=\"{$this->config['width']}\" height=\"{$this->config['height']}\" class=\"background\"/>";
    }

    /**
     * 生成动画背景
     */
    private function generateAnimatedBackground(): string
    {
        $methodName = 'generate' . ucfirst($this->config['shape']) . 'Animation';
        
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        
        return $this->generateWaveAnimation();
    }

    /**
     * 生成波浪动画
     */
    private function generateWaveAnimation(): string
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        $color1 = $this->config['color1'];
        $color2 = $this->config['color2'];
        $speed = $this->config['speed'];
        
        $wavePath = $this->generateWavePath($width, $height, 0);
        $wavePath2 = $this->generateWavePath($width, $height, 1.57);
        
        $duration = 4 / $speed;
        
        return <<<SVG
<rect width="{$width}" height="{$height}" fill="{$color2}"/>
<path d="{$wavePath}" fill="{$color1}" fill-opacity="0.3">
    <animate attributeName="d" values="{$wavePath};{$wavePath2};{$wavePath}" 
             dur="{$duration}s" repeatCount="indefinite"/>
</path>
SVG;
    }

    /**
     * 生成波浪路径
     */
    private function generateWavePath(int $width, int $height, float $phase): string
    {
        $path = "M0," . ($height / 2) . " ";
        
        for ($x = 0; $x <= $width; $x += 50) {
            $waveY = $height / 2 + sin($x * 0.02 + $phase) * 100;
            $path .= "C" . ($x + 25) . "," . ($waveY - 50) . " " . 
                    ($x + 25) . "," . ($waveY + 50) . " " . 
                    ($x + 50) . ",{$waveY} ";
        }
        
        $path .= "L{$width},{$height} L0,{$height} Z";
        return $path;
    }

    /**
     * 生成粒子背景
     */
    private function generateParticlesBackground(): string
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        $color1 = $this->config['color1'];
        $speed = $this->config['speed'];
        $animate = $this->config['animate'];
        $particleCount = $this->config['particleCount'];
        
        $particles = '';
        $durationBase = 8 / $speed;
        
        for ($i = 0; $i < $particleCount; $i++) {
            $x = random_int(0, $width);
            $y = random_int(0, $height);
            $size = random_int(2, 8);
            $opacity = random_int(1, 8) / 10;
            $duration = $durationBase + random_int(-2, 2);
            $delay = random_int(0, (int)$duration);
            
            $particle = "<circle cx=\"{$x}\" cy=\"{$y}\" r=\"{$size}\" 
                         fill=\"{$color1}\" fill-opacity=\"{$opacity}\"";
            
            if ($animate) {
                $targetX = random_int(0, $width);
                $targetY = random_int(0, $height);
                
                $particle .= ">
                    <animate attributeName=\"cx\" values=\"{$x};{$targetX};{$x}\" 
                             dur=\"{$duration}s\" repeatCount=\"indefinite\" begin=\"{$delay}s\"/>
                    <animate attributeName=\"cy\" values=\"{$y};{$targetY};{$y}\" 
                             dur=\"{$duration}s\" repeatCount=\"indefinite\" begin=\"{$delay}s\"/>
                </circle>";
            } else {
                $particle .= "/>";
            }
            
            $particles .= $particle;
        }
        
        return $particles;
    }

    /**
     * 生成几何背景
     */
    private function generateGeometricBackground(): string
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        $animate = $this->config['animate'];
        $speed = $this->config['speed'];
        
        $shapes = ['triangle', 'square', 'hexagon', 'circle'];
        $geometric = '';
        $durationBase = 8 / $speed;
        
        for ($i = 0; $i < 50; $i++) {
            $x = random_int(0, $width);
            $y = random_int(0, $height);
            $size = random_int(10, 40);
            $rotation = random_int(0, 360);
            $shapeType = $shapes[array_rand($shapes)];
            $opacity = random_int(3, 8) / 10;
            
            $shapeContent = $this->generateShape($shapeType, $x, $y, $size, $opacity);
            
            if ($animate) {
                $duration = $durationBase + random_int(-2, 2);
                $delay = random_int(0, (int)$duration);
                
                $geometric .= "<g transform=\"rotate({$rotation} {$x} {$y})\">
                    {$shapeContent}
                    <animateTransform attributeName=\"transform\" type=\"rotate\" 
                        values=\"{$rotation} {$x} {$y};" . ($rotation + 360) . " {$x} {$y}\" 
                        dur=\"{$duration}s\" repeatCount=\"indefinite\" begin=\"{$delay}s\" />
                </g>";
            } else {
                $geometric .= "<g transform=\"rotate({$rotation} {$x} {$y})\">{$shapeContent}</g>";
            }
        }
        
        return $geometric;
    }

    /**
     * 生成单个形状
     */
    private function generateShape(string $type, int $x, int $y, int $size, float $opacity): string
    {
        $gradientId = "geoGradient";
        
        switch ($type) {
            case 'triangle':
                $points = "{$x},{$y} " . ($x + $size) . ",{$y} " . ($x + $size/2) . "," . ($y + $size);
                return "<polygon points=\"{$points}\" fill=\"url(#{$gradientId})\" fill-opacity=\"{$opacity}\"/>";
                
            case 'square':
                return "<rect x=\"" . ($x - $size/2) . "\" y=\"" . ($y - $size/2) . "\" 
                        width=\"{$size}\" height=\"{$size}\" fill=\"url(#{$gradientId})\" fill-opacity=\"{$opacity}\"/>";
                
            case 'hexagon':
                $points = [];
                for ($j = 0; $j < 6; $j++) {
                    $angle = $j * 60 * M_PI / 180;
                    $px = $x + $size * cos($angle);
                    $py = $y + $size * sin($angle);
                    $points[] = $px . ',' . $py;
                }
                return "<polygon points=\"" . implode(' ', $points) . "\" 
                        fill=\"url(#{$gradientId})\" fill-opacity=\"{$opacity}\"/>";
                
            case 'circle':
            default:
                return "<circle cx=\"{$x}\" cy=\"{$y}\" r=\"" . ($size/2) . "\" 
                        fill=\"url(#{$gradientId})\" fill-opacity=\"{$opacity}\"/>";
        }
    }

    /**
     * 生成图案内容
     */
    private function generatePatternContent(): string
    {
        if ($this->config['pattern'] === 'none') {
            return '';
        }
        
        $methodName = 'generate' . ucfirst($this->config['pattern']) . 'Pattern';
        
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        
        return '';
    }

    /**
     * 生成三角形图案
     */
    private function generateTrianglesPattern(): string
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        $color1 = $this->config['color1'];
        
        $pattern = '';
        for ($i = 0; $i < 100; $i++) {
            $x = random_int(0, $width);
            $y = random_int(0, $height);
            $size = random_int(5, 15);
            $opacity = random_int(1, 5) / 10;
            
            $points = "{$x},{$y} " . ($x + $size) . ",{$y} " . ($x + $size/2) . "," . ($y + $size);
            $pattern .= "<polygon points=\"{$points}\" fill=\"{$color1}\" fill-opacity=\"{$opacity}\"/>";
        }
        
        return $pattern;
    }

    /**
     * 生成圆形图案
     */
    private function generateCirclesPattern(): string
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        $color1 = $this->config['color1'];
        
        $pattern = '';
        for ($i = 0; $i < 100; $i++) {
            $x = random_int(0, $width);
            $y = random_int(0, $height);
            $size = random_int(3, 12);
            $opacity = random_int(1, 5) / 10;
            
            $pattern .= "<circle cx=\"{$x}\" cy=\"{$y}\" r=\"{$size}\" 
                          fill=\"{$color1}\" fill-opacity=\"{$opacity}\"/>";
        }
        
        return $pattern;
    }

    /**
     * 包装SVG内容
     */
    private function wrapSVG(string $defs, string $background, string $pattern, string $gradientUse): string
    {
        $width = $this->config['width'];
        $height = $this->config['height'];
        
        // 添加几何渐变定义
        if ($this->config['type'] === 'geometric') {
            $color1 = $this->config['color1'];
            $color2 = $this->config['color2'];
            $defs .= "
            <linearGradient id=\"geoGradient\" x1=\"0%\" y1=\"0%\" x2=\"100%\" y2=\"100%\">
                <stop offset=\"0%\" stop-color=\"{$color1}\"/>
                <stop offset=\"100%\" stop-color=\"{$color2}\"/>
            </linearGradient>";
        }
        
        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" 
     xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
        {$defs}
    </defs>
    <style>
        .background { fill: {$gradientUse}; }
    </style>
    <g>
        {$background}
        {$pattern}
    </g>
</svg>
SVG;
    }

    /**
     * 验证颜色格式
     */
    private function validateColor(string $color, string $default): string
    {
        if (empty($color)) {
            return $default;
        }
        
        $color = '#' . ltrim($color, '#');
        
        return preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : $default;
    }

    /**
     * 数值范围限制
     */
    private function clamp($value, $min, $max)
    {
        return max($min, min($max, $value));
    }

    /**
     * 验证类型参数
     */
    private function getValidatedType(): string
    {
        $type = $this->params['type'] ?? 'gradient';
        return in_array($type, self::VALID_TYPES) ? $type : 'gradient';
    }

    /**
     * 验证方向参数
     */
    private function getValidatedDirection(): string
    {
        $direction = $this->params['direction'] ?? 'vertical';
        return in_array($direction, self::VALID_DIRECTIONS) ? $direction : 'vertical';
    }

    /**
     * 验证图案参数
     */
    private function getValidatedPattern(): string
    {
        $pattern = $this->params['pattern'] ?? 'none';
        return in_array($pattern, self::VALID_PATTERNS) ? $pattern : 'none';
    }

    /**
     * 验证形状参数
     */
    private function getValidatedShape(): string
    {
        $shape = $this->params['shape'] ?? 'wave';
        return in_array($shape, self::VALID_SHAPES) ? $shape : 'wave';
    }
}

/**
 * 处理HTTP缓存
 */
function handleCache(array $params): bool
{
    ksort($params);
    $etag = md5(serialize($params));
    
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
        $_SERVER['HTTP_IF_NONE_MATCH'] === "\"{$etag}\"") {
        header('HTTP/1.1 304 Not Modified');
        return true;
    }
    
    header("ETag: \"{$etag}\"");
    header('Cache-Control: public, max-age=31536000');
    return false;
}

/**
 * 主执行逻辑
 */
try {
    // 处理缓存
    if (handleCache($_GET)) {
        exit;
    }
    
    // 生成SVG
    $generator = new SVGBackgroundGenerator($_GET);
    $svg = $generator->generate();
    
    // 输出结果
    header('Content-Type: image/svg+xml');
    echo $svg;
    
} catch (Exception $e) {
    // 错误处理
    header('Content-Type: image/svg+xml');
    header('HTTP/1.1 500 Internal Server Error');
    
    $errorSvg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="200" height="100" viewBox="0 0 200 100" xmlns="http://www.w3.org/2000/svg">
    <rect width="200" height="100" fill="#f8d7da"/>
    <text x="100" y="50" text-anchor="middle" fill="#721c24" font-family="Arial" font-size="12">
        错误: 无法生成SVG背景
    </text>
</svg>
SVG;
    
    echo $errorSvg;
}
?>