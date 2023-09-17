import json
from urllib.request import urlopen

def fall_stat(url):
    response = urlopen(url)
    data = json.loads(response.read())
    status_report = []

    if data['fall']==1:
        status_report.append('A fall has been detected')
    elif data['fall']==0:
        status_report.append('No falls detected')

        return status_report