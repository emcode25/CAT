import socket

clientsocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
clientsocket.connect(('192.168.137.106', 8089))
clientsocket.send(bytes('hello','UTF-8'))
