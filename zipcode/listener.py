import socket
import TTScode

serversocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
serversocket.bind(('192.168.137.106', 8089))
serversocket.listen(5) # become a server socket, maximum 5 connections

while True:
    connection, address = serversocket.accept()
    buf = connection.recv(4096)
    if len(buf) > 0:
        TTScode.toSpeach(str(buf))