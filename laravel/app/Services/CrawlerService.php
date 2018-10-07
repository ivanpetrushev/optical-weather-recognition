<?php

namespace App\Services;

use App\Camera;
use App\Location;
use App\Image;
use DB;
use Mockery\Exception;

class CrawlerService
{
    protected $rootDir = '';

    public function setRootDir($dir)
    {
        $this->rootDir = $dir;
    }

    public function createLocations()
    {
        print "Creating LOCATIONS...\n";
        if (empty($this->rootDir)) {
            throw new Exception('No rootDir defined');
        }

        foreach (new \DirectoryIterator($this->rootDir) as $fileDir) {
            if ($fileDir->isDir() && !$fileDir->isDot()) {
                $basename = $fileDir->getBasename();
                print "Checking $basename... ";

                $location = Location::where('name', $basename)->first();
                if ($location) {
                    print "found.\n";
                } else {
                    $location = new Location();
                    $location->name = $basename;
                    $location->save();
                    print "added!\n";
                }
            }
        }
    }

    public function createCameras()
    {
        print "Creating CAMEREAS...\n";

        $locations = Location::all();
        foreach ($locations as $location) {
            $dir = $this->rootDir . '/' . $location->name;
            if (! is_dir($dir)) {
                print "Directory $dir is missing!\n";
                continue;
            }
            foreach (new \DirectoryIterator($dir) as $fileDir) {
                if ($fileDir->isDir() && !$fileDir->isDot()) {
                    $basename = $fileDir->getBasename();
                    print "Checking $basename... ";

                    $camera = Camera::where(['name' => $basename, 'location_id' => $location->id])->first();
                    if ($camera) {
                        print "found.\n";
                    } else {
                        $camera = new Camera();
                        $camera->name = $basename;
                        $camera->location_id = $location->id;
                        $camera->save();
                        print "added!\n";
                    }
                }
            }
        }
    }

    public function createImages()
    {
        print "Creating images...\n";

        $locations = Location::all();
        foreach ($locations as $location) {
            $cameras = Camera::where('location_id', $location->id)->get();
            foreach ($cameras as $camera) {
                $dir = $this->rootDir . '/' . $location->name . '/' . $camera->name;
                if (! is_dir($dir)) {
                    print "Directory $dir is missing!\n";
                    continue;
                }
                foreach (new \DirectoryIterator($dir) as $fileDir) {
                    if ($fileDir->isDir() && !$fileDir->isDot()) {
                        $date = $fileDir->getBasename();
                        print "LOC: {$location->name} CAM: {$camera->name} DATE: $date ";

                        $dateDir = $dir . '/' . $date;
                        foreach (new \DirectoryIterator($dateDir) as $imageDir) {
                            if ($imageDir->isFile()) {
                                $filename = $imageDir->getBasename();
                                // $filename = '2018-09-18_21:20:01.jpg';
                                $pathinfo = pathinfo($dateDir . '/' . $filename);
                                $name_parts = explode('_', $pathinfo['filename']);
                                $time = $name_parts[1];

                                $image = Image::where([
                                    'location_id' => $location->id,
                                    'camera_id' => $camera->id,
                                    'filename' => $filename
                                ])->first();
                                if ($image) {
                                    print '.';
                                } else {
                                    $image = new Image();
                                    $image->location_id = $location->id;
                                    $image->camera_id = $camera->id;
                                    $image->filename = $filename;
                                    $image->dir = $dateDir;
                                    $image->taken_date = $date;
                                    $image->taken_time = $time;
                                    $image->save();
                                    print '+';
                                }
                            }
                        }
                        print "\n";
                    }
                }
            }
        }
    }
}