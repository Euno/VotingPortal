<?php

class Barchart
{
    public function draw()
    {
        $data = [
            'Yes' => 80,
            'No' => 12,
            'Dunno' => 8
        ];

        $imageWidth = 700;
        $imageHeight = 600;

        $gridTop = 40;
        $gridLeft = 50;
        $gridBottom = 540;
        $gridRight = 650;
        $gridHeight = $gridBottom - $gridTop;
        $gridWidth = $gridRight - $gridLeft;

        $lineWidth = 0;
        $barWidth = 100;

        $font = './assets/fonts/euno/sf-ui-display-medium-58646be638f96.otf';
        $fontSize = 10;

        $labelMargin = 8;

        $yMaxValue = 100;

        $yLabelSpan = 40;

        $chart = imagecreate($imageWidth, $imageHeight);

        $backgroundColor = imagecolorallocatealpha($chart, 255, 255, 255, 0);
        $axisColor = imagecolorallocate($chart, 85, 85, 85);
        $labelColor = $axisColor;
        $gridColor = imagecolorallocate($chart, 212, 212, 212);
        $barColor = imagecolorallocate($chart, 47, 133, 217);
        $colorTextBars = imagecolorallocate($chart, 255, 255, 255);

        imagecolortransparent($chart, $backgroundColor);

        //imagefill($chart, 0, 0, $backgroundColor);

        imagesetthickness($chart, $lineWidth);

        $barSpacing = $gridWidth / count($data);
        $itemX = $gridLeft + $barSpacing / 2;

        foreach($data as $key => $value) {
            $x1 = $itemX - $barWidth / 2;
            $y1 = $gridBottom - $value / $yMaxValue * $gridHeight;
            $x2 = $itemX + $barWidth / 2;
            $y2 = $gridBottom - 1;

            imagefilledrectangle($chart, $x1, $y1, $x2, $y2, $barColor);

            $labelBox = imagettfbbox($fontSize, 0, $font, $key);
            $labelWidth = $labelBox[4] - $labelBox[0];

            $labelX = $itemX - $labelWidth / 2;
            $labelY = $gridBottom + $labelMargin + $fontSize;

            imagettftext($chart, $fontSize, 0, $labelX, $labelY, $colorTextBars, $font, $key);

            imagefttext($chart, $fontSize, 0, $x1+40, $y1-10, $colorTextBars, $font, $value.'%');

            $itemX += $barSpacing;
        }

        header('Content-Type: image/png');
        imagepng($chart);
    }
}