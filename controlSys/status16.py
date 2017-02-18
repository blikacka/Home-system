import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(16,GPIO.OUT)
status = GPIO.input(16)
if status == 1:
  print "Podlahove cerpadlo vypnute"
else:
  print "Podlahove cerpadlo zapnute"