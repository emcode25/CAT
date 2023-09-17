import json
import requests
import cv2
import time

happy_cat = cv2.imread("happycat.png", cv2.IMREAD_ANYCOLOR)
sad_cat = cv2.imread("sadcat.png", cv2.IMREAD_ANYCOLOR) 

while True:
    # Get data from 192.168.137.1/status.php
    data = requests.get("http://192.168.137.1/cat/status.php?uid=kyle")
    print(data.text)

    # Parse JSON data into relevant parts
    watch_info = json.loads(data.text)

    # Show cat face based on data
    total = int(watch_info["voc"]) + int(watch_info["co2"]) + int(watch_info["fall"]) + int(watch_info["hrStatus"])
    print(total)
    if total > 0:
        cv2.imshow("CAT", sad_cat)
    else:
        cv2.imshow("CAT", happy_cat)

    cv2.waitKey(250)
