import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(20,GPIO.OUT)
status = GPIO.input(20)
if status == 1:
  print "Klasicke cerpadlo vypnute"
else:
  print "Klasicke cerpadlo zapnute"