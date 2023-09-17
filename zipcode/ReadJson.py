import json
from urllib.request import urlopen

def status(url):

    response = urlopen(url)
    data = json.loads(response.read())
    bads = []
    goods = []
    status_report = []

    for each in data:
        if data[each] == 1:
            bads.append(each)
        elif data[each] == 0:
            goods.append(each)
    
    badCount = len(bads)
    goodCount = len(goods)

    if "fall" in bads:
        status_report.append('A fall has been detected')
    elif "hrStatus" in bads:
        status_report.append('A dangerous Heart Rate has been detected')
    elif "temp" in bads:
        status_report.append('A dangerous temperature has been detected')
    elif "voc" in bads:
        status_report.append("A dangerous Air Quality level has been detected")
    elif "co2" in bads:
        status_report.append("A dangerous CO2 level has been detected")
    elif len(bads) == 0:
        status_report.append('No issues detected!')

    if "fall" in goods:
        status_report.append('No falls detected')
    elif "hrStatus" in goods:
        status_report.append('Heart Rate is normal')
    elif "temp" in goods:
        status_report.append('Temperatures are normal')
    elif "voc" in goods:
        status_report.append("Air Quality levels are normal")
    elif "co2" in goods:
        status_report.append("Co2 levels are normal")
    elif len(goods) == 0:
        status_report.append('EMERGENCY DETECTED!')
    
    finalstring = ''
    for s in status_report:
        finalstring += s+' '

    return finalstring
