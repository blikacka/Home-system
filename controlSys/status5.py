import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(5,GPIO.OUT)
status = GPIO.input(5)
if status == 1:
  print "Rele 5 vypnute"
else:
  print "Rele 5 zapnute"