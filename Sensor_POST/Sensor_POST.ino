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

Adafruit_BME280 bme;
Adafruit_APDS9960 apds;
Adafruit_SGP30 sgp;
Adafruit_MPU6050 mpu;
Adafruit_MPU6050_Accelerometer acc(&mpu);
sensors_event_t event; 
WiFiMulti wifi;

const char* ssid = "postnet";
const char* password = "postNetPassword";
String url = "http://192.168.137.247/";

void setup() 
{
  Serial.begin(115200);

  delay(5000);

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

    //weather data
    float temp = bme.readTemperature()*9.0F / 5.0F + 32; // in F
    float pressure = bme.readPressure() * 0.0009869233 * 1000 / 100.0F; //in milliatmosphere
    float hum = bme.readHumidity(); //in percent
    if(!acc.getEvent(&event))
    {
      Serial.println("Could not get accelerometer data.");
    } //in ??

    //light level data
    uint16_t r, g ,b, c;
    apds.getColorData(&r, &g, &b, &c);
    while(!apds.colorDataReady())
    {
      delay(5);
    }
    uint16_t lux = apds.calculateLux(r, g, b) * 100;

    
    if(!sgp.IAQmeasure())
    {
      Serial.println("Measurement failed");
    }

    //air quality data
    uint16_t voc = sgp.TVOC;
    uint16_t co2 = sgp.eCO2;

    //Accelerometer data
    float ax = event.acceleration.x;
    float ay = event.acceleration.y;
    float az = event.acceleration.z;

    //http send
    HTTPClient http;
    String payload = url + "&temp=" + String(temp) + "&humidity=" + 
      String(lux) + "&voc=" + String(voc) + "&co2=" + String(co2) +
      "&ax=" + String(ax) + "&ay=" + String(ay) + "&az=" + String(az);
    
    http.begin(payload);
    int response = http.GET();

    Serial.println(payload);
    Serial.println(response);
  }
  
  delay(500);
  
}
