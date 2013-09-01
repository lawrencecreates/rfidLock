#!/bin/bash
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

gpio -g mode 24 out
gpio -g mode 7 out
gpio -g mode 8 out
gpio -g mode 25 out

gpio -g write 7 0
gpio -g write 8 1
gpio -g write 24 1
gpio -g write 25 1
sleep 0.01
gpio -g write 25 0

sleep 3
gpio -g write 24 0 
gpio -g write 7 1
gpio -g write 8 0


