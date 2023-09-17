#include <Arduino.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#include <Adafruit_APDS9960.h>
#include <Adafruit_SGP30.h>
#include <Adafruit_MPU6050.h>
#include <WiFi.h>
#include <WiFiMulti.h>
#include <HTTPClient.h>

//Fall constants
constexpr float FALL_THRESHOLD = 13.0f;
constexpr float FALL_SQUARED = FALL_THRESHOLD * FALL_THRESHOLD;

//Sensors
Adafruit_BME280 bme;
Adafruit_APDS9960 apds;
Adafruit_SGP30 sgp;
Adafruit_MPU6050 mpu;
Adafruit_MPU6050_Accelerometer acc(&mpu);
sensors_event_t event; 
WiFiMulti wifi;

//Communcation details
const char* ssid = "postnet";
const char* password = "postNetPassword";
String url = "http://192.168.137.247/datafwd.php/";
String UID = "kyle";

//Talk button GPIO pin
int buttonInput = 12;

void setup() 
{
  //Set up the baud rate
  Serial.begin(115200);

  delay(5000);

  pinMode(buttonInput, INPUT);

  //initialize sensors
  bme.begin();
  apds.begin();
  apds.enableColor(true);
  sgp.begin();
  mpu.begin();

  //add wifi connection
  wifi.addAP(ssid, password);
}

void loop() 
{
  
  //execute when wifi connected
  if((wifi.run() == WL_CONNECTED))
  {

    Serial.println("Connected!");

    //collect sensor data  
    int buttonPress = digitalRead(buttonInput);

    //Temperature data
    float temp = bme.readTemperature()*9.0F / 5.0F + 32; // in F

    //Error checking
    if(!acc.getEvent(&event))
    {
      Serial.println("Could not get accelerometer data.");
    }
    
    if(!sgp.IAQmeasure())
    {
      Serial.println("Air Quality Measurement failed");
    }

    //Air quality data
    uint16_t voc = sgp.TVOC;
    uint16_t co2 = sgp.eCO2;

    //Accelerometer data
    float ax = event.acceleration.x;
    float ay = event.acceleration.y;
    float az = event.acceleration.z;
    //Check to see if a fall has occurred
    int fall = (ax * ax + ay * ay + az * az) > FALL_SQUARED;

    //Prepare the paylaod with all relevant data
    HTTPClient http;
    String payload = url + "?temp=" + String(temp) + "&voc=" + String(voc) + 
      "&co2=" + String(co2) + "&fall=" + String(fall) + 
      "&talk=" + String(buttonPress) + "&hr=80" + "&uid=" + String(UID);
    
    //Send the payload
    http.begin(payload);
    int response = http.GET();

    Serial.println(payload);
    Serial.println(response);
  }
  
  delay(300);
  
}
