<?php 

    # RFID Lock Project 
    # Copyright (C) 2013  Ben Barker

    # This program is free software: you can redistribute it and/or modify
    # it under the terms of the GNU Affero General Public License as published by
    # the Free Software Foundation, either version 3 of the License, or
    # (at your option) any later version.

    # This program is distributed in the hope that it will be useful,
    # but WITHOUT ANY WARRANTY; without even the implied warranty of
    # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    # GNU Affero General Public License for more details.

    # You should have received a copy of the GNU Affero General Public License
    # along with this program.  If not, see <http://www.gnu.org/licenses/>.

	class GPIO {

		// Using BCM pin numbers.
		private $pins = ['0', '1', '4', '7', '8', '9', '10', '11', '14', '15', '17', '18', '21', '22', '23', '24', '25'];

		// exported pins for when we unexport all
		private $exportedPins = array();

		// Setup pin, takes pin number and direction (in or out)
		public function setup($pinNo, $direction) {
			// Check if valid BCM number
			if($this->isValidPin($pinNo)) {
				// if exported, unexport it first
				if($this->isExported($pinNo)) {
					$this->unexport($pinNo);
				}

				// Export pin
				file_put_contents('/sys/class/gpio/export', $pinNo);

				// if valid direction then set direction
				if($this->isValidDirection($direction)) {
					file_put_contents('/sys/class/gpio/gpio'.$pinNo.'/direction', $direction);
				}

				// Add to exported pins array
				$exportedPins[] = $pinNo;
			} else {
				echo 'Error! Not a valid pin!';
			}
		}

		public function input($pinNo) {
			if($this->isExported($pinNo)) {
				if($this->currentDirection($pinNo) != "out") {
					return file_get_contents('/sys/class/gpio/gpio'.$pinNo.'/value');
				} else {
					echo 'Error! Wrong Direction for this pin!';
				}
			}
		}

		// Value == 1 or 0, where 1 = on, 0 = off
		public function output($pinNo, $value) {
			if($this->isExported($pinNo)) {
				if($this->currentDirection($pinNo) != "in") {
					file_put_contents('/sys/class/gpio/gpio'.$pinNo.'/value', $value);
				} else {
					echo 'Error! Wrong Direction for this pin! Meant to be out while it is ' . $this->currentDirection($pinNo);
				}
			}
		}

		public function unexport($pinNo) {
			if($this->isExported($pinNo)) {
				file_put_contents('/sys/class/gpio/unexport', $pinNo);
				foreach ($this->exportedPins as $key => $value) {
					if($value == $pinNo) unset($key);
				}
			}
		}

		public function unexportAll() {
			foreach ($this->exportedPins as $key => $pinNo) file_put_contents('/sys/class/gpio/unexport', $pinNo);
			$this->exportedPins = array();
		}

		// Check if exported
		public function isExported($pinNo) {
			return file_exists('/sys/class/gpio/gpio'.$pinNo);
		}

		public function currentDirection($pinNo) {
			return file_get_contents('/sys/class/gpio/gpio'.$pinNo.'/direction');
		}

		// Check for valid direction, in or out
		public function isValidDirection($direction) {
			return (($direction == "in") || ($direction == "out"));
		}

		// Check for valid pin
		public function isValidPin($pinNo) {
			return in_array($pinNo, $this->pins);
		}
	}
?>
