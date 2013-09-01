#!/bin/bash

gpio -g mode 24 out #relay
gpio -g mode 7 out  #red
gpio -g mode 8 out  #green
gpio -g mode 25 out #buzzer

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

