<?php
/**
 * 通用SVG文字图像生成工具
 * 可通过URL参数控制SVG的各种属性
 *
 * 参数说明：
 * @param string $text 要显示的文字（默认：SVG）
 * @param string $color 文字颜色（默认：#ffffff）
 * @param string $bgcolor 背景颜色（默认：#1890ff）
 * @param int $size 字体大小（默认：24）
 * @param int $width 图像宽度（默认：120）
 * @param int $height 图像高度（默认：60）
 * @param string $font 字体名称（默认：Arial, sans-serif）
 * @param int $radius 圆角半径（默认：8）
 * @param string $border 边框颜色（默认：transparent）
 * @param int $borderWidth 边框宽度（默认：0）
 * @param string $shadow 阴影效果（默认：2px 2px 4px rgba(0,0,0,0.2)）
 * @param bool $autoWrap 是否自动换行（默认：true）
 * @param string $lineBreak 手动换行符（默认：\n）
 */

// 获取并验证参数
$text = isset($_GET['text']) ? $_GET['text'] : 'SVG';
$color = isset($_GET['color']) ? '#' . ltrim($_GET['color'], '#') : '#ffffff';
$bgColor = isset($_GET['bgcolor']) ? '#' . ltrim($_GET['bgcolor'], '#') : '#1890ff';
$size = isset($_GET['size']) ? max(8, min(100, (int)$_GET['size'])) : 24;
$width = isset($_GET['width']) ? max(20, min(1000, (int)$_GET['width'])) : 120;
$height = isset($_GET['height']) ? max(20, min(1000, (int)$_GET['height'])) : 60;
$font = isset($_GET['font']) ? $_GET['font'] : 'Arial, sans-serif';
$radius = isset($_GET['radius']) ? max(0, min(50, (int)$_GET['radius'])) : 8;
$border = isset($_GET['border']) ? '#' . ltrim($_GET['border'], '#') : 'transparent';
$borderWidth = isset($_GET['borderWidth']) ? max(0, min(10, (int)$_GET['borderWidth'])) : 0;
$shadow = isset($_GET['shadow']) ? $_GET['shadow'] : '2px 2px 4px rgba(0,0,0,0.2)';
$autoWrap = isset($_GET['autoWrap']) ? filter_var($_GET['autoWrap'], FILTER_VALIDATE_BOOLEAN) : true;
$lineBreak = isset($_GET['lineBreak']) ? $_GET['lineBreak'] : "\n";

// 计算文字宽度（考虑中英文混合）
function calculateTextWidth($text) {
    $width = 0;
    for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        // 中文、日文、韩文等宽字符按2个单位计算，其他按1个单位计算
        $width += (preg_match('/[\x{4e00}-\x{9fa5}\x{3040}-\x{30ff}\x{ac00}-\x{d7af}]/u', $char)) ? 2 : 1;
    }
    return $width;
}

// 文本自动换行函数
function autoWrapText($text, $maxWidth, $size, $font) {
    $lines = [];
    $currentLine = '';
    $currentWidth = 0;
    $charUnitWidth = $size * 0.5; // 每个单位宽度对应的像素值
    
    // 计算每行最大可容纳的字符单位数
    $maxUnitsPerLine = ($maxWidth / $charUnitWidth) * 0.9; // 0.9为安全系数
    
    for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        
        // 处理空格和标点符号的特殊情况
        if (trim($char) === '') {
            if (!empty($currentLine)) {
                $lines[] = trim($currentLine);
                $currentLine = '';
                $currentWidth = 0;
            }
            continue;
        }
        
        $charWidth = (preg_match('/[\x{4e00}-\x{9fa5}\x{3040}-\x{30ff}\x{ac00}-\x{d7af}]/u', $char)) ? 2 : 1;
        
        if ($currentWidth + $charWidth > $maxUnitsPerLine) {
            if (!empty($currentLine)) {
                $lines[] = trim($currentLine);
            }
            $currentLine = $char;
            $currentWidth = $charWidth;
        } else {
            $currentLine .= $char;
            $currentWidth += $charWidth;
        }
    }
    
    if (!empty($currentLine)) {
        $lines[] = trim($currentLine);
    }
    
    return $lines;
}

// 处理换行
$maxWidth = $width - 20; // 考虑左右边距各10px
$lineHeight = $size * 1.3; // 行高为字体大小的1.3倍（增加行间距）
$lines = [];

// 首先处理手动换行
$manualLines = explode($lineBreak, $text);

foreach ($manualLines as $manualLine) {
    if ($autoWrap) {
        // 始终进行自动换行处理，确保文本在有限空间内正确显示
        $autoWrappedLines = autoWrapText($manualLine, $maxWidth, $size, $font);
        $lines = array_merge($lines, $autoWrappedLines);
    } else {
        $lines[] = $manualLine;
    }
}

// 限制最大行数，确保有足够空间显示多行
$maxLines = (int)floor(($height - 10) / $lineHeight); // 减少上下边距限制，增加可用空间
$maxLines = max($maxLines, 1); // 至少显示一行

if (count($lines) > $maxLines) {
    $lines = array_slice($lines, 0, $maxLines);
    // 只在确实需要时添加省略号
    if (mb_strlen(end($lines), 'UTF-8') > 2) {
        $lines[count($lines) - 1] = rtrim(mb_substr(end($lines), 0, -1, 'UTF-8'), '.,;') . '...';
    }
}

// 计算文本总高度
$totalTextHeight = count($lines) * $lineHeight;

// 生成多行文本的SVG代码
$textSvg = '';
$startY = $height / 2; // 设置整体垂直居中

// 计算第一行的调整值，确保多行文本整体居中
$firstLineAdjust = 0;
if (count($lines) > 1) {
    $firstLineAdjust = -((count($lines) - 1) * $lineHeight) / 2;
}

foreach ($lines as $index => $line) {
    if ($index === 0) {
        // 第一行文本，设置正确的起始位置
        $textSvg .= "<tspan x=\"50%\" y=\"{$startY}\" dy=\"{$firstLineAdjust}\">{$line}</tspan>";
    } else {
        // 后续行只使用dy属性控制行间距，不使用y属性
        $textSvg .= "<tspan x=\"50%\" dy=\"{$lineHeight}\">{$line}</tspan>";
    }
}

// 生成SVG
$svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <!-- 背景矩形 -->
  <rect 
    width="{$width}" 
    height="{$height}" 
    fill="{$bgColor}" 
    rx="{$radius}" 
    ry="{$radius}"
    stroke="{$border}"
    stroke-width="{$borderWidth}"
    filter="drop-shadow({$shadow})"
  />
  <!-- 文字 -->
  <text 
    x="50%" 
    y="{$startY}" 
    font-family="{$font}" 
    font-size="{$size}" 
    fill="{$color}" 
    text-anchor="middle" 
    dominant-baseline="middle"
    font-weight="500"
  >{$textSvg}</text>
</svg>
SVG;

// 设置HTTP头并输出
header('Content-Type: image/svg+xml');
// header('Cache-Control: public, max-age=31536000');
echo $svg;