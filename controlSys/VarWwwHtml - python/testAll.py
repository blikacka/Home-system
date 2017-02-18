#!/usr/bin/env python
import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(16,GPIO.OUT)
GPIO.setup(20,GPIO.OUT)
GPIO.setup(21,GPIO.OUT)
GPIO.setup(5,GPIO.OUT)
GPIO.setup(6,GPIO.OUT)
GPIO.setup(13,GPIO.OUT)
GPIO.setup(19,GPIO.OUT)
GPIO.setup(26,GPIO.OUT)
while True:
        print "LED on"
        GPIO.output(16,GPIO.HIGH)
        GPIO.output(20,GPIO.HIGH)
        GPIO.output(21,GPIO.HIGH)
        GPIO.output(5,GPIO.HIGH)
        GPIO.output(6,GPIO.HIGH)
        GPIO.output(13,GPIO.HIGH)
        GPIO.output(19,GPIO.HIGH)
        GPIO.output(26,GPIO.HIGH)
        time.sleep(10.0 / 100.0)
        print "LED off"
        GPIO.output(16,GPIO.LOW)
        GPIO.output(20,GPIO.LOW)
        GPIO.output(21,GPIO.LOW)
        GPIO.output(5,GPIO.LOW)
        GPIO.output(6,GPIO.LOW)
        GPIO.output(13,GPIO.LOW)
        GPIO.output(19,GPIO.LOW)
        GPIO.output(26,GPIO.LOW)
        time.sleep(10.0 / 100.0)
