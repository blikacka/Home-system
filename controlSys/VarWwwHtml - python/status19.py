import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(19,GPIO.OUT)
status = GPIO.input(19)
if status == 1:
  print "Elektrokotel vypnute"
else:
  print "Elektrokotel zapnute"