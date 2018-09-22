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
        print "Creating locations...\n";
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
        print "Creating cameras...\n";

        $locations = Location::all();
        foreach ($locations as $location) {
            $dir = $this->rootDir . '/' . $location->name;
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
}