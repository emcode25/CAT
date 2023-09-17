import openai
import pyaudio
import wave
from datetime import datetime
import ReadJson
import mysql.connector
import socket
import falldetection

keyfile = open("openaiKey.txt", "r")
key = keyfile.read()
openai.api_key = key

log = [{"role": "system", "content": "Your name is Computer Assited Therapy, or CAT" + ReadJson.status('http://192.168.137.1/cat/status.php?uid=kyle')}]
CHUNK = 1024
FORMAT = pyaudio.paInt16
CHANNELS = 1
RATE = 44100

def record():

    filename = ''.join([datetime.utcnow().strftime('%H%M%S%Y%m%d'), '.wav'])

    p = pyaudio.PyAudio()
    stream = p.open(format=FORMAT,
                channels=CHANNELS,
                rate=RATE,
                input=True,
                frames_per_buffer=CHUNK)
    print('mic is HOT')
    frames = []
    seconds = 5
    for i in range(0,int(RATE / CHUNK * seconds)):
        data = stream.read(CHUNK)
        frames.append(data)
    stream.stop_stream()
    stream.close()
    p.terminate()
    wf = wave.open(filename, 'wb')
    wf.setnchannels(CHANNELS)
    wf.setsampwidth(p.get_sample_size(FORMAT))
    wf.setframerate(RATE)
    wf.writeframes(b''.join(frames))
    wf.close()
    print('recording saved')
    return filename
def whisper(filename):
    media_file = open(filename, 'rb')

    response = openai.Audio.transcribe(
        api_key=key,
        model='whisper-1',
        file=media_file,
    )
    return response['text']
def talk_status():
    mydb = mysql.connector.connect(
        host="localhost",
        user="cat",
        password="catpass",
        database="cat"
    )
    
    mycursor = mydb.cursor()

    mycursor.execute("SELECT talk FROM talkstatus WHERE id = 0")

    myresult = mycursor.fetchall()

    talkstat = myresult[0][0]

    return talkstat
def clientSend(msg):
    clientsocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    clientsocket.connect(('192.168.137.106', 8089))
    clientsocket.send(bytes(msg,'UTF-8'))
def convo():
    while talk_status() == 1:
        print(log)
        usrmsg = whisper(record())
        if "quit" in usrmsg:
            break
        else:
            log.append({"role": "user", "content": usrmsg})
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo",
                messages=log,
            )
            chatrespond = response['choices'][0]['message']['content']
            clientSend(chatrespond.strip("\n").strip())
            print("?"+chatrespond.strip("\n").strip()+"?")
            log.append({"role": "assistant", "content": chatrespond.strip("\n").strip()})
            clientSend(str(falldetection.fall_stat('http://192.168.137.1/cat/status.php?uid=kyle')))

while True:
    convo()



