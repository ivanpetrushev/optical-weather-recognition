<?php

namespace App\Services;

use App\Camera;
use App\Image;
use Mockery\Exception;

class HistogramService
{
    public function createSomeHistograms()
    {
        $images = Image::where('histogram_lightness', null)
            ->with('camera')
            ->with('location')
            ->get();
        $images = $images->toArray();
//        print_r($images);
        print "Generating histograms for " . count($images) . " images...\n";
        $tsStart = time();
        foreach ($images as $image) {
            $this->generateHistogramForImage($image);
        }
        $tsEnd = time();
        $executionTime = ($tsEnd - $tsStart);
        print "Time taken: $executionTime seconds\n";
    }

    protected function generateHistogramForImage($image)
    {
        $histogramHue = [];
        $histogramSaturation = [];
        $histogramLightness = [];
        for ($i = 0; $i < 360; $i++) {
            $histogramHue[$i] = 0;
        }
        for ($i = 0; $i < 100; $i++) {
            $histogramSaturation[$i] = 0;
        }
        for ($i = 0; $i < 100; $i++) {
            $histogramLightness[$i] = 0;
        }


        $fullpath = $image['dir'] . '/' . $image['filename'];
        $maskFullpath = '/weather_data/' . $image['location']['name'] . '/' . $image['camera']['name'] . '/mask.jpg';

        $imMask = imagecreatefromjpeg($maskFullpath);
        try {
            $imImage = imagecreatefromjpeg($fullpath);
        } catch (\Exception $e) {
            print "\n\n Error processing image: $fullpath: " . $e->getMessage() . "\n\n";
            return;
        }
        list ($width, $height) = getimagesize($maskFullpath);

        // debug
//        $imOut = imagecreatetruecolor($width, $height);

        $cntPixelsCalculated = 0;

        for ($w = 0; $w < $width; $w++) {
            for ($h = 0; $h < $height; $h++) {
                $cntPixelsCalculated++;
                $color = imagecolorat($imMask, $w, $h);
                if ($color == 0) {
                    $rgb = imagecolorat($imImage, $w, $h);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    list ($hh, $s, $l) = $this->rgbToHsl($r, $g, $b);


                    // accumulate HSL distribution across the image
                    if (! isset($histogramHue[$hh])) {
                        $histogramHue[$hh] = 0;
                    }
                    $histogramHue[$hh]++;

                    if (! isset($histogramSaturation[$s])) {
                        $histogramSaturation[$s] = 0;
                    }
                    $histogramSaturation[$s]++;

                    if (! isset($histogramLightness[$l])) {
                        $histogramLightness[$l] = 0;
                    }
                    $histogramLightness[$l]++;
//                    $outcolor = imagecolorallocate($imOut, $r, $g, $b);
//                    imagesetpixel($imOut, $w, $h, $outcolor);
                }

            }
        }

//        print "HUE: ";
//        print_r($histogramHue);
//        print "SAT: ";
//        print_r($histogramSaturation);
//        print "LIGHTNESS: ";
//        print_r($histogramLightness);

        $im = Image::find($image['id']);
        $im->histogram_lightness = json_encode($histogramLightness);
        $im->histogram_hue = json_encode($histogramHue);
        $im->histogram_saturation = json_encode($histogramSaturation);
        $im->save();
//        imagejpeg($imOut, '/var/www/laravel/out/' . $image['filename']);
        print $image['dir'] . '/' . $image['filename'] . " ($cntPixelsCalculated pixels) done\n";
    }

    public function getMask($image)
    {

    }

    // functions from https://gist.github.com/brandonheyer/5254516
    protected function rgbToHsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        if ($d == 0) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / (1 - abs(2 * $l - 1));
            switch ($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * (($b - $r) / $d + 2);
                    break;
                case $b:
                    $h = 60 * (($r - $g) / $d + 4);
                    break;
            }
        }
        return array(round($h), round($s * 100), round($l * 100));
    }

    protected function hslToRgb($h, $s, $l)
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod(($h / 60), 2) - 1));
        $m = $l - ($c / 2);
        if ($h < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ($h < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ($h < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ($h < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ($h < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }
        $r = ($r + $m) * 255;
        $g = ($g + $m) * 255;
        $b = ($b + $m) * 255;
        return array(floor($r), floor($g), floor($b));
    }
}