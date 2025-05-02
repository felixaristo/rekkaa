<?php
namespace App\Libraries;

class AppDurationLibrary {
    public static function fromString($string) {
        $parts = explode(':', $string);
        $object = new self();
        if (count($parts) === 2) {
            $object->minutes = $parts[0];
            $object->seconds = $parts[1];
        } elseif (count($parts) === 3) {
            $object->hours = $parts[0];
            $object->minutes = $parts[1];
            $object->seconds = $parts[2];
        } else {
            // handle error
        }
        return $object;
    }

    private $hours;
    private $minutes;
    private $seconds;

    public function getHours() {
        return $this->hours;
    }

    public function getMinutes() {
        return $this->minutes;
    }

    public function getSeconds() {
        return $this->seconds;
    }

    public function add(AppDurationLibrary $d) {
        $this->hours += $d->hours;
        $this->minutes += $d->minutes;
        $this->seconds += $d->seconds;
        while ($this->seconds >= 60) {
            $this->seconds -= 60;
            $this->minutes++;
        }
        while ($this->minutes >= 60) {
            $this->minutes -= 60;
            $this->hours++;
        }
    }

    public function toMinutes() {
        return ($this->hours*60) + ($this->minutes) + ($this->seconds/60);
    }

    public function __toString() {
        return implode(':', array($this->hours, $this->minutes, $this->seconds));
    }

}